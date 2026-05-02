<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function checkout(User $user)
    {
        DB::beginTransaction();

        try {
            $cart = $user->cart()->firstOrFail();
            $items = $cart->items()->get();

            if ($items->isEmpty()) {
                throw new Exception('Cart is empty');
            }

            $productIds = $items->pluck('product_id');

            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products[$item->product_id] ?? null;

                if (! $product || $product->stock < $item->quantity) {
                    throw new Exception('Out of stock');
                }
                $product->decrement('stock', $item->quantity);
            }

            $order = $user->orders()->create();

            $order->items()->createMany(
                $items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price_snapshot' => $products[$item->product_id]->price,
                ])->toArray()
            );

            // $user->notify(new \App\Notifications\OrderCompletedNotification());

            $cart->items()->delete();
            $cart->update(['total_price' => 0]);

            DB::commit();
        } catch (Exception $e) {
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

    }
}
