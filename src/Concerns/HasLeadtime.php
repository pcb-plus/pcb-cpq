<?php

namespace PcbPlus\PcbCpq\Concerns;

use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Leadtime;

trait HasLeadtime
{
    /**
     * @param int $leadtimeId
     * @return \PcbPlus\PcbCpq\Models\Leadtime
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function findLeadtimeOrAbort($leadtimeId)
    {
        $leadtime = Leadtime::find($leadtimeId);

        if (! $leadtime) {
            throw new RuntimeException('Leadtime not found');
        }

        return $leadtime;
    }
}
