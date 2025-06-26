<?php

namespace App\Observers;

use App\Models\PaymentRequest;

class WithdrawalRequestObserver
{

    // retrieved, creating, created, updating, updated, saving, saved, deleting, deleted,  restoring, restored
    public function created(PaymentRequest $request)
    {
        // Code after save
        $request->request_number = orderId($request->id, 'WID');
        $request->saveQuietly();
    }
}
