<?php


namespace Modules\Provider\DTO;

class ProviderWorkingTime
{
    public $user_id;
    public $day;
    public $start_at;
    public $end_at;

    public function __construct($request)
    {
        if ($request->get('user_id'))
            $this->user_id = $request->get('user_id');
        if ($request->get('day'))
            $this->day = $request->get('day');
        if ($request->get('start_at'))
            $this->start_at = $request->get('start_at');
        if ($request->get('end_at'))
            $this->end_at = $request->get('end_at');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->user_id == null)
            unset($data['user_id']);
        if ($this->day == null)
            unset($data['day']);
        if ($this->start_at == null)
            unset($data['start_at']);
        if ($this->end_at == null)
            unset($data['end_at']);
        return $data;
    }
}

