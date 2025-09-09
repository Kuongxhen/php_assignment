<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    private string $appointmentApiBaseUrl;
    private string $apiKey;

    public function __construct()
    {
        // In real implementation, these would come from config
        $this->appointmentApiBaseUrl = config('services.appointment.api_url', 'http://localhost:8001/api');
        $this->apiKey = config('services.appointment.api_key', 'your-api-key-here');
    }

    /**
     * Get appointment data from appointment module
     */
    public function getAppointmentData(int $appointmentId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->timeout(30)->get("{$this->appointmentApiBaseUrl}/appointments/{$appointmentId}");

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    return $data['data'];
                }
            }

            Log::warning('Failed to fetch appointment data', [
                'appointment_id' => $appointmentId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception while fetching appointment data', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Update appointment status in appointment module
     */
    public function updateAppointmentStatus(int $appointmentId, string $status): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->timeout(30)->put("{$this->appointmentApiBaseUrl}/appointments/{$appointmentId}/status", [
                'status' => $status,
                'updated_at' => now()->toISOString()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? false;
            }

            Log::warning('Failed to update appointment status', [
                'appointment_id' => $appointmentId,
                'status' => $status,
                'response_status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Exception while updating appointment status', [
                'appointment_id' => $appointmentId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get appointment products/cart items
     */
    public function getAppointmentProducts(int $appointmentId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json'
            ])->timeout(30)->get("{$this->appointmentApiBaseUrl}/appointments/{$appointmentId}/products");

            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ? $data['data'] : null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Exception while fetching appointment products', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Create payment record in appointment module
     */
    public function createPaymentRecord(int $appointmentId, array $paymentData): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->timeout(30)->post("{$this->appointmentApiBaseUrl}/appointments/{$appointmentId}/payments", $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? false;
            }

            Log::warning('Failed to create payment record', [
                'appointment_id' => $appointmentId,
                'response_status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Exception while creating payment record', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Mock appointment data for testing when appointment module is not available
     */
    public function getMockAppointmentData(int $appointmentId): array
    {
        return [
            'appointment_id' => $appointmentId,
            'patient_id' => 'PAT_001',
            'patient_name' => 'John Doe',
            'appointment_date' => now()->addDays(1)->toISOString(),
            'total_amount' => 150.00,
            'currency' => 'USD',
            'status' => 'pending_payment',
            'products' => [
                [
                    'product_id' => 1,
                    'name' => 'Paracetamol',
                    'quantity' => 2,
                    'price' => 10.00,
                    'subtotal' => 20.00
                ],
                [
                    'product_id' => 2,
                    'name' => 'Vitamin C',
                    'quantity' => 1,
                    'price' => 15.00,
                    'subtotal' => 15.00
                ]
            ],
            'services' => [
                [
                    'service_id' => 'CONSULTATION',
                    'name' => 'Doctor Consultation',
                    'price' => 100.00
                ],
                [
                    'service_id' => 'LAB_TEST',
                    'name' => 'Blood Test',
                    'price' => 15.00
                ]
            ]
        ];
    }
}
