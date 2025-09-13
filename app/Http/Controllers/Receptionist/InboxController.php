<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class InboxController extends Controller
{
    public function index()
    {
        // session-based receptionist check reused
        if (!session('user') || session('user_role') !== 'receptionist') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as a receptionist.');
        }
        $messages = Message::latest()->get();
        return view('receptionist.messages.index', compact('messages'));
    }

    public function show(Message $message)
    {
        if ($message->status === 'sent') {
            $message->update(['status' => 'read', 'read_at' => now()]);
        }
        return view('receptionist.messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $validated = $request->validate(['reply_body' => 'required|string|max:2000']);
        $message->update([
            'reply_body' => $validated['reply_body'],
            'reply_by_name' => session('user')->staffName ?? 'Receptionist',
            'status' => 'archived'
        ]);
        return redirect()->route('staffmod.receptionist.inbox')->with('success', 'Reply saved.');
    }
}


