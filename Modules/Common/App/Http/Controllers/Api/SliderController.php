<?php

namespace Modules\Common\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Common\Service\SliderService;
use Modules\Common\App\resources\SliderResource;

class SliderController extends Controller
{
    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['user',];
        $sliders = $this->sliderService->active($data, $relations);
        return returnMessage(true, 'Sliders Fetched Successfully', SliderResource::collection($sliders)->response()->getData(true));
    }

}