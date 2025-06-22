<?php

namespace Modules\User\App\Http\Controllers\Api;

use Carbon\Carbon;
use Modules\User\DTO\UserDto;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\User\DTO\UserDetailsDto;
use Modules\Provider\DTO\ProviderDto;
use Modules\User\Service\UserService;
use Modules\Common\App\Models\Setting;
use Modules\ShopOwner\DTO\ShopOwnerDto;
use Modules\User\App\resources\UserResource;
use Modules\Provider\DTO\ProviderWorkingTimeDto;
use Modules\ShopOwner\DTO\ShopOwnerWorkingTimeDto;
use Modules\User\App\Http\Requests\UserChooseType;
use Modules\User\App\Http\Requests\UserVerifyRequest;
use Modules\User\App\Http\Requests\UserLoginOrRegisterRequest;
use Modules\User\App\Http\Requests\UserCompleteRegistrationRequest;


class UserAuthController extends Controller
{
    protected $userService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:user', ['except' => ['login', 'register', 'verifyOtp', 'checkPhoneExists', 'loginOrRegister', 'chooseUserType']]);
        $this->userService = $userService;

    }

    public function loginOrRegister(UserLoginOrRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('phone', $request->phone)->where('country_code', $request->country_code)->first();
            if ($user) {
                $credentials = $request->validated();
                if (!$token = auth('user')->attempt($credentials)) {
                    return returnMessage(false, 'Unauthorized', ['password' => 'Wrong Credentials'], 'created');
                }
                if (auth('user')->user()['is_active'] == 0) {
                    return returnMessage(false, 'In-Active User Verification Required', null);
                }
                if ($request['fcm_token'] ?? null) {
                    auth('user')->user()->update(['fcm_token' => $request->fcm_token]);
                }
                DB::commit();
                return $this->respondWithToken($token);
            }
            $existingUser = User::where('phone', $request->phone)->first();
            if ($existingUser) {
                return returnValidationMessage(false, trans('validation.rules_failed'), ['phone' => 'This phone number is already registered'], 'unprocessable_entity');
            }
            $data = (new UserDto($request))->dataFromRequest();
            $this->userService->create($data);
            DB::commit();
            return returnMessage(true, 'User Registered Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function chooseUserType(UserChooseType $request)
    {
        DB::beginTransaction();
        try {
            $user = auth('user')->user();
            if ($user && $user->type !== null) {
                return returnMessage(false, 'User type already set', null, 'unprocessable_entity');
            }
            $data = $request->validated();
            $user = $this->userService->chooseUserType($data, $user);
            DB::commit();
            return $this->respondWithToken(auth('user')->login($user));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function completeRegistration(UserCompleteRegistrationRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth('user')->user();
            $user_id = $user->id;
            $userDetailsData = (new UserDetailsDto($request))->dataFromRequest();
            $type = $user->type;
            $profileData = null;
            $workingTimesData = null;
            if ($type == User::TYPE_SERVICE_PROVIDER) {
                $profileData = (new ProviderDto($request))->dataFromRequest();
                $workingTimesData = (new ProviderWorkingTimeDto($request, $user_id))->dataFromRequest();
            } else if ($type == User::TYPE_SHOP_OWNER) {
                $profileData = (new ShopOwnerDto($request))->dataFromRequest();
                $workingTimesData = (new ShopOwnerWorkingTimeDto($request, $user_id))->dataFromRequest();
            }
            if ($profileData != null) {
                $profileData['user_id'] = $user_id;
                $profileData['start_date'] = Carbon::now()->toDateString();
                $freeTrialMonths = $this->getFreeTrialMonths();
                $profileData['end_date'] = Carbon::now()->addMonths($freeTrialMonths)->toDateString();
                $profileData['is_active'] = 1;
            }
            $user = $this->userService->completeRegistration($type, $user, $userDetailsData, $profileData, $workingTimesData);
            DB::commit();
            return returnMessage(true, 'User Registered Successfully', new UserResource($user));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    private function getFreeTrialMonths()
    {
        $setting = Setting::where('key', 'free_trial_months')->first();
        return $setting ? (int) $setting->value : 3;
    }

    public function verifyOtp(UserVerifyRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $user = $this->userService->verifyOtp($data);
            if ($user == false) {
                return returnMessage(false, 'Wrong OTP', null, 'unprocessable_entity');
            }
            $token = auth('user')->login($user);
            if ($request['fcm_token'] ?? null) {
                auth('user')->user()->update(['fcm_token' => $request->fcm_token]);
            }
            DB::commit();
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('user')->user();

        if ($user->type == 'service_provider') {
            $user->load(['providerProfile.package', 'providerWorkingTimes', 'providerCertificates', 'providerContacts.client']);
        } elseif ($user->type == 'shop_owner') {
            $user->load(['shopOwnerProfile.package', 'shopOwnerWorkingTimes', 'shopOwnerShopImages', 'shopOwnerContacts.client']);
        }

        return returnMessage(true, 'User Data', new UserResource($user));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('user')->logout();
        return returnMessage(true, 'Successfully logged out', null);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('user')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $status = 'ok';
        $user = auth('user')->user();
        if ($user->type == null) {
            $status = 'non_authoritative_information';
        }
        if ($user->type == User::TYPE_SERVICE_PROVIDER) {
            $user->load(['providerProfile.subCategory.category', 'providerWorkingTimes', 'providerCertificates', 'providerProfile.package', 'providerContacts.client']);
        } elseif ($user->type == User::TYPE_SHOP_OWNER) {
            $user->load(['shopOwnerProfile.subCategory.category', 'shopOwnerWorkingTimes', 'shopOwnerShopImages', 'shopOwnerProfile.package', 'shopOwnerContacts.client']);
        }
        return returnMessage(
            true,
            'Successfully Logged in',
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('user')->factory()->getTTL() * 60,
                'user' => new UserResource($user),
            ],
            $status
        );
    }

}
