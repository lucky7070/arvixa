<?php

namespace App\Observers;

use App\Models\Ledger;

class LedgerObserver
{

    // retrieved, creating, created, updating, updated, saving, saved, deleting, deleted,  restoring, restored
    public function created(Ledger $ledger)
    {
        // Code after save
        $ledger->voucher_no = orderId($ledger->id, 'TID');
        $ledger->saveQuietly();
    }
}
