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

        // Kiểm tra nếu user là staff (giả sử có trường 'role' trong bảng 'users')
        if ($user->is_staff) {
            // Nếu là staff, lấy tất cả đơn hàng
            $orders = Order::with('orderDetails.book')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Nếu là user bình thường, lấy đơn hàng của user đó
            $orders = Order::with('orderDetails.book')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }
    public function getOrderDetails(Request $request, $id)
    {
        // Fetch the order details for the given order_id
        $user = $request->user();

        $orderDetails = Order::with('orderDetails.book')
            ->where('id', $id)
            ->firstOrFail()
            ->orderDetails;

        return response()->json([
            'status' => 'success',
            'data' => $orderDetails,
        ]);
    }
}
