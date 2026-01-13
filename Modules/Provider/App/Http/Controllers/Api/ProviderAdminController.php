<?php

namespace Modules\Provider\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Provider\Service\ProviderService;
use Modules\Provider\App\resources\ProviderResource;
use Modules\Provider\App\Http\Requests\ProviderAdminRequest;

class ProviderAdminController extends Controller
{
    protected $providerService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ProviderService $providerService)
    {
        $this->middleware('auth:admin');
        $this->providerService = $providerService;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $relations = [
                'providerProfile' => function ($q) {
                    $q->with([
                        'subCategory' => function ($q) {
                            $q->with('category');
                        },
                        'package',
                        'subCategories' => function ($q) {
                            $q->with('category');
                        }
                    ]);
                },
                'providerWorkingTimes',
                'providerCertificates',
                'providerContacts.client',

            ];
            $providers = $this->providerService->findAll($data, $relations);
            return returnMessage(true, 'Providers', ProviderResource::collection($providers)->response()->getData(true));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function updateSubscription(ProviderAdminRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $provider = $this->providerService->updateSubscription($user, $data);
            DB::commit();
            return returnMessage(true, 'Provider updated successfully', $provider);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
