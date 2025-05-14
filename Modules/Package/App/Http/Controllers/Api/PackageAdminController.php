<?php

namespace Modules\Package\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Package\DTO\PackageDto;
use Modules\Package\App\Models\Package;
use Modules\Package\Service\PackageService;
use Modules\Package\App\resources\PackageResource;
use Modules\Package\App\Http\Requests\PackageRequest;

class PackageAdminController extends Controller
{
    protected $packageService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(PackageService $packageService)
    {
        // $this->middleware('auth:admin');
        $this->packageService = $packageService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = [];
        $packages = $this->packageService->findAll($data, $relations);
        return returnMessage(true, 'Packages Fetched Successfully', PackageResource::collection($packages)->response()->getData(true));
    }

    public function store(PackageRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new PackageDto($request))->dataFromRequest();
            $package = $this->packageService->create($data);
            DB::commit();
            return returnMessage(true, 'Package Created Successfully', new PackageResource($package));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(PackageRequest $request, Package $package)
    {
        try {
            DB::beginTransaction();
            $data = (new PackageDto($request))->dataFromRequest();
            $package = $this->packageService->update($package, $data);
            DB::commit();
            return returnMessage(true, 'Package Updated Successfully', new PackageResource($package));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(Package $package)
    {
        try {
            DB::beginTransaction();
            $this->packageService->delete($package);
            DB::commit();
            return returnMessage(true, 'Package Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function toggleActivate(Package $package)
    {
        try {
            DB::beginTransaction();
            $package = $this->packageService->toggleActivate($package);
            DB::commit();
            return returnMessage(true, 'Package updated successfully', new PackageResource($package));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
