<?php

namespace Modules\Common\Service;

use Modules\Common\App\Models\History;

class HistoryService
{
    public function findAll($data = [])
    {
        $histories = History::query()->available();

        return getCaseCollection($histories, $data);
    }
}
