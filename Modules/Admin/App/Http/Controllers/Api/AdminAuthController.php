<?php

namespace Modules\Admin\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Admin\App\Http\Requests\AdminLoginRequest;
use Modules\Admin\App\resources\AdminResource;


class AdminAuthController extends Controller
{
   /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AdminLoginRequest $request)
    {
        try{
            $credentials = $request->validated();

            if (! $token = auth('admin')->attempt($credentials)) {
                return returnValidationMessage(false,'Unauthorized',['password'=>'Wrong Credentials'],'unauthorized');
            }

            if (auth('admin')->user()['is_active'] == 0) {
                return returnMessage(false, 'In-Active Admin Verification Required', null, 'temporary_redirect');
            }
            if ($request['fcm_token'] ?? null) {
                auth('admin')->user()->update(['fcm_token' => $request->fcm_token]);
            }
            return $this->respondWithToken($token);

        }catch(\Exception $e){
            return returnMessage(false,$e->getMessage(),null,'server_error');
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return returnMessage(true,'Admin Data', new AdminResource(auth('admin')->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin')->logout();
        return returnMessage(true,'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('admin')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return returnMessage(true,'Successfully Logged in',[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
            'user' => new AdminResource(auth('admin')->user()),
        ]);
    }
}
