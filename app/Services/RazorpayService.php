<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected $keyId;
    protected $keySecret;
    protected $currency;

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key_id') ?? env('RAZORPAY_KEY_ID');
        $this->keySecret = config('services.razorpay.key_secret') ?? env('RAZORPAY_KEY_SECRET');
        $this->currency = config('services.razorpay.currency') ?? env('RAZORPAY_CURRENCY', 'INR');
    }

    /**
     * Create a Razorpay order
     */
    public function createOrder(array $data): array
    {
        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $data['amount'] * 100, // Convert to paise
                    'currency' => $this->currency,
                    'receipt' => $data['receipt'] ?? null,
                    'notes' => $data['notes'] ?? [],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay order creation failed', [
                'response' => $response->body(),
                'data' => $data,
            ]);

            throw new \Exception('Failed to create Razorpay order');
        } catch (\Exception $e) {
            Log::error('Razorpay API error', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Verify Razorpay payment signature
     */
    public function verifyPayment(array $data): bool
    {
        $orderId = $data['razorpay_order_id'];
        $paymentId = $data['razorpay_payment_id'];
        $signature = $data['razorpay_signature'];

        $generatedSignature = hash_hmac(
            'sha256',
            $orderId . '|' . $paymentId,
            $this->keySecret
        );

        return hash_equals($generatedSignature, $signature);
    }

    /**
     * Fetch payment details from Razorpay
     */
    public function fetchPayment(string $paymentId): array
    {
        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->get("https://api.razorpay.com/v1/payments/{$paymentId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay payment fetch failed', [
                'payment_id' => $paymentId,
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to fetch payment details');
        } catch (\Exception $e) {
            Log::error('Razorpay API error', [
                'message' => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
            throw $e;
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentId, float $amount = null): array
    {
        try {
            $data = [];
            if ($amount) {
                $data['amount'] = $amount * 100; // Convert to paise
            }

            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->post("https://api.razorpay.com/v1/payments/{$paymentId}/refund", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay refund failed', [
                'payment_id' => $paymentId,
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to process refund');
        } catch (\Exception $e) {
            Log::error('Razorpay API error', [
                'message' => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
            throw $e;
        }
    }

    /**
     * Get Razorpay key ID for frontend
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * Check if Razorpay is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->keyId) && !empty($this->keySecret);
    }
}
