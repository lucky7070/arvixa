<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerObserver
{
    // retrieved, creating, created, updating, updated, saving, saved, deleting, deleted,  restoring, restored
    public function creating(Customer $customer)
    {
        $customer->slug         = Str::uuid();
        $customer->image        = $customer->image ? $customer->image : 'customer/avatar.png';
        $customer->password     = $customer->password ? $customer->password : Hash::make($customer->mobile);
    }
}
