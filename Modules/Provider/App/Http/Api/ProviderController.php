<?php

namespace Modules\Provider\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Provider\App\resources\ProviderResource;
use Modules\Client\Service\ClientService;
use Modules\Client\App\Http\Requests\ClientChangePasswordRequest;
use Modules\Client\App\Http\Requests\ClientUpdateProfileRequest;

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
        $this->middleware('auth:provider');
        $this->providerService = $providerService;
    }

    public function changePassword(ClientChangePasswordRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->clientService->changePassword($request->validated());
            DB::commit();
            return returnMessage(true, 'Password Changed Successfully');
        }
        catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,500);
        }
    }

    public function updateProfile(ClientUpdateProfileRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->clientService->updateProfile($request->validated());
            DB::commit();
            return returnMessage(true, 'Profile Updated Successfully', new ClientResource(auth('client')->user()));
        }catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,500);
        }
    }
}