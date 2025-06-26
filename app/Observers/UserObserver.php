<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        // Code after save
        $user->userId = orderId($user->id, 'A', 6);
        $user->saveQuietly();
    }
}
