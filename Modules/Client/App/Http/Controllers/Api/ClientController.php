<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Client\Service\ClientService;
use Modules\Client\App\Http\Requests\ClientContactRequest;
use Modules\Client\App\resources\ClientContactResource;

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

    public function clientContact(ClientContactRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->clientService->clientContact($request->validated());
            DB::commit();
            return returnMessage(true, 'Client Contacted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function clientContactList()
    {
        $relations = ['user'];
        $clientContact = $this->clientService->clientContactList($relations);
        return returnMessage(true, 'Client Contact List Fetched Successfully', ClientContactResource::collection($clientContact));
    }

}