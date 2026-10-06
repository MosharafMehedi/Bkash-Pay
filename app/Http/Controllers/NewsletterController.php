<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->email));

        try {
            // Check if already subscribed
            $existing = NewsletterSubscriber::where('email', $email)->first();

            if ($existing) {
                // Reactivate if inactive
                if (! $existing->is_active) {
                    $existing->update([
                        'is_active' => true,
                        'subscribed_at' => now(),
                        'unsubscribed_at' => null,
                    ]);

                    return response()->json([
                        'ok' => true,
                        'message' => 'Welcome back! You have been resubscribed.',
                    ]);
                }

                return response()->json([
                    'ok' => true,
                    'message' => 'You are already subscribed to our newsletter.',
                ]);
            }

            // Create new subscriber
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'ip_address' => $request->ip(),
                'subscribed_at' => now(),
            ]);

            // Send welcome email
            try {
                Mail::to($email)->send(new NewsletterWelcomeMail($subscriber));
            } catch (\Throwable $e) {
                Log::error('Newsletter welcome mail failed: ' . $e->getMessage());
            }

            return response()->json([
                'ok' => true,
                'message' => 'Successfully subscribed! Check your email.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Newsletter subscribe failed: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'message' => 'Subscription failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Unsubscribe via token.
     */
    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (! $subscriber) {
            return view('welcome.newsletter-unsubscribed', [
                'success' => false,
                'message' => 'Invalid unsubscribe link.',
            ]);
        }

        if (! $subscriber->is_active) {
            return view('welcome.newsletter-unsubscribed', [
                'success' => true,
                'message' => 'You are already unsubscribed.',
            ]);
        }

        $subscriber->unsubscribe();

        return view('welcome.newsletter-unsubscribed', [
            'success' => true,
            'message' => 'You have been successfully unsubscribed.',
        ]);
    }
}