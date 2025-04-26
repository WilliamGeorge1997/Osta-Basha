<?php

namespace Modules\Service\Service;

use Illuminate\Support\Facades\File;
use Modules\Service\App\Models\Service;
use Modules\Common\Helpers\UploadHelper;

class ServiceService
{
    use UploadHelper;

    function findAll($data)
    {
        $services = Service::all();
        return getCaseCollection($services, $data);
    }
    function findById($id)
    {
        return Service::find($id);
    }
    function findBy($key, $value)
    {
        return Service::where($key, $value)->get();
    }
    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'provider');
        }
        $service = Service::create($data);
        return $service;
    }

    function update($service, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/Service/' . $this->getImageName('service', $service->image)));
            $data['image'] = $this->upload(request()->file('image'), 'service');
        }
        $service->update($data);
        return $service;
    }

    function delete($service)
    {
        File::delete(public_path('uploads/Service/' . $this->getImageName('service', $service->image)));
        $service->delete();
    }

    function activate($service)
    {
        $service->is_active = !$service->is_active;
        $service->save();
    }
    function providerServices($data)
    {
        $services = Service::where('provider_id', auth('provider')->id())->orderByDesc('id');
        return getCaseCollection($services, $data);
    }
}