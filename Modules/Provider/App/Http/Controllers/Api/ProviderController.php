<?php

namespace Modules\Provider\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Provider\Service\ProviderService;
use Modules\Provider\App\resources\ProviderResource;

class ProviderController extends Controller
{
    protected $providerService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = ['providerProfile', 'providerWorkingTimes', 'providerCertificates'];
            $providers = $this->providerService->active($data, $relations);
            return returnMessage(true, 'Providers', ProviderResource::collection($providers)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    public function mostContactedProviders(Request $request)
    {
        try {
            $data = $request->all();
            $relations = ['providerProfile', 'providerWorkingTimes', 'providerCertificates'];
            $providers = $this->providerService->mostContactedProviders($data, $relations);
            return returnMessage(true, 'Most Contacted Providers', ProviderResource::collection($providers)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}