<?php

namespace Modules\Category\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Category\DTO\SubCategoryDto;
use Modules\Category\App\Models\SubCategory;
use Modules\Category\Service\SubCategoryService;
use Modules\Category\App\resources\SubCategoryResource;
use Modules\Category\App\Http\Requests\SubCategoryRequest;

class SubCategoryAdminController extends Controller
{
    protected $subCategoryService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(SubCategoryService $subCategoryService)
    {
        $this->middleware('auth:admin');
        $this->subCategoryService = $subCategoryService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['category'];
        $subCategories = $this->subCategoryService->findAll($data, $relations);
        return returnMessage(true, 'Sub-Categories Fetched Successfully', SubCategoryResource::collection($subCategories)->response()->getData(true));
    }

    public function store(SubCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new SubCategoryDto($request))->dataFromRequest();
            $subCategory = $this->subCategoryService->create($data);
            DB::commit();
            return returnMessage(true, 'Sub-Category Created Successfully', $subCategory);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(SubCategoryRequest $request, SubCategory $subCategory)
    {
        try {
            DB::beginTransaction();
            $data = (new SubCategoryDto($request))->dataFromRequest();
            $subCategory = $this->subCategoryService->update($subCategory, $data);
            DB::commit();
            return returnMessage(true, 'Sub-Category Updated Successfully', $subCategory);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(SubCategory $subCategory)
    {
        try {
            DB::beginTransaction();
            $this->subCategoryService->delete($subCategory);
            DB::commit();
            return returnMessage(true, 'Sub-Category Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function toggleActivate(SubCategory $subCategory)
    {
        try {
            DB::beginTransaction();
            $subCategory = $this->subCategoryService->toggleActivate($subCategory);
            DB::commit();
            return returnMessage(true, 'Sub-Category updated successfully', new SubCategoryResource($subCategory));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}