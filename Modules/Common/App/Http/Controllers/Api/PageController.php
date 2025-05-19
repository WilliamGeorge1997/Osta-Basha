<?php

namespace Modules\Common\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Common\DTO\PageDto;
use Illuminate\Support\Facades\DB;
use Modules\Common\App\Models\Page;
use App\Http\Controllers\Controller;
use Modules\Common\Service\PageService;
use Modules\Common\App\resources\PageResource;
use Modules\Common\App\Http\Requests\PageRequest;

class PageController extends Controller
{
    protected $pageService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function findPage(Request $request)
    {
        try {
            $data = $request->all();
            $page = $this->pageService->findByPage($data);
            if (!$page) {
                return returnMessage(false, 'Page not found', null, 'not_found');
            }
            return returnMessage(true, 'Page Fetched Successfully', new PageResource($page));
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}