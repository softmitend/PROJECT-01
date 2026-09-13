<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function access(User $user): bool
    {
        return true;
    }
}
