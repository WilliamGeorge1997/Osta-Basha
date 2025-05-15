<?php

namespace Modules\ShopOwner\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ShopOwner\App\resources\ShopOwnerResource;
use Modules\ShopOwner\Service\ShopOwnerService;

class ShopOwnerAdminController extends Controller
{

    protected $shopOwnerService;
    public function __construct(ShopOwnerService $shopOwnerService)
    {
        // $this->middleware('auth:admin');
        $this->shopOwnerService = $shopOwnerService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = ['shopOwnerProfile', 'shopOwnerWorkingTimes', 'shopOwnerShopImages'];
            $shopOwners = $this->shopOwnerService->findAll($data, $relations);
            return returnMessage(true, 'Shop Owners fetched successfully', ShopOwnerResource::collection($shopOwners)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), 'server_error');
        }
    }

}
