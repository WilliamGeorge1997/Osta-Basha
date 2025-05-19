<?php

namespace Modules\User\App\Http\Controllers\Api;

use Modules\User\App\Models\User;
use App\Http\Controllers\Controller;
use Modules\User\App\resources\UserResource;
use Modules\User\Service\UserService;

class UserAdminController extends Controller
{
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:admin');
        $this->userService = $userService;
    }

    public function toggleActivate(User $user)
    {
        try {
            $user = $this->userService->toggleActivate($user);
            return returnMessage(true, "User updated successfully", new UserResource($user));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}