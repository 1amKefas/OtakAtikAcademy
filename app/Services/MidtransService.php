<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        // Setup Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create Snap transaction
     */
    public function createTransaction(array $transactionDetails)
    {
        try {
            $snapToken = Snap::getSnapToken($transactionDetails);
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Simulate payment success (for development)
     */
    public function simulatePayment($orderId, $status = 'success')
    {
        // In real implementation, this would be webhook from Midtrans
        // For now, we simulate based on status
        
        $simulatedResponse = [
            'transaction_status' => $status === 'success' ? 'capture' : 'deny',
            'order_id' => $orderId,
            'gross_amount' => '100000',
            'payment_type' => 'bank_transfer',
            'transaction_time' => now()->toDateTimeString(),
        ];

        return $simulatedResponse;
    }

    /**
     * Handle payment notification (webhook)
     */
    public function handleNotification($notification)
    {
        // This would handle real Midtrans notification
        // For simulation, we'll use direct method calls
        return [
            'status' => 'ok',
            'message' => 'Notification handled'
        ];
    }
}