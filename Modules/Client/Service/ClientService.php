<?php

namespace Modules\Client\Service;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Client\App\Models\Client;
use Modules\Common\Helpers\UploadHelper;
use Modules\Client\App\Emails\ClientEmailVerification;

class ClientService
{
    use UploadHelper;

    function findById($id){
        return Client::find($id);
    }
    function findBy($key, $value)
    {
        return Client::where($key, $value)->get();
    }
    public function create($data)
    {
        if(request()->hasFile('image')){
            $data['image'] = $this->upload(request()->file('image'), 'client');
        }
        $client = Client::create($data);
        // Mail::to($data['email'])->send(new ClientEmailVerification($data['verify_code']));
        return $client;
    }

    public function verifyOtp($data)
    {
        $client = $this->findBy('phone', $data['phone'])[0];
        if ($client && $client->verify_code == $data['otp']) {
            return $this->update($client->id, ['is_active' => 1]);
        }
        return false;
    }

    function update($id, $data)
    {
        $client = $this->findById($id);
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/Client/' . $this->getImageName('client', $client->image)));
            $data['image'] = $this->upload(request()->file('image'), 'client');
        }
        $client->update($data);
        return $client;
    }

    public function changePassword($data)
    {
        $client = auth('client')->user();
        $client->update([
            'password' => Hash::make($data['new_password'])
        ]);
    }

    public function updateProfile($data)
    {
        $client = auth('client')->user();
        if (request()->hasFile('image')) {
            if ($client->image) {
                File::delete(public_path('uploads/client/' . $this->getImageName('client', $client->image)));
            }
            $data['image'] = $this->upload(request()->file('image'), 'client');
        }
        $client->update($data);
    }
}