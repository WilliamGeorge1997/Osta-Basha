<?php

namespace Modules\User\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\User\App\resources\UserResource;
use Modules\User\Service\UserService;
use Modules\User\App\Http\Requests\UserChangePasswordRequest;
use Modules\User\App\Http\Requests\UserUpdateProfileRequest;

class UserController extends Controller
{
    protected $userService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:user');
        $this->userService = $userService;
    }

    public function changePassword(UserChangePasswordRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->userService->changePassword($request->validated());
            DB::commit();
            return returnMessage(true, 'Password Changed Successfully');
        }
        catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,500);
        }
    }

    public function updateProfile(UserUpdateProfileRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->userService->updateProfile($request->validated());
            DB::commit();
            return returnMessage(true, 'Profile Updated Successfully', new UserResource(auth('user')->user()));
        }catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,500);
        }
    }
}