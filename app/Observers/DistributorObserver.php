<?php

namespace App\Observers;

use App\Models\Distributor;

class DistributorObserver
{
    public function created(Distributor $distributer)
    {
        // Code after save
        $distributer->userId = orderId($distributer->id, 'D', 6);
        $distributer->saveQuietly();
    }
}
