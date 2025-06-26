<?php

namespace App\Observers;

use App\Models\ProductStockLog;

class ProductStockObserver
{
    public function created(ProductStockLog $log)
    {
        // Code after save
        $log->voucher_no = orderId($log->id, 'TID');
        $log->saveQuietly();
    }
}
