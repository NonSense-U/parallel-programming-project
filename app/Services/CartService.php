<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Throwable;

class CartService
{

    public function checkout($user)
    {
        DB::beginTransaction();
        try {
            $cart = $user->cart()->firstOrFail();
            $order = $user->orders()->create();

            $order->items()->createMany(
                $cart->items()->with('product')->get()->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_snapshot' => $item->product->price,
                    ];
                })->toArray()
            );

            $cart->items()->delete();
            $cart->update(['total_price' => 0]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addItem($user, $productId, $quantity)
    {
        $cart = $user->cart()->firstOrCreate([]);
        
        DB::table('cart_items')->updateOrInsert(
            [
                'cart_id' => $cart->id,
                'product_id' => $productId,
            ],
            [
                'quantity' => $quantity,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $total_price = $cart->items()->join('products', 'cart_items.product_id', '=', 'products.id')
            ->sum(DB::raw('cart_items.quantity * products.price'));

        $cart->update(['total_price' => $total_price]);

        return $cart;
    }

    public function removeItem($user, $productId, $quantity)
    {
        $cart = $user->cart()->firstOrFail();
        $cart->items()->where('product_id', $productId)->delete();


        $total_price = $cart->items()->join('products', 'cart_items.product_id', '=', 'products.id')
            ->sum(DB::raw('cart_items.quantity * products.price'));

        $cart->update(['total_price' => $total_price]);
        return;
    }
}
