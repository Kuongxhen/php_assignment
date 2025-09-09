<?php

namespace App\Services\Payment\Strategies;

use App\Services\Payment\PaymentStrategyInterface;
use App\Models\Payment;

class BankTransferPaymentStrategy implements PaymentStrategyInterface
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
                'method' => Payment::METHOD_BANK_TRANSFER,
                'status' => Payment::STATUS_PENDING,
                'payment_details' => $this->encryptPaymentDetails($paymentDetails),
            ]);

            $accountNumber = $paymentDetails['account_number'];
            $routingNumber = $paymentDetails['routing_number'];
            $accountHolderName = $paymentDetails['account_holder_name'];

            // Validate routing number (basic US bank routing number validation)
            if (!preg_match('/^\d{9}$/', $routingNumber)) {
                $payment->markAsFailed('Invalid routing number format');
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Invalid routing number format'
                ];
            }

            // Validate account number (basic validation)
            if (!preg_match('/^\d{4,17}$/', $accountNumber)) {
                $payment->markAsFailed('Invalid account number format');
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Invalid account number format'
                ];
            }

            // Simulate bank processing delay
            usleep(1000000); // 1 second

            // Simulate success (95% success rate for demo)
            $isSuccessful = rand(1, 100) <= 95;

            if ($isSuccessful) {
                $transactionId = 'BT_' . time() . '_' . rand(1000, 9999);
                
                // Update payment record with success
                $payment->update([
                    'gateway_response' => [
                        'transaction_id' => $transactionId,
                        'gateway' => 'demo_bank_transfer_gateway',
                        'account_holder_name' => $accountHolderName,
                        'response_code' => '00',
                        'response_message' => 'Transfer Initiated'
                    ]
                ]);
                $payment->markAsSuccessful($transactionId);
                
                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                    'payment_method' => 'bank_transfer',
                    'amount' => $amount,
                    'currency' => $paymentData['currency'] ?? 'MYR',
                    'appointment_id' => $appointmentId,
                    'account_holder_name' => $accountHolderName,
                    'processed_at' => $payment->paid_at->toISOString(),
                    'message' => 'Bank transfer initiated successfully. Payment will be processed within 1-3 business days.'
                ];
            } else {
                $payment->update([
                    'gateway_response' => [
                        'gateway' => 'demo_bank_transfer_gateway',
                        'response_code' => '05',
                        'response_message' => 'Transfer Failed'
                    ]
                ]);
                $payment->markAsFailed('Bank transfer failed. Please verify account details.');
                
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Bank transfer failed. Please verify account details.'
                ];
            }

        } catch (\Exception $e) {
            if (isset($payment)) {
                $payment->markAsFailed('Bank transfer processing failed: ' . $e->getMessage());
            }
            
            return [
                'success' => false,
                'payment_id' => $payment->id ?? null,
                'message' => 'Bank transfer processing failed: ' . $e->getMessage()
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
            'account_number',
            'routing_number',
            'account_holder_name'
        ];
    }

    /**
     * Encrypt sensitive payment details
     */
    private function encryptPaymentDetails(array $paymentDetails): array
    {
        // In production, use proper encryption
        // For demo purposes, we'll mask sensitive data
        $encrypted = $paymentDetails;
        
        // Mask account number (show only last 4 digits)
        if (isset($encrypted['account_number'])) {
            $accountNumber = $encrypted['account_number'];
            $encrypted['account_number'] = '****' . substr($accountNumber, -4);
        }
        
        // Mask routing number (show only last 4 digits)
        if (isset($encrypted['routing_number'])) {
            $routingNumber = $encrypted['routing_number'];
            $encrypted['routing_number'] = '*****' . substr($routingNumber, -4);
        }
        
        return $encrypted;
    }
}
