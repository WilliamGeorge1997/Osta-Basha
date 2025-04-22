<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Modules\Client\DTO\ClientDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Client\App\Models\Client;
use Laravel\Socialite\Facades\Socialite;
use Modules\Client\Service\ClientService;
use Modules\Client\App\resources\ClientResource;
use Modules\Client\App\Http\Requests\ClientLoginRequest;
use Modules\Client\App\Http\Requests\ClientVerifyRequest;
use Modules\Client\App\Http\Requests\ClientRegisterRequest;
use Modules\Client\App\Http\Requests\CheckPhoneExistsRequest;


class ClientAuthController extends Controller
{
    protected $clientService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ClientService $clientService)
    {
        $this->middleware('auth:client', ['except' => ['login', 'register', 'verifyOtp', 'checkPhoneExists']]);
        $this->clientService = $clientService;

    }

    public function register(ClientRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = (new ClientDto($request))->dataFromRequest();
            $this->clientService->create($data);
            DB::commit();
            return returnMessage(true, 'Client Registered Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function verifyOtp(ClientVerifyRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $result = $this->clientService->verifyOtp($data);
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
    public function login(ClientLoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if (!$token = auth('client')->attempt($credentials)) {
                return returnValidationMessage(false, 'Unauthorized', ['password' => 'Wrong Credentials'], 'unauthorized');
            }
            if (auth('client')->user()['is_active'] == 0) {
                return returnMessage(false, 'In-Active Client Verification Required', null, 'temporary_redirect');
            }
            if ($request['fcm_token'] ?? null) {
                auth('client')->user()->update(['fcm_token' => $request->fcm_token]);
            }
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }


    public function checkPhoneExists(CheckPhoneExistsRequest $request)
    {
        $client = Client::where('phone', $request->phone)->first();
        if ($client) {
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
        return returnMessage(true, 'Client Data', new ClientResource(auth('client')->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('client')->logout();
        return returnMessage(true, 'Successfully logged out', null);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('client')->refresh());
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
            'expires_in' => auth('client')->factory()->getTTL() * 60,
            'user' => new ClientResource(auth('client')->user()),
        ]);
    }

}
