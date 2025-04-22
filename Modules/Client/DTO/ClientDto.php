<?php


namespace Modules\Client\DTO;

use Illuminate\Support\Facades\Hash;

class ClientDto
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;

    public function __construct($request) {
        if($request->get('first_name')) $this->first_name = $request->get('first_name');
        if($request->get('last_name')) $this->last_name = $request->get('last_name');
        if($request->get('email')) $this->email = $request->get('email');
        if($request->get('phone')) $this->phone = $request->get('phone');
        if($request->get('password')) $this->password = Hash::make($request->get('password'));
    }

    public function dataFromRequest()
    {
        $data =  json_decode(json_encode($this), true);
        if($this->first_name == null) unset($data['first_name']);
        if($this->last_name == null) unset($data['last_name']);
        if($this->email == null) unset($data['email']);
        if($this->phone == null) unset($data['phone']);
        if($this->password == null) unset($data['password']);
        // $data['verify_code'] = rand(1000,9999);
        $data['verify_code'] = 9999;
        return $data;
    }
}

