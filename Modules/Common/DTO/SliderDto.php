<?php


namespace Modules\Common\DTO;

class SliderDto
{
    public $image_ar;
    public $image_en;
    public $user_id;

    public function __construct($request)
    {
        if ($request->get('image_ar'))
            $this->image_ar = $request->get('image_ar');
        if ($request->get('image_en'))
            $this->image_en = $request->get('image_en');
        if ($request->get('user_id'))
            $this->user_id = $request->get('user_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->image_ar == null)
            unset($data['image_ar']);
        if ($this->image_en == null)
            unset($data['image_en']);
        if ($this->user_id == null)
            unset($data['user_id']);
        return $data;
    }
}

