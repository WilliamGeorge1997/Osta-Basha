<?php

namespace Modules\Provider\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Provider\DTO\ProviderDto;
use Modules\Provider\App\Models\Provider;
use Modules\Provider\Service\ProviderService;
use Modules\Provider\App\resources\ProviderResource;
use Modules\Provider\App\Http\Requests\ProviderLoginRequest;
use Modules\Provider\App\Http\Requests\ProviderVerifyRequest;
use Modules\Provider\App\Http\Requests\ProviderRegisterRequest;
use Modules\Provider\App\Http\Requests\ProviderLoginOrRegisterRequest;
use Modules\Provider\App\Http\Requests\CheckProviderPhoneExistsRequest;


class ProviderAuthController extends Controller
{
    protected $providerService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ProviderService $providerService)
    {
        $this->middleware('auth:provider', ['except' => ['login', 'register', 'verifyOtp', 'checkPhoneExists', 'loginOrRegister']]);
        $this->providerService = $providerService;

    }
    public function loginOrRegister(ProviderLoginOrRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $provider = Provider::where('phone', $request->phone)->first();
            if ($provider) {
                $credentials = $request->validated();
                if (!$token = auth('provider')->attempt($credentials)) {
                    return returnMessage(false, 'Unauthorized', ['password' => 'Wrong Credentials'], 'created');
                }
                if (auth('provider')->user()['is_active'] == 0) {
                    return returnMessage(false, 'In-Active Provider Verification Required', null);
                }
                if ($request['fcm_token'] ?? null) {
                    auth('provider')->user()->update(['fcm_token' => $request->fcm_token]);
                }
                DB::commit();
                return $this->respondWithToken($token);
            }
            $data = (new ProviderDto($request))->dataFromRequest();
            $this->providerService->create($data);
            DB::commit();
            return returnMessage(false, 'Provider Registered Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    public function register(ProviderRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = (new ProviderDto($request))->dataFromRequest();
            $this->providerService->create($data);
            DB::commit();
            return returnMessage(true, 'Provider Registered Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function verifyOtp(ProviderVerifyRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $result = $this->providerService->verifyOtp($data);
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
    public function login(ProviderLoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if (!$token = auth('provider')->attempt($credentials)) {
                return returnValidationMessage(false, 'Unauthorized', ['password' => 'Wrong Credentials'], 'unauthorized');
            }
            if (auth('provider')->user()['is_active'] == 0) {
                return returnMessage(false, 'In-Active Provider Verification Required', null, 'temporary_redirect');
            }
            if ($request['fcm_token'] ?? null) {
                auth('provider')->user()->update(['fcm_token' => $request->fcm_token]);
            }
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }


    public function checkPhoneExists(CheckProviderPhoneExistsRequest $request)
    {
        $provider = Provider::where('phone', $request->phone)->first();
        if ($provider) {
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
        return returnMessage(true, 'Provider Data', new ProviderResource(auth('provider')->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('provider')->logout();
        return returnMessage(true, 'Successfully logged out', null);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('provider')->refresh());
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
            'expires_in' => auth('provider')->factory()->getTTL() * 60,
            'user' => new ProviderResource(auth('provider')->user()),
        ]);
    }

}
