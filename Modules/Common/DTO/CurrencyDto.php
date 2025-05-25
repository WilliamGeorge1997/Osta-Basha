<?php


namespace Modules\Common\DTO;

class CurrencyDto
{
    public $title;

    public function __construct($request)
    {
        if ($request->get('currency_title'))
            $this->title = $request->get('currency_title');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);

        return $data;
    }
}

