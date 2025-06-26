<?php

namespace App\Observers;

use App\Models\MainDistributor;

class MainDistributorObserver
{
    public function created(MainDistributor $main_distributer)
    {
        // Code after save
        $main_distributer->userId = orderId($main_distributer->id, 'M', 6);
        $main_distributer->saveQuietly();
    }
}
