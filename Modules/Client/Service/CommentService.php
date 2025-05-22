<?php

namespace Modules\Client\Service;

use Modules\Client\App\Models\Comment;



class CommentService
{

    public function create($data)
    {
        $comment = Comment::create($data);
        return $comment;
    }

    public function update($comment, $data)
    {
        $comment->update($data);
        return $comment;
    }

    public function delete($comment)
    {
        $comment->delete();
        return $comment;
    }
}