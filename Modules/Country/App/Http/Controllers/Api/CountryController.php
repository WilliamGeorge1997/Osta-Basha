<?php

namespace Modules\Country\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Country\Service\CountryService;
use Modules\Country\App\resources\CountryResource;
class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = [];
        $countries = $this->countryService->active($data, $relations);
        return returnMessage(true, 'Countries Fetched Successfully', CountryResource::collection($countries)->response()->getData(true));
    }

}