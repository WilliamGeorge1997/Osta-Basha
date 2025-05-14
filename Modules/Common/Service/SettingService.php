<?php

namespace Modules\Common\Service;

use Modules\Common\App\Models\Setting;

class SettingService
{
    public function findAll($data = [])
    {
        return Setting::query()->latest()->get();
    }

    public function create($data)
    {
        $setting = Setting::create($data);
        return $setting;
    }

    public function update($settings)
    {
        if (is_array($settings)) {
            $results = [];
            foreach ($settings as $key => $setting) {
                $dbSetting = Setting::where('key', $setting['key'])->first();
                if ($dbSetting) {
                    $dbSetting->value = $setting['value'];
                    $results[$key] = $dbSetting->save();
                }
            }
            return $results;
        }
    }

    public function delete($setting)
    {
        return $setting->delete();
    }
}
