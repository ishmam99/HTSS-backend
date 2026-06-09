<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Order;
use App\Models\TrainingEnrollment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid webhook'], 400);
        }

        // =========================
        // PAYMENT SUCCESS
        // =========================
        if ($event->type === 'payment_intent.succeeded') {

            $paymentIntent = $event->data->object;

            $this->handleSuccess($paymentIntent);
        }

        // =========================
        // PAYMENT FAILED
        // =========================
        if ($event->type === 'payment_intent.payment_failed') {

            $paymentIntent = $event->data->object;

            $this->handleFailed($paymentIntent);
        }

        return response()->json(['status' => 'success']);
    }

    private function handleSuccess($paymentIntent)
    {
        // IMPORTANT: use metadata to find order
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) return;

        $order = TrainingEnrollment::find($orderId);

        if (!$order) return;

        if ($order->status === 2) return; // prevent double processing

        $order->update([
            'status' => 2,
            'transaction_id' => $paymentIntent->id,
        ]);

        // 👉 OPTIONAL: stock deduction
        // 👉 OPTIONAL: send email
        // 👉 OPTIONAL: generate invoice
    }

    private function handleFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) return;

        $order = TrainingEnrollment::find($orderId);

        if (!$order) return;

        $order->update([
            'status' => 3 // Assuming 3 represents payment failed
        ]);
    }

     public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string'
        ]);

        Stripe::setApiKey(config('stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount * 100, // Stripe uses cents
            'currency' => $request->currency ?? 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret
        ]);
    }
}