<?php

namespace Modules\Provider\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Common\Helpers\ArabicNumeralsConverterTrait;

class ProviderWorkingTimeResource extends JsonResource
{
    use ArabicNumeralsConverterTrait;
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data = [
            "id" => $this->id,
            "day" => $this->day,
            "start_at" => $this->start_at,
            "end_at" => $this->end_at
        ];

        return $this->convertNumericToArabic($data, [
            'start_at',
            'end_at'
        ]);
    }
}
