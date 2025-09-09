<?php

namespace App\Services\Payment;

interface PaymentStrategyInterface
{
    /**
     * Process payment with the specific strategy
     *
     * @param array $paymentData
     * @return array
     */
    public function processPayment(array $paymentData): array;

    /**
     * Validate payment details for this strategy
     *
     * @param array $paymentDetails
     * @return bool
     */
    public function validatePaymentDetails(array $paymentDetails): bool;

    /**
     * Get required fields for this payment method
     *
     * @return array
     */
    public function getRequiredFields(): array;
}
