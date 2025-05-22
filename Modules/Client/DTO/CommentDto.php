<?php


namespace Modules\Client\DTO;

use Modules\User\App\Models\User;
use Modules\Provider\App\Models\Provider;
use Modules\ShopOwner\App\Models\ShopOwner;

class CommentDto
{
    public $client_id;
    public $comment;
    public $commentable_id;
    public $commentable_type;

    public function __construct($request)
    {
        $this->client_id = auth()->id();
        if ($request->get('comment'))
            $this->comment = $request->get('comment');
        if ($request->get('commentable_id'))
            $this->commentable_id = $request->get('commentable_id');
        $this->commentable_type = $this->getType();
    }

    private function getType()
    {
        $user = User::findOrFail($this->commentable_id);
        return $user->type == User::TYPE_SERVICE_PROVIDER ? Provider::class : ShopOwner::class;
    }
    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->client_id == null)
            unset($data['client_id']);
        if ($this->comment == null)
            unset($data['comment']);
        if ($this->commentable_id == null)
            unset($data['commentable_id']);
        if ($this->commentable_type == null)
            unset($data['rateable_type']);
        return $data;
    }
}

