<?php

namespace Modules\User\App\Http\Controllers\Api;

use Modules\Shop\DTO\ShopDto;
use Modules\User\DTO\UserDto;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Service\DTO\ServiceDto;
use App\Http\Controllers\Controller;
use Modules\Shop\Service\ShopService;
use Modules\User\Service\UserService;
use Modules\Service\Service\ServiceService;
use Modules\User\Validation\UserValidation;
use Modules\User\App\Http\Requests\UserLoginRequest;
use Modules\User\App\Http\Requests\UserVerifyRequest;
use Modules\User\App\Http\Requests\UserRegisterRequest;
use Modules\User\App\Http\Requests\CheckPhoneExistsRequest;
use Modules\User\App\Http\Requests\UserLoginOrRegisterRequest;


class UserAuthController extends Controller
{
    use UserValidation;
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
            $validator = $this->validateUserRegister($request->all());
            if ($validator->fails()) {
                return returnValidationMessage(false, 'Validation Error', $validator->errors(), 'unprocessable_entity');
            }
            $data = (new UserDto($request))->dataFromRequest();
            $user = $this->userService->create($data);
            if ($user->type == 'service_provider') {
                $serviceData = (new ServiceDto($request))->dataFromRequest();
                (new ServiceService())->create($serviceData);
            } else if ($user->type == 'shop_owner') {
                $shopData = (new ShopDto($request))->dataFromRequest();
                (new ShopService())->create($shopData);
            }
            DB::commit();
            return returnMessage(false, 'User Registered Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function register(UserRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = (new UserDto($request))->dataFromRequest();
            $this->userService->create($data);
            DB::commit();
            return returnMessage(true, 'User Registered Successfully', null);
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
            $result = $this->userService->verifyOtp($data);
            if ($result == false) {
                return returnMessage(false, 'Wrong OTP', null, 'unprocessable_entity');
            }
            DB::commit();
            return returnMessage(true, 'Phone Number Verified Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if (!$token = auth('user')->attempt($credentials)) {
                return returnValidationMessage(false, 'Unauthorized', ['password' => 'Wrong Credentials'], 'unauthorized');
            }
            if (auth('user')->user()['is_active'] == 0) {
                return returnMessage(false, 'In-Active User Verification Required', null, 'temporary_redirect');
            }
            if ($request['fcm_token'] ?? null) {
                auth('user')->user()->update(['fcm_token' => $request->fcm_token]);
            }
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }


    public function checkPhoneExists(CheckPhoneExistsRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            return returnMessage(true, 'Phone Number Exists', null, 'success');
        }
        return returnMessage(false, 'Phone Number Does Not Exist', null, 'unprocessable_entity');
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
        return returnMessage(true, 'Successfully Logged in', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('user')->factory()->getTTL() * 60,
            'user' => new UserResource(auth('user')->user()),
        ]);
    }

}
