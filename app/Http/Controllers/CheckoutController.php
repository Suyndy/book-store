<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        // Validate the request data
        if (!$request->has('cartItems') || !is_array($request->cartItems)) {
            return response()->json(['error' => 'Invalid cart items'], 400);
        }

        // Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $cartItems = $request->cartItems;

        // Calculate the total price
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            $totalPrice += $book->price * $item['quantity'];
        }

        // Create a new order
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Create order details
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            OrderDetail::create([
                'order_id' => $order->id,
                'book_id' => $book->id,
                'quantity' => $item['quantity'],
                'price' => $book->price,
            ]);
        }

        // Create a payment intent
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::create([
                'amount' => $totalPrice, 
                'currency' => 'vnd',
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment intent creation failed', 'message' => $e->getMessage()], 500);
        }

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'order_id' => $order->id,
        ]);
    }

    public function handlePaymentSuccess(Request $request)
    {
        // Validate the request data
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'stripe_payment_id' => 'required|string',
        ]);

        // Check if the payment has already been processed
        if (Payment::where('transaction_id', $request->stripe_payment_id)->exists()) {
            return response()->json(['message' => 'Payment already processed'], 400);
        }

        $order = Order::findOrFail($request->order_id);
        $order->status = 'completed';
        $order->save();

        // Save payment information
        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $request->stripe_payment_id,
            'payment_date' => now(),
            'payment_method' => 'card',
        ]);

        // Update user payment information if needed
        $user = $order->user;
        $user->pm_type = 'card';
        $user->save();

        return response()->json(['status' => 'success']);
    }
}