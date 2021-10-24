<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Exception;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', '=', $request->user->id)->orderBy('id', 'desc')->get();
        foreach($orders as $order) {
            $this->simplyfyOrder($order);
        }
        return response(["status" => true, 'data' => $orders]);
    }

    public function order($id)
    {
        try {
            $order = Order::findOrfail($id);
            // $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
            $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
            $data = [];
            foreach($cart as $key => $val) {
                $data[$key] = $val;
            }
            $items = [];
            foreach($cart->items as $item) {
                $items[] = $item;
            }
            $data['items'] = $items;
            return response($data);
        } catch (Exception $err) {
            return response($err);
        }
    }
    function simplyfyOrder($order) {
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $data = [];
        foreach($cart as $key => $val) {
            $data[$key] = $val;
        }
        $items = [];
        foreach($cart->items as $item) {
            $items[] = $item;
        }
        $data['items'] = $items;
        $order['cart'] = $data;
    }
}
