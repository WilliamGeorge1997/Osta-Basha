<?php

namespace Modules\Category\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Category\DTO\CategoryLocalizationDto;
use Modules\Category\App\Models\CategoryLocalization;
use Modules\Category\Service\CategoryLocalizationService;
use Modules\Category\App\resources\CategoryLocalizationResource;
use Modules\Category\App\Http\Requests\CategoryLocalizationRequest;

class CategoryLocalizationAdminController extends Controller
{
    protected $localizationService;

    public function __construct(CategoryLocalizationService $localizationService)
    {
        $this->middleware('auth:admin');
        $this->localizationService = $localizationService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['category', 'country'];
        $localizations = $this->localizationService->findAll($data, $relations);
        return returnMessage(true, 'Category Localizations Fetched Successfully', CategoryLocalizationResource::collection($localizations)->response()->getData(true));
    }

    public function byCategoryId(Request $request, $categoryId)
    {
        $data = $request->all();
        $relations = ['country'];
        $localizations = $this->localizationService->findByCategory($categoryId, $data, $relations);
        return returnMessage(true, 'Category Localizations Fetched Successfully', CategoryLocalizationResource::collection($localizations)->response()->getData(true));
    }

    public function store(CategoryLocalizationRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new CategoryLocalizationDto($request))->dataFromRequest();
            $localizations = $this->localizationService->create($data);
            DB::commit();
            return returnMessage(true, 'Category Localizations Created Successfully', CategoryLocalizationResource::collection($localizations));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function show(CategoryLocalization $categoryLocalization)
    {
        $categoryLocalization->load(['category', 'country']);
        return returnMessage(true, 'Category Localization Fetched Successfully', new CategoryLocalizationResource($categoryLocalization));
    }

    public function update(CategoryLocalizationRequest $request, CategoryLocalization $categoryLocalization)
    {
        try {
            DB::beginTransaction();
            $data = (new CategoryLocalizationDto($request))->dataFromRequest();

            // Remove country_ids since we're updating a specific localization
            if (isset($data['country_ids'])) {
                unset($data['country_ids']);
            }

            $localization = $this->localizationService->update($categoryLocalization, $data);
            DB::commit();
            return returnMessage(true, 'Category Localization Updated Successfully', new CategoryLocalizationResource($localization));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(CategoryLocalization $categoryLocalization)
    {
        try {
            DB::beginTransaction();
            $this->localizationService->delete($categoryLocalization);
            DB::commit();
            return returnMessage(true, 'Category Localization Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function toggleActivate(CategoryLocalization $categoryLocalization)
    {
        try {
            DB::beginTransaction();
            $localization = $this->localizationService->toggleActivate($categoryLocalization);
            DB::commit();
            return returnMessage(true, 'Category Localization updated successfully', new CategoryLocalizationResource($localization));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}