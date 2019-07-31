<?php

namespace App\Transformers;

use App\Models\UserAddress;
use League\Fractal\TransformerAbstract;

class UserAddressesTransformer extends TransformerAbstract
{
    public function  transform(UserAddress $userAddress)
    { 
        return [
            'address'=>$userAddress->full_address,
            'zip'=>(int)$userAddress->zip,
            'contact_name'=>$userAddress->contact_name,
            'contact_phone'=>$userAddress->contact_phone,
        ];
    }
}
