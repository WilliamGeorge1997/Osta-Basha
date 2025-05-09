<?php

namespace Modules\Provider\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Provider\Service\ProviderService;
use Modules\Provider\App\resources\ProviderResource;
use Modules\Provider\App\Http\Requests\ProviderUpdateProfileRequest;
use Modules\Provider\App\Http\Requests\ProviderChangePasswordRequest;

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
        $this->middleware('auth:user');
        $this->providerService = $providerService;
    }

    public function mostContactedProviders(Request $request)
    {
        try {
            $data = $request->all();
            $relations = ['provider', 'service'];
            $providers = $this->providerService->mostContactedProviders($data, $relations);
            return returnMessage(true, 'Most Contacted Providers', $providers);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }


    public function changePassword(ProviderChangePasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->providerService->changePassword($request->validated());
            DB::commit();
            return returnMessage(true, 'Password Changed Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function updateProfile(ProviderUpdateProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->providerService->updateProfile($request->validated());
            DB::commit();
            return returnMessage(true, 'Profile Updated Successfully', new ProviderResource(auth('provider')->user()));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}