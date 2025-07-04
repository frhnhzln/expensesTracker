<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile; // ✅ use Profile instead of User
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function verifyUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email'
        ]);

        $user = Profile::where('username', $request->username)
                       ->where('email', $request->email)
                       ->first();

        if (!$user) {
            return response()->json([
                'errors' => ['The username and email do not match any account.']
            ], 422);
        }

        $qr = 'https://api.qrserver.com/v1/create-qr-code/?data=ResetToken123&size=200x200';

        // Send test email
        Mail::raw("Reset your password: $qr", function($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Request');
        });

        return response()->json([
            'message' => 'Verification successful! Check your email for reset instructions.',
            'qr' => $qr
        ]);
    }
}

