<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Purono order gula ke order_items e copy koro
        // (jodi product_id + product_name + quantity thake)

        $orders = DB::table('orders')
            ->whereNotNull('product_id')
            ->whereNotNull('product_name')
            ->whereNotNull('quantity')
            ->get();

        foreach ($orders as $order) {
            // Check — ei order e already item ache kina
            $exists = DB::table('order_items')
                ->where('order_id', $order->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $unitPrice = $order->quantity > 0
                ? $order->subtotal / $order->quantity
                : $order->subtotal;

            DB::table('order_items')->insert([
                'order_id'     => $order->id,
                'product_id'   => $order->product_id,
                'product_name' => $order->product_name,
                'quantity'     => $order->quantity,
                'unit_price'   => round($unitPrice, 2),
                'total_price'  => $order->subtotal,
                'created_at'   => $order->created_at ?? now(),
                'updated_at'   => $order->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        // Rollback e sob order_items remove koro
        DB::table('order_items')->truncate();
    }
};