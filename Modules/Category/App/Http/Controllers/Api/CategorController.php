<?php

namespace Modules\Category\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Category\DTO\CategoryDto;
use Modules\Category\App\Models\Category;
use Modules\Category\Service\CategoryService;
use Modules\Category\App\resources\CategoryResource;
use Modules\Category\App\Http\Requests\CategoryRequest;
use Modules\Category\App\resources\SubCategoryResource;

class CategorController extends Controller
{
    protected $categoryService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:user');
        $this->categoryService = $categoryService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $categories = $this->categoryService->active($data);
        return returnMessage(true, 'Categories Fetched Successfully', CategoryResource::collection($categories)->response()->getData(true));
    }

}