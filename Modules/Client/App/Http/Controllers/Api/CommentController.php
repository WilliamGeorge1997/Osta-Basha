<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Modules\Client\DTO\CommentDto;
use App\Http\Controllers\Controller;
use Modules\Client\App\Models\Comment;
use Modules\Client\Service\CommentService;
use Modules\Client\App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    protected $commentService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(CommentService $commentService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Client');
        $this->commentService = $commentService;
    }

    public function store(CommentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = (new CommentDto($request))->dataFromRequest();
            $this->commentService->create($data);
            DB::commit();
            return returnMessage(true, 'Comment Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(CommentRequest $request, Comment $comment)
    {
        DB::beginTransaction();
        try {
            $this->commentService->update($comment, $request->validated());
            DB::commit();
            return returnMessage(true, 'Comment Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(CommentRequest $request, Comment $comment)
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