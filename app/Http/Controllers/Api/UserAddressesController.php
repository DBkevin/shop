<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Transformers\UserAddressesTransformer;
use App\Http\Requests\Api\UserAddressesRequest;

class UserAddressesController extends Controller
{
    //
    public function index()
    {

        $user = $this->user();

        $addresses = UserAddress::where('user_id', $user->id)
            ->orderBy('last_user_at', 'desc')
            ->get();
        return $this->response->collection($addresses, new UserAddressesTransformer());
    }

    public function store(UserAddressesRequest $request)
    {
        $this->user()->addresses()->create(
            $request->only([
                'province',
                'city',
                'district',
                'address',
                'zip',
                'contact_name',
                'contact_phone',
            ])
        );
        return $this->response->noContent()->setStatusCode(201);
    }

    public function update(UserAddressesRequest $request,UserAddress $UserAddress)
    {
        $this->authorize('own', $UserAddress);
        $data = $request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]);
        $UserAddress->update($data);
        return $this->response->noContent();
    }

    public function destroy(UserAddress $UserAddress){
        $this->authorize('own',$UserAddress);
        $UserAddress->delete();

        return $this->response->noContent();
    }
}
