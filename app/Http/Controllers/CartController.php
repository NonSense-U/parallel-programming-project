<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{

    private $cartService;

    public function __construct(\App\Services\CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->cartService->addItem($request->user(), $request->product_id, $request->quantity);

        return ApiResponse::success('Product added to cart successfully', data: $cart);
    }

    public function removeItem(Request $request, $productId)
    {
        $cart = $request->user()->cart()->first();
        if (!$cart) {
            return ApiResponse::fail('Cart not found', 404);
        }

        $cartItem = $cart->items()->where('product_id', $productId)->first();
        if (!$cartItem) {
            return ApiResponse::fail('Product not found in cart', 404);
        }

        $cartItem->delete();

        $total_price = $cart->items()->with('product')->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $cart->update(['total_price' => $total_price]);

        return ApiResponse::success('Product removed from cart successfully', data: $cart);
    }


    public function checkout(Request $request)
    {
        $this->cartService->checkout($request->user());
        return ApiResponse::success();
    }

    public function empty(Request $request)
    {
        $cart = $request->user()->cart()->first();
        if (!$cart) {
            return ApiResponse::fail('Cart not found', 404);
        }

        $cart->items()->delete();
        $cart->update(['total_price' => 0]);

        return ApiResponse::success('Cart emptied successfully', data: $cart);
    }

    public function show(Request $request)
    {
        $cart = $request->user()->cart()->with('items.product')->first();
        if (!$cart) {
            return ApiResponse::fail('Cart not found', 404);
        }

        return ApiResponse::success('Cart retrieved successfully', data: $cart);
    }
}
