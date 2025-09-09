<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentContext;
use App\Services\Payment\Strategies\CreditCardPaymentStrategy;
use App\Services\Payment\Strategies\PayPalPaymentStrategy;
use App\Services\Payment\Strategies\BankTransferPaymentStrategy;
use App\Services\AppointmentService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Process payment for appointment
     */
    public function processPayment(Request $request): JsonResponse
    {
        // TODO: Uncomment when appointment table exists
        // $request->validate([
        //     'appointment_id' => 'required|integer|exists:appointments,id',
        //     'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer',
        //     'payment_details' => 'required|array'
        // ]);

        // TEMPORARY: Validation without appointment table dependency
        $request->validate([
            'appointment_id' => 'required|integer',
            'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer',
            'payment_details' => 'required|array',
            'amount' => 'sometimes|numeric|min:0.01'
        ]);

        try {
            // TODO: Uncomment when appointment module is available
            // Fetch appointment data from appointment module
            // $appointmentData = $this->appointmentService->getAppointmentData($request->appointment_id);
            
            // TEMPORARY: Mock appointment data for testing purposes
            $appointmentData = $this->getMockAppointmentData($request->appointment_id);
            
            if (!$appointmentData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            // Use provided amount or mock amount
            $amount = $request->amount ?? $appointmentData['total_amount'];
            
            // Create payment record first
            $payment = Payment::create([
                'appointment_id' => $request->appointment_id,
                'amount' => $amount,
                'currency' => $appointmentData['currency'] ?? 'USD',
                'method' => $request->payment_method,
                'status' => 'pending',
                'payment_details' => $request->payment_details,
                'transaction_reference' => 'txn_' . uniqid()
            ]);

            // TODO: Uncomment when payment strategies are ready
            // Create payment context with appropriate strategy
            // $paymentContext = $this->createPaymentContext($request->payment_method);
            // Process payment
            // $paymentResult = $paymentContext->processPayment([
            //     'appointment_id' => $request->appointment_id,
            //     'amount' => $appointmentData['total_amount'],
            //     'currency' => $appointmentData['currency'] ?? 'USD',
            //     'payment_details' => $request->payment_details,
            //     'appointment_data' => $appointmentData
            // ]);

            // TEMPORARY: Simulate payment processing for testing
            $paymentResult = $this->simulatePaymentProcessing($request->payment_method, $request->payment_details, $amount);
            
            // Update payment status based on result
            $payment->update([
                'status' => $paymentResult['success'] ? 'successful' : 'failed',
                'payment_details' => array_merge(
                    $request->payment_details, 
                    $paymentResult['details'] ?? []
                ),
                'paid_at' => $paymentResult['success'] ? now() : null
            ]);

            // Refresh the payment model to get updated data
            $payment->refresh();

            if ($paymentResult['success']) {
                // TODO: Uncomment when appointment module is available
                // Update appointment status in appointment module
                // $this->appointmentService->updateAppointmentStatus(
                //     $request->appointment_id, 
                //     'paid'
                // );

                return response()->json([
                    'success' => true,
                    'data' => [
                        'payment_id' => $payment->id,
                        'transaction_id' => $payment->transaction_reference,
                        'status' => $payment->status,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'payment_method' => $payment->method,
                        'processed_at' => $payment->updated_at->toISOString(),
                        'details' => $paymentResult['details'] ?? []
                    ],
                    'message' => 'Payment processed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Payment failed',
                    'data' => [
                        'payment_id' => $payment->id,
                        'status' => $payment->status,
                        'error_code' => $paymentResult['error_code'] ?? 'PAYMENT_FAILED'
                    ]
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'credit_card' => [
                    'name' => 'Credit Card',
                    'description' => 'Pay with Visa, MasterCard, or American Express',
                    'required_fields' => ['card_number', 'expiry_date', 'cvv', 'cardholder_name']
                ],
                'paypal' => [
                    'name' => 'PayPal',
                    'description' => 'Pay securely with PayPal',
                    'required_fields' => ['paypal_email']
                ],
                'bank_transfer' => [
                    'name' => 'Bank Transfer',
                    'description' => 'Direct bank transfer',
                    'required_fields' => ['account_number', 'routing_number', 'account_holder_name']
                ]
            ],
            'message' => 'Payment methods retrieved successfully'
        ]);
    }

    /**
     * Get payment status by payment ID
     */
    public function getPaymentStatus($paymentId): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'appointment_id' => $payment->appointment_id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'method' => $payment->method,
                    'method_display_name' => $payment->method_display_name,
                    'status' => $payment->status,
                    'status_display_name' => $payment->status_display_name,
                    'transaction_reference' => $payment->transaction_reference,
                    'paid_at' => $payment->paid_at,
                    'failure_reason' => $payment->failure_reason,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ],
                'message' => 'Payment status retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }

    /**
     * Get payments by appointment ID
     */
    public function getPaymentsByAppointment($appointmentId): JsonResponse
    {
        try {
            // Validate appointment ID is integer
            if (!is_numeric($appointmentId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid appointment ID format'
                ], 400);
            }

            $payments = Payment::forAppointment((int)$appointmentId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'method' => $payment->method,
                        'method_display_name' => $payment->method_display_name,
                        'status' => $payment->status,
                        'status_display_name' => $payment->status_display_name,
                        'transaction_reference' => $payment->transaction_reference,
                        'paid_at' => $payment->paid_at,
                        'failure_reason' => $payment->failure_reason,
                        'created_at' => $payment->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $payments,
                'message' => 'Payments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments'
            ], 500);
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Request $request, $paymentId): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $payment = Payment::findOrFail($paymentId);

            if (!$payment->isSuccessful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only successful payments can be refunded'
                ], 400);
            }

            if ($payment->isRefunded()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has already been refunded'
                ], 400);
            }

            // Mark payment as refunded
            $payment->markAsRefunded();

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'status' => $payment->status,
                    'refunded_at' => $payment->updated_at,
                ],
                'message' => 'Payment refunded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refund payment'
            ], 500);
        }
    }

    /**
     * Create payment context with appropriate strategy
     */
    private function createPaymentContext(string $paymentMethod): PaymentContext
    {
        $context = new PaymentContext();

        switch ($paymentMethod) {
            case 'credit_card':
                $context->setPaymentStrategy(new CreditCardPaymentStrategy());
                break;
            case 'paypal':
                $context->setPaymentStrategy(new PayPalPaymentStrategy());
                break;
            case 'bank_transfer':
                $context->setPaymentStrategy(new BankTransferPaymentStrategy());
                break;
            default:
                throw new \InvalidArgumentException("Unsupported payment method: {$paymentMethod}");
        }

        return $context;
    }

    /**
     * Get all payments
     */
    public function index(): JsonResponse
    {
        try {
            $payments = Payment::with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'appointment_id' => $payment->appointment_id,
                        'user_id' => $payment->user_id,
                        'user_name' => $payment->user ? $payment->user->name : null,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'payment_method' => $payment->payment_method,
                        'payment_status' => $payment->payment_status,
                        'transaction_id' => $payment->transaction_id,
                        'created_at' => $payment->created_at,
                        'updated_at' => $payment->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $payments,
                'message' => 'Payments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific payment
     */
    public function show($id): JsonResponse
    {
        try {
            $payment = Payment::with('user')->findOrFail($id);

            $paymentData = [
                'id' => $payment->id,
                'appointment_id' => $payment->appointment_id,
                'user_id' => $payment->user_id,
                'user_name' => $payment->user ? $payment->user->name : null,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'transaction_id' => $payment->transaction_id,
                'payment_details' => $payment->payment_details,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $paymentData,
                'message' => 'Payment retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }

    /**
     * Get mock appointment data for testing
     * TODO: Remove this method when actual appointment service is integrated
     * This is a temporary solution to provide test data for payment processing
     */
    private function getMockAppointmentData($appointmentId): ?array
    {
        // Mock appointment data - in real implementation, this would come from appointment service
        $mockAppointments = [
            123 => [
                'id' => 123,
                'user_id' => 1,
                'patient_name' => 'John Doe',
                'appointment_date' => '2025-09-15',
                'appointment_time' => '10:00:00',
                'services' => [
                    ['name' => 'Consultation', 'price' => 50.00],
                    ['name' => 'Blood Test', 'price' => 25.50]
                ],
                'total_amount' => 75.50,
                'currency' => 'USD',
                'status' => 'confirmed'
            ],
            124 => [
                'id' => 124,
                'user_id' => 2,
                'patient_name' => 'Jane Smith',
                'appointment_date' => '2025-09-16',
                'appointment_time' => '14:30:00',
                'services' => [
                    ['name' => 'X-Ray', 'price' => 100.00],
                    ['name' => 'Consultation', 'price' => 50.00]
                ],
                'total_amount' => 150.00,
                'currency' => 'USD',
                'status' => 'confirmed'
            ],
            125 => [
                'id' => 125,
                'user_id' => 1,
                'patient_name' => 'Bob Johnson',
                'appointment_date' => '2025-09-17',
                'appointment_time' => '09:15:00',
                'services' => [
                    ['name' => 'Dental Cleaning', 'price' => 80.00]
                ],
                'total_amount' => 80.00,
                'currency' => 'USD',
                'status' => 'pending'
            ],
            12345 => [
                'id' => 12345,
                'user_id' => 1,
                'patient_name' => 'John Doe',
                'appointment_date' => '2025-09-15',
                'appointment_time' => '10:00:00',
                'services' => [
                    ['name' => 'Medical Consultation', 'price' => 100.00],
                    ['name' => 'Health Screening', 'price' => 50.00]
                ],
                'total_amount' => 150.00,
                'currency' => 'MYR',
                'status' => 'confirmed'
            ]
        ];

        return $mockAppointments[$appointmentId] ?? null;
    }

    /**
     * Simulate payment processing for different payment methods
     * TODO: Remove this method when actual payment gateway integration is complete
     * This provides realistic testing scenarios without external API calls
     */
    private function simulatePaymentProcessing(string $paymentMethod, array $paymentDetails, float $amount): array
    {
        // Simulate processing delay
        usleep(500000); // 0.5 second delay

        switch ($paymentMethod) {
            case 'credit_card':
                return $this->simulateCreditCardPayment($paymentDetails, $amount);
                
            case 'paypal':
                return $this->simulatePayPalPayment($paymentDetails, $amount);
                
            case 'bank_transfer':
                return $this->simulateBankTransferPayment($paymentDetails, $amount);
                
            default:
                return [
                    'success' => false,
                    'message' => 'Invalid payment method',
                    'error_code' => 'INVALID_METHOD'
                ];
        }
    }

    /**
     * Simulate credit card payment processing
     */
    private function simulateCreditCardPayment(array $details, float $amount): array
    {
        // Validate required fields
        $requiredFields = ['card_number', 'expiry_date', 'cvv', 'cardholder_name'];
        foreach ($requiredFields as $field) {
            if (!isset($details[$field]) || empty($details[$field])) {
                return [
                    'success' => false,
                    'message' => "Missing required field: {$field}",
                    'error_code' => 'MISSING_FIELD'
                ];
            }
        }

        $cardNumber = $details['card_number'];
        
        // Simulate different card scenarios
        if ($cardNumber === '4000000000000002') {
            return [
                'success' => false,
                'message' => 'Card declined by issuer',
                'error_code' => 'CARD_DECLINED'
            ];
        }
        
        if ($cardNumber === '4000000000000127') {
            return [
                'success' => false,
                'message' => 'Insufficient funds',
                'error_code' => 'INSUFFICIENT_FUNDS'
            ];
        }

        // Successful payment
        return [
            'success' => true,
            'message' => 'Credit card payment processed successfully',
            'details' => [
                'card_last_four' => substr($cardNumber, -4),
                'card_type' => $this->getCardType($cardNumber),
                'authorization_code' => 'AUTH_' . strtoupper(uniqid()),
                'processor_response' => 'APPROVED'
            ]
        ];
    }

    /**
     * Simulate PayPal payment processing
     */
    private function simulatePayPalPayment(array $details, float $amount): array
    {
        if (!isset($details['email']) || empty($details['email'])) {
            return [
                'success' => false,
                'message' => 'PayPal email is required',
                'error_code' => 'MISSING_EMAIL'
            ];
        }

        // Simulate PayPal scenarios
        if ($details['email'] === 'declined@example.com') {
            return [
                'success' => false,
                'message' => 'PayPal payment declined',
                'error_code' => 'PAYPAL_DECLINED'
            ];
        }

        return [
            'success' => true,
            'message' => 'PayPal payment processed successfully',
            'details' => [
                'paypal_email' => $details['email'],
                'paypal_transaction_id' => 'PP_' . strtoupper(uniqid()),
                'payer_status' => 'VERIFIED'
            ]
        ];
    }

    /**
     * Simulate bank transfer payment processing
     */
    private function simulateBankTransferPayment(array $details, float $amount): array
    {
        $requiredFields = ['account_number', 'routing_number', 'account_holder_name'];
        foreach ($requiredFields as $field) {
            if (!isset($details[$field]) || empty($details[$field])) {
                return [
                    'success' => false,
                    'message' => "Missing required field: {$field}",
                    'error_code' => 'MISSING_FIELD'
                ];
            }
        }

        // Simulate bank scenarios
        if ($details['account_number'] === '1234567890') {
            return [
                'success' => false,
                'message' => 'Invalid account number',
                'error_code' => 'INVALID_ACCOUNT'
            ];
        }

        return [
            'success' => true,
            'message' => 'Bank transfer initiated successfully',
            'details' => [
                'account_last_four' => substr($details['account_number'], -4),
                'bank_reference' => 'BT_' . strtoupper(uniqid()),
                'processing_time' => '1-3 business days',
                'status' => 'PENDING_VERIFICATION'
            ]
        ];
    }

    /**
     * Determine card type from card number
     */
    private function getCardType(string $cardNumber): string
    {
        $firstDigit = substr($cardNumber, 0, 1);
        $firstTwoDigits = substr($cardNumber, 0, 2);
        
        if ($firstDigit === '4') {
            return 'visa';
        } elseif (in_array($firstTwoDigits, ['51', '52', '53', '54', '55'])) {
            return 'mastercard';
        } elseif (in_array($firstTwoDigits, ['34', '37'])) {
            return 'amex';
        } else {
            return 'unknown';
        }
    }
}
