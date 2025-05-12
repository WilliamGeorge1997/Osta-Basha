<?php


namespace Modules\User\DTO;


class UserDetailsDto
{
    public $first_name;
    public $last_name;
    public $email;

    public function __construct($request)
    {
        if ($request->get('first_name'))
            $this->first_name = $request->get('first_name');
        if ($request->get('last_name'))
            $this->last_name = $request->get('last_name');
        if ($request->get('email'))
            $this->email = $request->get('email');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->first_name == null)
            unset($data['first_name']);
        if ($this->last_name == null)
            unset($data['last_name']);
        if ($this->email == null)
            unset($data['email']);
        return $data;
    }
}

