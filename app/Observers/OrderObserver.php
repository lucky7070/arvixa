<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        // Code after save
        $order->voucher_no = orderId($order->id, 'ORD', 7);
        $order->saveQuietly();
    }
}
