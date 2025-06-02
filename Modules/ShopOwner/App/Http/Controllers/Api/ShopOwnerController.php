<?php

namespace Modules\ShopOwner\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ShopOwner\Service\ShopOwnerService;
use Modules\ShopOwner\App\resources\ShopOwnerResource;

class ShopOwnerController extends Controller
{
    protected $shopOwnerService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ShopOwnerService $shopOwnerService)
    {
        $this->shopOwnerService = $shopOwnerService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = ['shopOwnerProfile' => function ($q) {
                $q->with(['subCategory' => function ($q) {
                    $q->with('category');
                }, 'package']);
            }, 'shopOwnerWorkingTimes', 'shopOwnerShopImages', 'shopOwnerContacts.client'];
            $shopOwners = $this->shopOwnerService->active($data, $relations);
            return returnMessage(true, 'Shop Owners', ShopOwnerResource::collection($shopOwners)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}