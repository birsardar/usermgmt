<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'product_name' => $request->product_name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'status' => 'pending',
                'payment_status' => 'unpaid'
            ]);
            return response()->json(['message' => 'Order placed successfully.', 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order placement failed.']);
        }
    }

    public function trackOrder($id)
    {
        try {
            $order = Order::findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order not found.']);
        }
    }

    public function updatePaymentStatus(Request $request, $orderId)
    {
        try {
            // Retrieve the authenticated user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            // Validate the incoming request for the payment status
            $validator = Validator::make($request->all(), [
                'status' => 'required|string', // Add specific rules for allowed statuses if needed
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Retrieve the order
            $order = Order::where('id', $orderId)->where('user_id', $user->id)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Update the payment status
            $order->update([
                'status' => $request->status, // Change 'status' to 'payment_status'
                'payment_status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully.',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status.',
                'error' => $e->getMessage(),
                'order' => $request->all(),
                'id' => $orderId
            ], 500);
        }
    }
}
