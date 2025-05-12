<?php

namespace Modules\Service\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Service\DTO\ServiceDto;
use App\Http\Controllers\Controller;
use Modules\Service\App\Models\Service;
use Modules\Service\Service\ServiceService;
use Modules\Service\App\resources\ServiceResource;
use Modules\Service\App\Http\Requests\ServiceRequest;

class ServiceProviderController extends Controller
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
        $this->middleware('role:Service Provider');
        $this->serviceService = $serviceService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $services = $this->serviceService->providerServices($data);
        return returnMessage(true, 'Services Fetched Successfully', ServiceResource::collection($services)->response()->getData(true));
    }

    public function show(ServiceRequest $request, Service $service)
    {
        return returnMessage(true, 'Service Fetched Successfully', new ServiceResource($service));
    }

    public function store(ServiceRequest $request)
    {
        $userId = auth('user')->id();
        $service = $this->serviceService->findBy('user_id', $userId);
        if ($service->count() > 0) {
            return returnMessage(false, 'You already have a service', null, 'bad_request');
        }
        DB::beginTransaction();
        try {
            $data = (new ServiceDto($request))->dataFromRequest();
            $data['user_id'] = $userId;
            $service = $this->serviceService->create($data);
            DB::commit();
            return returnMessage(true, 'Service Created Successfully', $service);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function update(ServiceRequest $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $data = (new ServiceDto($request))->dataFromRequest();
            $service = $this->serviceService->update($service, $data);
            DB::commit();
            return returnMessage(true, 'Service Updated Successfully', $service);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy(ServiceRequest $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $this->serviceService->delete($service);
            DB::commit();
            return returnMessage(true, 'Service Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }
}
