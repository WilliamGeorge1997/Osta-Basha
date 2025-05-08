<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Client\Service\ClientService;
use Modules\Client\App\Http\Requests\ContactProviderRequest;

class ClientController extends Controller
{
    protected $clientService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ClientService $clientService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Client');
        $this->clientService = $clientService;
    }

    public function contactProvider(ContactProviderRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->clientService->contactProvider($request->validated());
            DB::commit();
            return returnMessage(true, 'Client Contacted Provider Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}