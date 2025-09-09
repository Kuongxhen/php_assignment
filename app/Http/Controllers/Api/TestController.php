<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use App\Services\Payment\PaymentContext;
use App\Services\Payment\Strategies\CreditCardPaymentStrategy;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Test endpoint to demonstrate the payment system
     */
    public function testPayment(): JsonResponse
    {
        try {
            // Get mock appointment data
            $appointmentId = rand(1000, 9999); // Use integer ID for testing
            $appointmentData = $this->appointmentService->getMockAppointmentData($appointmentId);

            // Create payment context with credit card strategy
            $paymentContext = new PaymentContext();
            $paymentContext->setPaymentStrategy(new CreditCardPaymentStrategy());

            // Mock payment details
            $paymentDetails = [
                'card_number' => '4111111111111111',
                'expiry_date' => '12/25',
                'cvv' => '123',
                'cardholder_name' => 'John Doe'
            ];

            // Process payment
            $paymentResult = $paymentContext->processPayment([
                'appointment_id' => $appointmentId,
                'amount' => $appointmentData['total_amount'],
                'currency' => $appointmentData['currency'],
                'payment_details' => $paymentDetails,
                'appointment_data' => $appointmentData
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'appointment_data' => $appointmentData,
                    'payment_result' => $paymentResult
                ],
                'message' => 'Payment test completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint to demonstrate product API
     */
    public function testProducts(): JsonResponse
    {
        try {
            $productController = new ProductController(app(ProductRepositoryInterface::class));
            
            // Test getting all products
            $allProducts = $productController->index();
            
            // Test getting products by category
            $medicationProducts = $productController->getByCategory('Medication');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'all_products' => $allProducts->getData(),
                    'medication_products' => $medicationProducts->getData()
                ],
                'message' => 'Product API test completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product API test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
