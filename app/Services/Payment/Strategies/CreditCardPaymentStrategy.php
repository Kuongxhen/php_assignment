<?php

namespace App\Services\Payment\Strategies;

use App\Services\Payment\PaymentStrategyInterface;
use App\Models\Payment;

class CreditCardPaymentStrategy implements PaymentStrategyInterface
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
                'method' => Payment::METHOD_CREDIT_CARD,
                'status' => Payment::STATUS_PENDING,
                'payment_details' => $this->encryptPaymentDetails($paymentDetails),
            ]);

            // Validate card number format (basic validation)
            $cardNumber = str_replace(' ', '', $paymentDetails['card_number']);
            if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
                $payment->markAsFailed('Invalid card number format');
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Invalid card number format'
                ];
            }

            // Validate expiry date
            $expiryDate = $paymentDetails['expiry_date'];
            if (!$this->isValidExpiryDate($expiryDate)) {
                $payment->markAsFailed('Card has expired or invalid expiry date');
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Card has expired or invalid expiry date'
                ];
            }

            // Simulate payment processing delay
            usleep(500000); // 0.5 seconds

            // Simulate success (90% success rate for demo)
            $isSuccessful = rand(1, 10) <= 9;

            if ($isSuccessful) {
                $transactionId = 'CC_' . time() . '_' . rand(1000, 9999);
                
                // Update payment record with success
                $payment->update([
                    'gateway_response' => [
                        'transaction_id' => $transactionId,
                        'gateway' => 'demo_credit_card_gateway',
                        'response_code' => '00',
                        'response_message' => 'Approved'
                    ]
                ]);
                $payment->markAsSuccessful($transactionId);
                
                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                    'payment_method' => 'credit_card',
                    'amount' => $amount,
                    'currency' => $paymentData['currency'] ?? 'MYR',
                    'appointment_id' => $appointmentId,
                    'processed_at' => $payment->paid_at->toISOString(),
                    'message' => 'Credit card payment processed successfully'
                ];
            } else {
                $payment->update([
                    'gateway_response' => [
                        'gateway' => 'demo_credit_card_gateway',
                        'response_code' => '05',
                        'response_message' => 'Declined'
                    ]
                ]);
                $payment->markAsFailed('Credit card payment declined by bank');
                
                return [
                    'success' => false,
                    'payment_id' => $payment->id,
                    'message' => 'Credit card payment declined by bank'
                ];
            }

        } catch (\Exception $e) {
            if (isset($payment)) {
                $payment->markAsFailed('Credit card payment processing failed: ' . $e->getMessage());
            }
            
            return [
                'success' => false,
                'payment_id' => $payment->id ?? null,
                'message' => 'Credit card payment processing failed: ' . $e->getMessage()
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
            'card_number',
            'expiry_date',
            'cvv',
            'cardholder_name'
        ];
    }

    private function isValidExpiryDate(string $expiryDate): bool
    {
        // Expected format: MM/YY or MM/YYYY
        if (!preg_match('/^(\d{2})\/(\d{2,4})$/', $expiryDate, $matches)) {
            return false;
        }

        $month = (int) $matches[1];
        $year = (int) $matches[2];

        // Convert 2-digit year to 4-digit
        if ($year < 100) {
            $year += 2000;
        }

        // Check if month is valid
        if ($month < 1 || $month > 12) {
            return false;
        }

        // Check if card has expired
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
            return false;
        }

        return true;
    }

    /**
     * Encrypt sensitive payment details
     */
    private function encryptPaymentDetails(array $paymentDetails): array
    {
        // In production, use proper encryption
        // For demo purposes, we'll mask sensitive data
        $encrypted = $paymentDetails;
        
        // Mask card number (show only last 4 digits)
        if (isset($encrypted['card_number'])) {
            $cardNumber = str_replace(' ', '', $encrypted['card_number']);
            $encrypted['card_number'] = '**** **** **** ' . substr($cardNumber, -4);
        }
        
        // Mask CVV
        if (isset($encrypted['cvv'])) {
            $encrypted['cvv'] = '***';
        }
        
        return $encrypted;
    }
}
