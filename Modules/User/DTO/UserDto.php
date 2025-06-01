<?php


namespace Modules\User\DTO;

use Illuminate\Support\Facades\Hash;

class UserDto
{
    public $country_code;
    public $phone;
    public $password;

    public function __construct($request) {
        if($request->get('country_code')) $this->country_code = $request->get('country_code');
        if($request->get('phone')) $this->phone = $request->get('phone');
        if($request->get('password')) $this->password = Hash::make($request->get('password'));
    }

    public function dataFromRequest()
    {
        $data =  json_decode(json_encode($this), true);
        if($this->country_code == null) unset($data['country_code']);
        if($this->phone == null) unset($data['phone']);
        if($this->password == null) unset($data['password']);
        // $data['verify_code'] = rand(1000,9999);
        $data['verify_code'] = 9999;
        return $data;
    }
}

