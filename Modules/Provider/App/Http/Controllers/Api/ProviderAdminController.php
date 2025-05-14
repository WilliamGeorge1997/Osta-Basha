<?php

namespace Modules\Provider\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Provider\Service\ProviderService;
use Modules\Provider\App\resources\ProviderResource;

class ProviderAdminController extends Controller
{
    protected $providerService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ProviderService $providerService)
    {
        // $this->middleware('auth:admin');
        $this->providerService = $providerService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = ['providerProfile', 'providerWorkingTimes', 'providerCertificates'];
            $providers = $this->providerService->findAll($data, $relations);
            return returnMessage(true, 'Providers', ProviderResource::collection($providers)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $provider = $this->providerService->update($id, $data);
            DB::commit();
            return returnMessage(true, 'Provider updated successfully', ProviderResource::collection($provider)->response()->getData(true));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
