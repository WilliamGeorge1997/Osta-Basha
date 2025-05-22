<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Client\App\Models\Comment;
use Modules\Client\Service\CommentService;

class CommentAdminController extends Controller
{
    protected $commentService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(CommentService $commentService)
    {
        $this->middleware('auth:admin');
        $this->commentService = $commentService;
    }

    public function destroy(Comment $comment)
    {
        DB::beginTransaction();
        try {
            $this->commentService->delete($comment);
            DB::commit();
            return returnMessage(true, 'Comment Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
