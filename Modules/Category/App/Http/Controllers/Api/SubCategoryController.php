<?php

namespace Modules\Category\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Category\App\Models\Category;
use Modules\Category\Service\SubCategoryService;
use Modules\Category\App\resources\CategoryResource;
use Modules\Category\App\resources\SubCategoryResource;

class SubCategoryController extends Controller
{
    protected $subCategoryService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(SubCategoryService $subCategoryService)
    {
        // $this->middleware('auth:user');
        $this->subCategoryService = $subCategoryService;
    }
    public function index(Request $request, Category $category)
    {
        $data = $request->all();
        $relations = [];
        $subCategories = $this->subCategoryService->active($category, $data, $relations);
        return returnMessage(true, 'Sub-Categories Fetched Successfully', SubCategoryResource::collection($subCategories)->response()->getData(true));
    }

}