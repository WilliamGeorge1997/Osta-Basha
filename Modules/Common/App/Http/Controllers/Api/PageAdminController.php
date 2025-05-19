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

class PageAdminController extends Controller
{
    protected $pageService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(PageService $pageService)
    {
        $this->middleware('auth:admin');
        $this->pageService = $pageService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = [];
        $pages = $this->pageService->findAll($data, $relations);
        return returnMessage(true, 'Pages Fetched Successfully', PageResource::collection($pages)->response()->getData(true));
    }

    public function store(PageRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new PageDto($request))->dataFromRequest();
            $page = $this->pageService->create($data);
            DB::commit();
            return returnMessage(true, 'Page Created Successfully', $page);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(PageRequest $request, Page $page)
    {
        try {
            DB::beginTransaction();
            $data = (new PageDto($request))->dataFromRequest();
            $page = $this->pageService->update($page, $data);
            DB::commit();
            return returnMessage(true, 'Page Updated Successfully', $page);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(Page $page)
    {
        try {
            DB::beginTransaction();
            $this->pageService->delete($page);
            DB::commit();
            return returnMessage(true, 'Page Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}