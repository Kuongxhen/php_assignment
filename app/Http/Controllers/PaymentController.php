<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;

class PaymentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Show payment page
     */
    public function index(Request $request)
    {
        $appointmentId = $request->get('appointment_id', 12345); // Default for demo
        
        // In real implementation, fetch appointment data
        $appointmentData = $this->appointmentService->getMockAppointmentData($appointmentId);
        
        return view('payment.index', compact('appointmentData'));
    }

    /**
     * Show payment success page
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if (!$paymentId) {
            return redirect('/')->with('error', 'Invalid payment reference.');
        }

        return view('payment.success', compact('paymentId'));
    }

    /**
     * Show payment failure page
     */
    public function failure(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if (!$paymentId) {
            return redirect('/')->with('error', 'Invalid payment reference.');
        }

        return view('payment.failure', compact('paymentId'));
    }
}
