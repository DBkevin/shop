<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\UserAddress;

class UserAddressPolicy
{
    use HandlesAuthorization;


    public function own(User $user, UserAddress $user_address)
    {
        return $user->id === $user_address->user_id;
    }
}
