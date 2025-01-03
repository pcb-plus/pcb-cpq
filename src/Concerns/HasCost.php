<?php

namespace PcbPlus\PcbCpq\Concerns;

use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Cost;

trait HasCost
{
    /**
     * @param int $costId
     * @return \PcbPlus\PcbCpq\Models\Cost
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function findCostOrAbort($costId)
    {
        $cost = Cost::find($costId);

        if (! $cost) {
            throw new RuntimeException('Cost not found');
        }

        return $cost;
    }

    /**
     * @param int $productId
     * @param string $code
     * @param int|null $exceptId
     * @return bool
     */
    public function isCostExists($productId, $code, $exceptId = null)
    {
        $query = Cost::query()
            ->where('product_id', $productId)
            ->where('code', $code);

        if (is_numeric($exceptId) && $exceptId > 0) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}
