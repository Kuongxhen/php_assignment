<?php

namespace App\Services\Payment;

class PaymentContext
{
    private PaymentStrategyInterface $paymentStrategy;

    /**
     * Set the payment strategy
     */
    public function setPaymentStrategy(PaymentStrategyInterface $paymentStrategy): void
    {
        $this->paymentStrategy = $paymentStrategy;
    }

    /**
     * Process payment using the current strategy
     */
    public function processPayment(array $paymentData): array
    {
        if (!isset($this->paymentStrategy)) {
            throw new \RuntimeException('Payment strategy not set');
        }

        // Validate payment details
        if (!$this->paymentStrategy->validatePaymentDetails($paymentData['payment_details'])) {
            return [
                'success' => false,
                'message' => 'Invalid payment details',
                'required_fields' => $this->paymentStrategy->getRequiredFields()
            ];
        }

        // Process payment
        return $this->paymentStrategy->processPayment($paymentData);
    }
}
