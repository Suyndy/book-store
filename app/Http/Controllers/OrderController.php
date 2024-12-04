<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Lấy danh sách các đơn hàng của user đã đăng nhập.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrders(Request $request)
    {
        // Lấy user đã đăng nhập
        $user = $request->user();

        // Lấy danh sách các order của user
        $orders = Order::with('orderDetails.book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }
}