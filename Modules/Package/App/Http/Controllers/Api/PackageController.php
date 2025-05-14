<?php

namespace Modules\Package\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Package\Service\PackageService;
use Modules\Package\App\resources\PackageResource;

class PackageController extends Controller
{
    protected $packageService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $packages = $this->packageService->active($data);
        return returnMessage(true, 'Packages Fetched Successfully', PackageResource::collection($packages)->response()->getData(true));
    }

}