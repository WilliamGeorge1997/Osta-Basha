<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Client\Service\CommentService;
use Modules\Client\App\Models\ClientContact;

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

    public function destroy(ClientContact $clientContact)
    {
        DB::beginTransaction();
        try {
            $this->commentService->delete($clientContact);
            DB::commit();
            return returnMessage(true, 'Comment Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
