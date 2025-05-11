<?php

namespace Modules\User\App\Http\Controllers\Api;

use Modules\User\DTO\UserDto;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\User\DTO\UserDetailsDto;
use Modules\Provider\DTO\ProviderDto;
use Modules\User\Service\UserService;
use Modules\ShopOwner\DTO\ShopOwnerDto;
use Modules\User\App\resources\UserResource;
use Modules\Provider\DTO\ProviderWorkingTimeDto;
use Modules\ShopOwner\DTO\ShopOwnerWorkingTimeDto;
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
        $this->middleware('auth:user', ['except' => ['login', 'register', 'verifyOtp', 'checkPhoneExists', 'loginOrRegister']]);
        $this->userService = $userService;

    }

    public function loginOrRegister(UserLoginOrRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('phone', $request->phone)->first();
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
            $data = (new UserDto($request))->dataFromRequest();
            $this->userService->create($data);
            DB::commit();
            return returnMessage(false, 'User Registered Successfully', null);
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
            if ($user && !$user->type == null) {
                return returnMessage(false, 'User Already Completed Registration', null, 'unprocessable_entity');
            }
            $userDetailsData = (new UserDetailsDto($request))->dataFromRequest();
            $type = $userDetailsData['type'];
            $profileData = null;
            $workingTimesData = null;
            if ($type == 'service_provider') {
                $profileData = (new ProviderDto($request, $user->id))->dataFromRequest();
                $workingTimesData = (new ProviderWorkingTimeDto($request, $user->id))->dataFromRequest();
            } else if ($type == 'shop_owner') {
                $profileData = (new ShopOwnerDto($request, $user->id))->dataFromRequest();
                $workingTimesData = (new ShopOwnerWorkingTimeDto($request, $user->id))->dataFromRequest();
            }
            $user = $this->userService->completeRegistration($type, $user, $userDetailsData, $profileData, $workingTimesData);
            DB::commit();
            return returnMessage(true, 'User Completed Registration Successfully', new UserResource($user));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
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
        return returnMessage(true, 'User Data', new UserResource(auth('user')->user()));
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
        $authUser = auth('user')->user();
        if ($authUser->type == null) {
            $status = 'non_authoritative_information';
        }
        return returnMessage(
            true,
            'Successfully Logged in',
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('user')->factory()->getTTL() * 60,
                'user' => new UserResource($authUser),
            ],
            $status
        );
    }

}
