<?php

namespace Modules\Admin\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Admin\Service\AdminService;
use Modules\Admin\App\resources\AdminResource;
use Modules\Admin\App\Http\Requests\AdminUpdateProfileRequest;
use Modules\Admin\App\Http\Requests\AdminChangePasswordRequest;

class AdminController extends Controller
{
    protected $adminService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AdminService $adminService)
    {
        $this->middleware('auth:admin');
        $this->adminService = $adminService;
    }

    public function changePassword(AdminChangePasswordRequest $request)
    {
        try{
            DB::beginTransaction();
            $this->adminService->changePassword($request->validated());
            DB::commit();
            return returnMessage(true, 'Password Changed Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,'server_error');
        }
    }
    public function updateProfile(AdminUpdateProfileRequest $request)
    {
        try{
            DB::beginTransaction();
             $this->adminService->updateProfile($request->validated());
            DB::commit();
            return returnMessage(true, 'Profile Updated Successfully', new AdminResource(auth('admin')->user()));
        }catch(\Exception $e){
            DB::rollBack();
            return returnMessage(false, $e->getMessage(),null ,'server_error');
        }
    }
}
