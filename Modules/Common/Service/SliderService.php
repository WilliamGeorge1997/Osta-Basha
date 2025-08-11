<?php

namespace Modules\Common\Service;

use Illuminate\Support\Facades\File;
use Modules\Common\App\Models\Slider;
use Modules\Common\Helpers\UploadHelper;

class SliderService
{
    use UploadHelper;

    function findAll($data = [], $relations = [])
    {
        $sliders = Slider::query()->with($relations)->latest();
        return getCaseCollection($sliders, $data);
    }

    function findById($id)
    {
        $slider = Slider::findOrFail($id);
        return $slider;
    }

    function findBy($key, $value, $relations = [])
    {
        return Slider::where($key, $value)->with($relations)->get();
    }

    function active($data = [], $relations = [])
    {
        $sliders = Slider::query()
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1)
                    ->where('is_available', 1)
                    ->where(function ($q) {
                        $q->where(function ($query) {
                            $query->where('type', 'service_provider')
                                ->whereHas('providerProfile', function ($subquery) {
                                    $subquery->where('is_active', 1);
                                });
                        })
                        ->orWhere(function ($query) {
                            $query->where('type', 'shop_owner')
                                ->whereHas('shopOwnerProfile', function ($subquery) {
                                    $subquery->where('is_active', 1);
                                });
                        });
                    });
            })
            ->active()->with($relations)->latest();
        return getCaseCollection($sliders, $data);
    }
    public function create($data)
    {
        if (request()->hasFile('image_ar')) {
            $data['image_ar'] = $this->upload(request()->file('image_ar'), 'slider');
        }
        if (request()->hasFile('image_en')) {
            $data['image_en'] = $this->upload(request()->file('image_en'), 'slider');
        }
        $slider = Slider::create($data);
        return $slider;
    }

    function update($slider, $data)
    {
        if (request()->hasFile('image_ar')) {
            File::delete(public_path('uploads/slider/' . $this->getImageName('slider', $slider->image_ar)));
            $data['image_ar'] = $this->upload(request()->file('image_ar'), 'slider');
        }
        if (request()->hasFile('image_en')) {
            File::delete(public_path('uploads/slider/' . $this->getImageName('slider', $slider->image_en)));
            $data['image_en'] = $this->upload(request()->file('image_en'), 'slider');
        }
        $slider->update($data);
        return $slider->fresh();
    }

    function delete($slider)
    {
        if ($slider->image) {
            File::delete(public_path('uploads/slider/' . $this->getImageName('slider', $slider->image)));
        }
        $slider->delete();
    }

    public function toggleActivate($slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);
        return $slider->fresh();
    }
}