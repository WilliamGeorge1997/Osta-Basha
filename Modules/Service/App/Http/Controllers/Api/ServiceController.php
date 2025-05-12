<?php

namespace Modules\Service\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Service\Service\ServiceService;
use Modules\Service\App\resources\ServiceResource;

class ServiceController extends Controller
{
    protected $serviceService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ServiceService $serviceService)
    {
        $this->middleware('auth:user');
        $this->serviceService = $serviceService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['user', 'user.providerProfile', 'user.providerWorkingTimes', 'user.providerCertificates', 'subCategory'];
        $services = $this->serviceService->active($data, $relations);
        return returnMessage(true, 'Services Fetched Successfully', ServiceResource::collection($services)->response()->getData(true));
    }
}
