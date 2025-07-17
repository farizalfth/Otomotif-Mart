<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class OrderWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Menerima notifikasi pesanan dari Hub', $request->all());

        $validator = Validator::make($request->all(), [
            'order_number' => 'required|string|unique:orders,id',
            'total_amount' => 'required|numeric',
            'shipping_address' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi data pesanan dari Hub gagal', $validator->errors()->toArray());
            return response()->json(['message' => 'Invalid data provided', 'errors' => $validator->errors()], 400);
        }

        try {
            $user = User::first(); 

            $order = Order::create([
                'id' => $request->order_number,
                'user_id' => $user->id,
                'grand_total' => $request->total_amount,
                'payment_method' => 'hub_umkm',
                'payment_status' => 'paid',
                'status' => 'new',
                'notes' => $request->notes,
            ]);

            Address::create([
                'order_id' => $order->id,
                'street_address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_province,
                'zip_code' => $request->shipping_postal_code,
                'first_name' => $request->customer_name ?? 'Pelanggan',
                'last_name' => 'Hub',
                'phone' => $request->customer_phone ?? '000',
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_amount' => $item['price'],
                    'total_amount' => $item['price'] * $item['quantity'],
                ]);
            }

            Log::info('Pesanan dari Hub berhasil disimpan dengan ID: ' . $order->id);
            return response()->json(['message' => 'Order received successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pesanan dari Hub', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to process order'], 500);
        }
    }
}
