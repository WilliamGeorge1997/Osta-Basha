<?php

namespace Modules\ShopOwner\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\ShopOwner\Service\ShopOwnerService;
use Modules\ShopOwner\App\resources\ShopOwnerResource;
use Modules\ShopOwner\App\Http\Requests\ShopOwnerAdminRequest;

class ShopOwnerAdminController extends Controller
{

    protected $shopOwnerService;
    public function __construct(ShopOwnerService $shopOwnerService)
    {
        $this->middleware('auth:admin');
        $this->shopOwnerService = $shopOwnerService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = [
                'shopOwnerProfile' => function ($q) {
                    $q->with([
                        'subCategory' => function ($q) {
                            $q->with('category');
                        }
                    ]);
                },
                'shopOwnerWorkingTimes',
                'shopOwnerShopImages',
                'shopOwnerContacts.client',
                'package'
            ];
            $shopOwners = $this->shopOwnerService->findAll($data, $relations);
            return returnMessage(true, 'Shop Owners fetched successfully', ShopOwnerResource::collection($shopOwners)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), 'server_error');
        }
    }
    public function updateSubscription(ShopOwnerAdminRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $provider = $this->shopOwnerService->updateSubscription($user, $data);
            DB::commit();
            return returnMessage(true, 'Provider updated successfully', $provider);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
