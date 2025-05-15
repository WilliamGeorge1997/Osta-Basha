<?php

namespace Modules\Client\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Client\App\resources\ClientResource;
use Modules\Client\Service\ClientService;

class ClientAdminController extends Controller
{

    protected $clientService;
    public function __construct(ClientService $clientService)
    {
        $this->middleware('auth:admin');
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = [];
            $clients = $this->clientService->findAll($data, $relations);
            return returnMessage(true, 'Clients fetched successfully', ClientResource::collection($clients)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), 'server_error');
        }
    }

}
