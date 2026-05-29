<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Show payment method selection page
     */
    public function selectMethod($subscriptionId)
    {
        $subscription = Subscription::with('plan')->findOrFail($subscriptionId);
        
        // Verify tenant owns this subscription
        if ($subscription->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        // Check if payment already completed
        if ($subscription->payment_status === 'paid') {
            return redirect()->route('tenant.subscriptions.current')
                ->with('info', 'Payment already completed for this subscription.');
        }

        return view('payments.select_method', compact('subscription'));
    }

    /**
     * Initiate Razorpay payment
     */
    public function initiateRazorpay(Request $request, $subscriptionId)
    {
        $request->validate([
            'coupon_code' => 'nullable|string',
        ]);

        $subscription = Subscription::with('plan')->findOrFail($subscriptionId);
        
        if ($subscription->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        if (!$this->razorpayService->isConfigured()) {
            return back()->with('error', 'Razorpay is not configured. Please contact support.');
        }

        // Calculate final amount
        $amount = $subscription->final_price ?? $subscription->plan->price;

        // Create Razorpay order
        try {
            $order = $this->razorpayService->createOrder([
                'amount' => $amount,
                'receipt' => 'sub_' . $subscription->id,
                'notes' => [
                    'subscription_id' => $subscription->id,
                    'tenant_id' => $subscription->tenant_id,
                    'user_id' => Auth::id(),
                ],
            ]);

            // Create payment record
            $payment = Payment::create([
                'subscription_id' => $subscription->id,
                'tenant_id' => $subscription->tenant_id,
                'payment_method' => 'razorpay',
                'amount' => $amount,
                'currency' => 'INR',
                'payment_status' => 'pending',
                'razorpay_order_id' => $order['id'],
                'transaction_id' => $order['id'],
            ]);

            return view('payments.razorpay', [
                'subscription' => $subscription,
                'payment' => $payment,
                'order' => $order,
                'keyId' => $this->razorpayService->getKeyId(),
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Verify Razorpay payment
     */
    public function verifyRazorpay(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        try {
            // Verify signature
            if (!$this->razorpayService->verifyPayment($request->all())) {
                return back()->with('error', 'Payment verification failed. Invalid signature.');
            }

            // Find payment record
            $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();

            // Update payment record
            $payment->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'payment_status' => 'processing',
            ]);

            // Mark as completed
            $payment->markAsCompleted($request->razorpay_payment_id);

            return redirect()->route('tenant.subscriptions.current')
                ->with('success', 'Payment completed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Show UPI QR payment page
     */
    public function showUpiQr($subscriptionId)
    {
        $subscription = Subscription::with('plan')->findOrFail($subscriptionId);
        
        if ($subscription->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $amount = $subscription->final_price ?? $subscription->plan->price;
        $upiId = env('UPI_VPA');
        $merchantName = env('UPI_MERCHANT_NAME', 'BT Guru');

        // Generate UPI QR code URL (using a free QR API)
        $upiString = "upi://pay?pa={$upiId}&pn={$merchantName}&am={$amount}&cu=INR";
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upiString);

        return view('payments.upi_qr', compact('subscription', 'amount', 'upiId', 'merchantName', 'qrCodeUrl'));
    }

    /**
     * Submit UPI payment with screenshot
     */
    public function submitUpiPayment(Request $request, $subscriptionId)
    {
        $request->validate([
            'upi_transaction_id' => 'required|string',
            'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        $subscription = Subscription::findOrFail($subscriptionId);
        
        if ($subscription->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $amount = $subscription->final_price ?? $subscription->plan->price;

        // Upload screenshot
        $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'public');

        // Create payment record
        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'payment_method' => 'upi_qr',
            'amount' => $amount,
            'currency' => 'INR',
            'payment_status' => 'processing',
            'transaction_id' => $request->upi_transaction_id,
            'upi_transaction_id' => $request->upi_transaction_id,
            'upi_id' => env('UPI_VPA'),
            'screenshot_path' => $screenshotPath,
            'notes' => $request->notes,
        ]);

        return redirect()->route('tenant.subscriptions.current')
            ->with('success', 'Payment submitted for verification. We will confirm your payment shortly.');
    }

    /**
     * Show manual payment page
     */
    public function showManualPayment($subscriptionId)
    {
        $subscription = Subscription::with('plan')->findOrFail($subscriptionId);
        
        if ($subscription->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $amount = $subscription->final_price ?? $subscription->plan->price;

        return view('payments.manual', compact('subscription', 'amount'));
    }

    /**
     * Submit manual payment
     */
    public function submitManualPayment(Request $request, $subscriptionId)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'payment_date' => 'required|date',
            'bank_name' => 'required|string',
            'screenshot' => 'required|image|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string',
        ]);

        $subscription = Subscription::findOrFail($subscriptionId);
        
        if ($subscription->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $amount = $subscription->final_price ?? $subscription->plan->price;

        // Upload screenshot
        $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'public');

        // Create payment record
        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'payment_method' => 'manual',
            'amount' => $amount,
            'currency' => 'INR',
            'payment_status' => 'processing',
            'transaction_id' => $request->transaction_id,
            'screenshot_path' => $screenshotPath,
            'notes' => "Bank: {$request->bank_name}, Date: {$request->payment_date}. " . ($request->notes ?? ''),
        ]);

        return redirect()->route('tenant.subscriptions.current')
            ->with('success', 'Payment submitted for verification. We will confirm your payment shortly.');
    }

    /**
     * Admin: Index all payments
     */
    public function index()
    {
        $payments = Payment::with(['subscription.tenant', 'subscription.plan'])
            ->latest()
            ->get();

        $stats = [
            'total' => $payments->count(),
            'pending' => $payments->where('payment_status', 'pending')->count(),
            'processing' => $payments->where('payment_status', 'processing')->count(),
            'completed' => $payments->where('payment_status', 'completed')->count(),
            'failed' => $payments->where('payment_status', 'failed')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Admin: Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['subscription.tenant', 'subscription.plan']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Admin: Verify pending payment
     */
    public function verifyPayment(Request $request, Payment $payment)
    {
        \Log::info('verifyPayment called', [
            'payment_id' => $payment->id,
            'action' => $request->action,
            'current_status' => $payment->payment_status,
        ]);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'required_if:action,reject|string',
        ]);

        try {
            if ($request->action === 'approve') {
                $payment->load('subscription.plan');
                $payment->markAsCompleted();
                \Log::info('Payment approved', ['payment_id' => $payment->id]);
                return back()->with('success', 'Payment approved and subscription activated.');
            } else {
                $payment->markAsFailed($request->reason);
                \Log::info('Payment rejected', ['payment_id' => $payment->id]);
                return back()->with('success', 'Payment rejected.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }
}
