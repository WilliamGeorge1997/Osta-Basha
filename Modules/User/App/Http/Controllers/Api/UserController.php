<?php

namespace Modules\User\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\User\Service\UserService;
use Modules\User\App\resources\UserResource;
use Modules\User\App\resources\UserSearchResource;
use Modules\User\App\Http\Requests\UserUpdateProfileRequest;
use Modules\User\App\Http\Requests\UserChangePasswordRequest;

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
        $this->middleware('auth:user')->except(['search']);
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

    public function search(Request $request)
    {
        $data = $request->all();
        $users = $this->userService->search($data);
        return returnMessage(true, 'Users', UserSearchResource::collection($users)->response()->getData(true));
    }
}