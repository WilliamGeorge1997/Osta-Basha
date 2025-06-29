<?php

namespace Modules\Client\App\Http\Controllers\Api;

use Modules\Client\DTO\RateDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Client\Service\RateService;
use Modules\Client\App\Models\ClientContact;
use Modules\Client\App\Http\Requests\RateRequest;

class RateController extends Controller
{
    protected $rateService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(RateService $rateService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Client');
        $this->rateService = $rateService;
    }

    public function store(RateRequest $request, ClientContact $clientContact)
    {
        DB::beginTransaction();
        try {
            $data = (new RateDto($request))->dataFromRequest();
            $this->rateService->create($data, $clientContact);
            DB::commit();
            return returnMessage(true, 'Rate Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    public function update(RateRequest $request, ClientContact $clientContact)
    {
        DB::beginTransaction();
        try {
            $this->rateService->update($clientContact, $request->validated());
            DB::commit();
            return returnMessage(true, 'Rate Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(RateRequest $request, ClientContact $clientContact)
    {
        DB::beginTransaction();
        try {
            $this->rateService->delete($clientContact);
            DB::commit();
            return returnMessage(true, 'Rate Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
