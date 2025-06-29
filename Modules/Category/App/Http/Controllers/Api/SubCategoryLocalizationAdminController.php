<?php

namespace Modules\Category\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Category\DTO\CategoryLocalizationDto;
use Modules\Category\App\Models\SubCategoryLocalization;
use Modules\Category\Service\SubCategoryLocalizationService;
use Modules\Category\App\resources\SubCategoryLocalizationResource;
use Modules\Category\App\Http\Requests\SubCategoryLocalizationRequest;

class SubCategoryLocalizationAdminController extends Controller
{
    protected $localizationService;

    public function __construct(SubCategoryLocalizationService $localizationService)
    {
        $this->middleware('auth:admin');
        $this->localizationService = $localizationService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['subCategory', 'country'];
        $localizations = $this->localizationService->findAll($data, $relations);
        return returnMessage(true, 'Sub Category Localizations Fetched Successfully', SubCategoryLocalizationResource::collection($localizations)->response()->getData(true));
    }

    public function bySubCategoryId(Request $request, $subCategoryId)
    {
        $data = $request->all();
        $relations = ['country'];
        $localizations = $this->localizationService->findBySubCategory($subCategoryId, $data, $relations);
        return returnMessage(true, 'Sub Category Localizations Fetched Successfully', SubCategoryLocalizationResource::collection($localizations)->response()->getData(true));
    }

    public function store(SubCategoryLocalizationRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new CategoryLocalizationDto($request))->dataFromRequest();
            $localizations = $this->localizationService->create($data);
            DB::commit();
            return returnMessage(true, 'Sub Category Localizations Created Successfully', SubCategoryLocalizationResource::collection($localizations));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function show(SubCategoryLocalization $subCategoryLocalization)
    {
        $subCategoryLocalization->load(['subCategory', 'country']);
        return returnMessage(true, 'Sub Category Localization Fetched Successfully', new SubCategoryLocalizationResource($subCategoryLocalization));
    }

    public function update(SubCategoryLocalizationRequest $request, SubCategoryLocalization $subCategoryLocalization)
    {
        try {
            DB::beginTransaction();
            $data = (new CategoryLocalizationDto($request))->dataFromRequest();

            // Remove country_ids since we're updating a specific localization
            if (isset($data['country_ids'])) {
                unset($data['country_ids']);
            }

            $localization = $this->localizationService->update($subCategoryLocalization, $data);
            DB::commit();
            return returnMessage(true, 'Sub Category Localization Updated Successfully', new SubCategoryLocalizationResource($localization));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(SubCategoryLocalization $subCategoryLocalization)
    {
        try {
            DB::beginTransaction();
            $this->localizationService->delete($subCategoryLocalization);
            DB::commit();
            return returnMessage(true, 'Sub Category Localization Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function toggleActivate(SubCategoryLocalization $subCategoryLocalization)
    {
        try {
            DB::beginTransaction();
            $localization = $this->localizationService->toggleActivate($subCategoryLocalization);
            DB::commit();
            return returnMessage(true, 'Sub Category Localization updated successfully', new SubCategoryLocalizationResource($localization));
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}