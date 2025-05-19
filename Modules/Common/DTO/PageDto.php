<?php


namespace Modules\Common\DTO;

class PageDto
{
    public $page;
    public $title;
    public $description;

    public function __construct($request)
    {
        if ($request->get('title'))
            $this->title = $request->get('title');
        if ($request->get('description'))
            $this->description = $request->get('description');
        if ($request->get('page'))
            $this->page = $request->get('page');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->title == null)
            unset($data['title']);
        if ($this->description == null)
            unset($data['description']);
        if ($this->page == null)
            unset($data['page']);
        return $data;
    }
}

