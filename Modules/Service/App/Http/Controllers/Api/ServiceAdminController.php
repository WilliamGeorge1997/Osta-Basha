<?php

namespace Modules\Service\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Service\App\Models\Service;
use Modules\Service\DTO\ServiceAdminDto;
use Modules\Service\Service\ServiceService;
use Modules\Service\App\resources\ServiceResource;
use Modules\Service\App\Http\Requests\ServiceAdminRequest;

class ServiceAdminController extends Controller
{
    protected $serviceService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ServiceService $serviceService)
    {
        $this->middleware('auth:admin');
        $this->serviceService = $serviceService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['user', 'subCategory'];
        $services = $this->serviceService->findAll($data, $relations);
        return returnMessage(true, 'Services Fetched Successfully', ServiceResource::collection($services)->response()->getData(true));
    }

    public function show(ServiceAdminRequest $request, Service $service)
    {
        return returnMessage(true, 'Service Fetched Successfully', new ServiceResource($service));
    }

    public function update(ServiceAdminRequest $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $data = (new ServiceAdminDto($request))->dataFromRequest();
            $service = $this->serviceService->update($service, $data);
            DB::commit();
            return returnMessage(true, 'Service Updated Successfully', $service);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(ServiceAdminRequest $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $this->serviceService->delete($service);
            DB::commit();
            return returnMessage(true, 'Service Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function toggleActivate(Service $service)
    {
        try {
            DB::beginTransaction();
            $service = $this->serviceService->toggleActivate($service);
            DB::commit();
            return returnMessage(true, 'Service updated successfully', new ServiceResource($service));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
