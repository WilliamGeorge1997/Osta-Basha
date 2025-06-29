<?php

namespace Modules\User\App\Http\Controllers\Api;

use Illuminate\Http\Request;
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
use Modules\User\App\resources\UserSearchResource;
use Modules\User\App\Http\Requests\UserUpdateLocation;
use Modules\User\App\Http\Requests\UserDeleteImageRequest;
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
        $this->middleware('role:Service Provider|Shop Owner')->only('deleteImage', 'toggleAvailable', 'receivedContacts');
        $this->userService = $userService;
    }

    public function changePassword(UserChangePasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->userService->changePassword($request->validated());
            DB::commit();
            return returnMessage(true, 'Password Changed Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function updateProfile(UserUpdateProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth('user')->user();
            $userDetailsData = (new UserDetailsDto($request))->dataFromRequest();
            $user_id = $user->id;
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
            $user = $this->userService->updateProfile($type, $user, $userDetailsData, $profileData, $workingTimesData);
            DB::commit();
            return returnMessage(true, 'Profile Updated Successfully', new UserResource($user));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function deleteImage(UserDeleteImageRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $this->userService->deleteImage($id);
            DB::commit();
            if (!$user) {
                return returnMessage(false, 'Image not found', null);
            }
            return returnMessage(true, 'Image deleted successfully', new UserResource($user));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $users = $this->userService->search($data);
        return returnMessage(true, 'Users', UserSearchResource::collection($users)->response()->getData(true));
    }

    public function toggleAvailable(Request $request)
    {
        try {
            $user = $this->userService->toggleAvailable();
            return returnMessage(true, "User updated successfully", new UserResource($user));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function receivedContacts(Request $request)
    {
        try {
            $data = $request->all();
            $contacts = $this->userService->getReceivedContacts($data);
            return returnMessage(true, 'Contacts Received', $contacts);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function updateLocation(UserUpdateLocation $request)
    {
        try {
            $user = $this->userService->updateLocation($request->validated());
            return returnMessage(true, 'Location Updated Successfully', new UserResource($user));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}