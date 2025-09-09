<?php

namespace App\Services\Payment\Strategies;

use App\Services\Payment\PaymentStrategyInterface;
use App\Models\Payment;

class PayPalPaymentStrategy implements PaymentStrategyInterface
{
    public function processPayment(array $paymentData): array
    {
        $paymentDetails = $paymentData['payment_details'];
        $amount = $paymentData['amount'];
        $appointmentId = $paymentData['appointment_id'];

        try {
            // Create payment record in database
            $payment = Payment::create([
                'appointment_id' => $appointmentId,
                'amount' => $amount,
                'currency' => $paymentData['currency'] ?? 'MYR',
                'method' => Payment::METHOD_EWALLET, // PayPal is considered e-wallet
                'status' => Payment::STATUS_PENDING,
                'payment_details' => $paymentDetails,
            ]);

            $paypalEmail = $paymentDetails['paypal_email'];
            
            // Validate email format
            if (!filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) {
                $payment->markAsFailed('Invalid PayPal email address');
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Invalid PayPal email address'
                ];
            }

            // Simulate PayPal API call delay
            usleep(800000); // 0.8 seconds

            // Simulate success (85% success rate for demo)
            $isSuccessful = rand(1, 100) <= 85;

            if ($isSuccessful) {
                $transactionId = 'PP_' . time() . '_' . rand(1000, 9999);
                
                // Update payment record with success
                $payment->update([
                    'gateway_response' => [
                        'transaction_id' => $transactionId,
                        'gateway' => 'demo_paypal_gateway',
                        'paypal_email' => $paypalEmail,
                        'response_code' => '00',
                        'response_message' => 'Approved'
                    ]
                ]);
                $payment->markAsSuccessful($transactionId);
                
                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                    'payment_method' => 'paypal',
                    'amount' => $amount,
                    'currency' => $paymentData['currency'] ?? 'MYR',
                    'appointment_id' => $appointmentId,
                    'paypal_email' => $paypalEmail,
                    'processed_at' => $payment->paid_at->toISOString(),
                    'message' => 'PayPal payment processed successfully'
                ];
            } else {
                $payment->update([
                    'gateway_response' => [
                        'gateway' => 'demo_paypal_gateway',
                        'response_code' => '05',
                        'response_message' => 'Declined'
                    ]
                ]);
                $payment->markAsFailed('PayPal payment was declined or failed');
                
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'PayPal payment was declined or failed'
                ];
            }

        } catch (\Exception $e) {
            if (isset($payment)) {
                $payment->markAsFailed('PayPal payment processing failed: ' . $e->getMessage());
            }
            
            return [
                'success' => false,
                'payment_id' => $payment->id ?? null,
                'message' => 'PayPal payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    public function validatePaymentDetails(array $paymentDetails): bool
    {
        $requiredFields = $this->getRequiredFields();
        
        foreach ($requiredFields as $field) {
            if (!isset($paymentDetails[$field]) || empty($paymentDetails[$field])) {
                return false;
            }
        }

        return true;
    }

    public function getRequiredFields(): array
    {
        return [
            'paypal_email'
        ];
    }
}
