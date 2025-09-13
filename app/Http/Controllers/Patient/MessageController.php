<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        $messages = Message::where('patient_id', $patient->id)->latest()->get();
        return view('patient.messages.index', compact('messages'));
    }

    public function create()
    {
        return view('patient.messages.create');
    }

    public function store(Request $request)
    {
        $patient = Auth::user()->patient;
        $validated = $request->validate([
            'category' => 'required|in:general,prescription,billing',
            'subject' => 'required|string|max:120',
            'body' => 'required|string|max:2000',
        ]);
        Message::create(array_merge($validated, ['patient_id' => $patient->id]));
        return redirect()->route('patient.messages.index')->with('success', 'Message sent to receptionist.');
    }
}


