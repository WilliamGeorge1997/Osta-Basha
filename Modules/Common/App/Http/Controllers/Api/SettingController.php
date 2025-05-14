<?php

namespace Modules\Common\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Common\Service\SettingService;
use Modules\Common\App\Http\Requests\SettingRequest;

class SettingController extends Controller
{
    protected $settingService;
    public function __construct(SettingService $settingService)
    {
        $this->middleware('auth:admin');
        $this->settingService = $settingService;
    }

    public function index()
    {
        try {
            $settings = $this->settingService->findAll();
            return returnMessage(true, 'Setting fetched successfully', $settings);
        } catch (\Exception $e) {
            return returnMessage(false, 'Setting fetch failed', $e->getMessage());
        }
    }

    public function update(SettingRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $this->settingService->update($data['settings']);
            DB::commit();
            return returnMessage(true, 'Setting updated successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, 'Setting update failed', $e->getMessage());
        }
    }
}
