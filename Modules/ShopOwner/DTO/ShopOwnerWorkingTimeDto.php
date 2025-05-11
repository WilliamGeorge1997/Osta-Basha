<?php

namespace Modules\ShopOwner\DTO;

class ShopOwnerWorkingTimeDto
{
    public $user_id;
    public $working_times = [];

    public function __construct($request, $user_id)
    {
        $this->user_id = $user_id;
        if ($request->has('working_times')) {
            $this->working_times = $request->get('working_times');
        }
    }

    public function dataFromRequest()
    {
        $workingTimesData = [];

        if (!empty($this->working_times)) {
            foreach ($this->working_times as $time) {
                $workingTimesData[] = [
                    'user_id' => $this->user_id,
                    'day' => $time['day'] ?? null,
                    'start_at' => $time['start_at'] ?? null,
                    'end_at' => $time['end_at'] ?? null
                ];
            }
        }

        return $workingTimesData;
    }
}