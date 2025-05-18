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
        $sliders = Slider::query()->active()->with($relations)->latest();
        return getCaseCollection($sliders, $data);
    }
    public function create($data)
    {
        if (request()->hasFile('image')) {
            $data['image'] = $this->upload(request()->file('image'), 'slider');
        }
        $slider = Slider::create($data);
        return $slider;
    }

    function update($slider, $data)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/slider/' . $this->getImageName('slider', $slider->image)));
            $data['image'] = $this->upload(request()->file('image'), 'slider');
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