<?php

namespace Modules\Country\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Country\DTO\CountryDto;
use App\Http\Controllers\Controller;
use Modules\Country\App\Models\Country;
use Modules\Country\Service\CountryService;
use Modules\Country\App\resources\CountryResource;
use Modules\Country\App\Http\Requests\CountryRequest;

class CountryAdminController extends Controller
{
    protected $countryService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(CountryService $countryService)
    {
        $this->middleware('auth:admin');
        $this->countryService = $countryService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = [];
        $countries = $this->countryService->findAll($data, $relations);
        return returnMessage(true, 'Countries Fetched Successfully', CountryResource::collection($countries)->response()->getData(true));
    }

    public function store(CountryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new CountryDto($request))->dataFromRequest();
            $country = $this->countryService->create($data);
            DB::commit();
            return returnMessage(true, 'Country Created Successfully', $country);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(CountryRequest $request, Country $country)
    {
        try {
            DB::beginTransaction();
            $data = (new CountryDto($request))->dataFromRequest();
            $country = $this->countryService->update($country, $data);
            DB::commit();
            return returnMessage(true, 'Country Updated Successfully', $country);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(Country $country)
    {
        try {
            DB::beginTransaction();
            $this->countryService->delete($country);
            DB::commit();
            return returnMessage(true, 'Country Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function toggleActivate(Country $country)
    {
        try {
            DB::beginTransaction();
            $country = $this->countryService->toggleActivate($country);
            DB::commit();
            return returnMessage(true, 'Country updated successfully', new CountryResource($country));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}