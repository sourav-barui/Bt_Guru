<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\PaymentRequest;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Display the book store for students
     */
    public function index()
    {
        $student = Auth::user();
        $tenant = app('current_tenant');

        $books = Book::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        // Get student's purchased books
        $purchasedBookIds = BookOrder::where('student_id', $student->id)
            ->where('payment_status', 'completed')
            ->whereIn('order_type', ['pdf', 'both'])
            ->pluck('book_id')
            ->toArray();

        return view('student.books.index', compact('books', 'purchasedBookIds'));
    }

    /**
     * Show book details
     */
    public function show(Book $book)
    {
        $student = Auth::user();
        $tenant = app('current_tenant');

        // Verify book belongs to tenant
        if ($book->tenant_id !== $tenant->id || $book->status !== 'active') {
            abort(404);
        }

        // Check if student has purchased this book
        $order = BookOrder::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->where('payment_status', 'completed')
            ->first();

        $canDownload = $order && $order->canDownload();

        return view('student.books.show', compact('book', 'order', 'canDownload'));
    }

    /**
     * Show checkout page for buying a book
     */
    public function checkout(Request $request, Book $book)
    {
        $student = Auth::user();
        $tenant = app('current_tenant');

        if ($book->tenant_id !== $tenant->id || $book->status !== 'active') {
            abort(404);
        }

        $request->validate([
            'type' => 'required|in:pdf,physical,both',
        ]);

        $orderType = $request->type;

        // Validate book type supports the selected order type
        if ($orderType === 'pdf' && !$book->isPdf()) {
            return back()->with('error', 'This book is not available in PDF format.');
        }
        if ($orderType === 'physical' && !$book->isPhysical()) {
            return back()->with('error', 'This book is not available in physical format.');
        }
        if ($orderType === 'both' && !$book->isBoth()) {
            return back()->with('error', 'This book is not available in both formats.');
        }

        // Check if already purchased (for PDF)
        if (in_array($orderType, ['pdf', 'both'])) {
            $existingOrder = BookOrder::where('student_id', $student->id)
                ->where('book_id', $book->id)
                ->where('payment_status', 'completed')
                ->whereIn('order_type', ['pdf', 'both'])
                ->first();

            if ($existingOrder) {
                return redirect()->route('student.books.show', $book)
                    ->with('info', 'You have already purchased this book. You can download it from the book details page.');
            }
        }

        // Check stock for physical book
        if (in_array($orderType, ['physical', 'both']) && !$book->isInStock()) {
            return back()->with('error', 'This book is currently out of stock.');
        }

        // Calculate price
        $pdfPrice = in_array($orderType, ['pdf', 'both']) ? $book->pdf_price : 0;
        $physicalPrice = in_array($orderType, ['physical', 'both']) ? $book->physical_price : 0;
        $totalAmount = $pdfPrice + $physicalPrice;

        return view('student.books.checkout', compact('book', 'orderType', 'pdfPrice', 'physicalPrice', 'totalAmount'));
    }

    /**
     * Process book purchase — creates a PaymentRequest like course payments
     */
    public function purchase(Request $request, Book $book)
    {
        $student = Auth::user();
        $tenant = app('current_tenant');

        if ($book->tenant_id !== $tenant->id || $book->status !== 'active') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'order_type' => 'required|in:pdf,physical,both',
            'reference_number' => 'nullable|string|max:100',
            'screenshot' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $orderType = $request->order_type;

        // Validate book type
        if ($orderType === 'pdf' && !$book->isPdf()) {
            return back()->with('error', 'This book is not available in PDF format.');
        }
        if ($orderType === 'physical' && !$book->isPhysical()) {
            return back()->with('error', 'This book is not available in physical format.');
        }

        // Check stock for physical book
        if (in_array($orderType, ['physical', 'both']) && !$book->isInStock()) {
            return back()->with('error', 'This book is currently out of stock.');
        }

        // Calculate price
        $pdfPrice = in_array($orderType, ['pdf', 'both']) ? $book->pdf_price : 0;
        $physicalPrice = in_array($orderType, ['physical', 'both']) ? $book->physical_price : 0;
        $totalAmount = $pdfPrice + $physicalPrice;

        // Check if already purchased (for PDF)
        if (in_array($orderType, ['pdf', 'both'])) {
            $existingOrder = BookOrder::where('student_id', $student->id)
                ->where('book_id', $book->id)
                ->where('payment_status', 'completed')
                ->whereIn('order_type', ['pdf', 'both'])
                ->first();

            if ($existingOrder) {
                return redirect()->route('student.books.show', $book)
                    ->with('info', 'You have already purchased this book.');
            }
        }

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'public');
        }

        // Build note with book/order details
        $note = $request->note;
        $note = ($note ? $note . "\n" : '') . "Book: {$book->title}\nOrder Type: " . ucfirst($orderType);
        if (in_array($orderType, ['physical', 'both']) && $request->delivery_address) {
            $note .= "\nDelivery Address: " . $request->delivery_address;
        }
        if (in_array($orderType, ['physical', 'both']) && $request->delivery_phone) {
            $note .= "\nContact: " . $request->delivery_phone;
        }

        PaymentRequest::create([
            'tenant_id'        => $tenant->id,
            'student_id'       => $student->id,
            'book_id'          => $book->id,
            'payment_type'     => 'book_purchase',
            'amount'           => $totalAmount,
            'reference_number' => $request->reference_number,
            'screenshot'       => $screenshotPath,
            'note'             => $note,
            'status'           => 'pending',
            'metadata'         => [
                'order_type' => $orderType,
                'pdf_price' => $pdfPrice,
                'physical_price' => $physicalPrice,
                'delivery_address' => $request->delivery_address,
                'delivery_phone' => $request->delivery_phone,
            ],
        ]);

        return redirect()->route('student.payments.index')
            ->with('success', 'Book purchase request submitted successfully. Admin will verify and approve it shortly.');
    }

    /**
     * Download PDF book
     */
    public function download(BookOrder $order)
    {
        $student = Auth::user();

        // Verify order belongs to student
        if ($order->student_id !== $student->id) {
            abort(403);
        }

        // Verify order is completed and has PDF
        if (!$order->canDownload()) {
            return back()->with('error', 'You cannot download this book. Please complete the payment first.');
        }

        $book = $order->book;

        if (!$book->pdf_file || !Storage::disk('public')->exists($book->pdf_file)) {
            return back()->with('error', 'PDF file not found.');
        }

        return Storage::disk('public')->download($book->pdf_file, $book->title . '.pdf');
    }

    /**
     * Show student's book orders
     */
    public function myOrders()
    {
        $student = Auth::user();
        $tenant = app('current_tenant');

        $orders = BookOrder::where('student_id', $student->id)
            ->where('tenant_id', $tenant->id)
            ->with('book')
            ->latest()
            ->paginate(10);

        return view('student.books.my_orders', compact('orders'));
    }
}
