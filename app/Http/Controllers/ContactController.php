<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'topic'   => 'nullable|string|max:100',
            'message' => 'required|string|max:5000',
        ]);

        $to      = config('mail.from.address', 'contact@anim24.com');
        $subject = '[Anim24 Contact] ' . ($validated['topic'] ?? 'General') . ' from ' . $validated['name'];
        $body    = "Name: {$validated['name']}\nEmail: {$validated['email']}\nTopic: " . ($validated['topic'] ?? 'General') . "\n\n{$validated['message']}";

        Mail::raw($body, function ($message) use ($to, $subject, $validated) {
            $message->to($to)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject($subject);
        });

        return redirect()->route('contact')->with('success', 'Your message has been sent. We\'ll get back to you within 2 business days.');
    }
}
