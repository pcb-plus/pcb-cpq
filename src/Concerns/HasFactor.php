<?php

namespace PcbPlus\PcbCpq\Concerns;

use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Factor;

trait HasFactor
{
    /**
     * @param int $factorId
     * @return \PcbPlus\PcbCpq\Models\Factor
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function findFactorOrAbort($factorId)
    {
        $factor = Factor::find($factorId);

        if (! $factor) {
            throw new RuntimeException('Factor not found');
        }

        return $factor;
    }

    /**
     * @param int $productId
     * @param string $code
     * @param int|null $exceptId
     * @return bool
     */
    public function isFactorExists($productId, $code, $exceptId = null)
    {
        $query = Factor::query()
            ->where('product_id', $productId)
            ->where('code', $code);

        if (is_numeric($exceptId) && $exceptId > 0) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Factor $factor
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateFactorIsOptional($factor)
    {
        if (! $factor->is_optional) {
            throw new RuntimeException('Factor must be optional');
        }
    }
}
