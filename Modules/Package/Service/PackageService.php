<?php

namespace Modules\Package\Service;

use Illuminate\Support\Facades\File;
use Modules\Package\App\Models\Package;
use Modules\Common\Helpers\UploadHelper;

class PackageService
{
    use UploadHelper;

    function findAll($data = [], $relations = [])
    {
        $packages = Package::query()->with($relations)->latest();
        return getCaseCollection($packages, $data);
    }


    function findById($id)
    {
        $package = Package::findOrFail($id);
        return $package;
    }

    function findBy($key, $value, $relations = [])
    {
        return Package::where($key, $value)->with($relations)->get();
    }

    function active($data = [])
    {
        $packages = Package::query()->active()->latest();
        return getCaseCollection($packages, $data);
    }
    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'package');
        }
        $package = Package::create($data);
        return $package->fresh();
    }

    function update($package, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/package/' . $this->getImageName('package', $package->image)));
            $data['image'] = $this->upload(request()->file('image'), 'package');
        }
        $package->update($data);
        return $package->fresh();
    }

    function delete($package)
    {
        if ($package->image) {
            File::delete(public_path('uploads/package/' . $this->getImageName('package', $package->image)));
        }
        $package->delete();
    }

    public function toggleActivate($package)
    {
        $package->update(['is_active' => !$package->is_active]);
        return $package->fresh();
    }
}