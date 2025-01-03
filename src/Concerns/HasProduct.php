<?php

namespace PcbPlus\PcbCpq\Concerns;

use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Product;

trait HasProduct
{
    /**
     * @param int $productId
     * @return \PcbPlus\PcbCpq\Models\Product
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function findProductOrAbort($productId)
    {
        $product = Product::find($productId);

        if (! $product) {
            throw new RuntimeException('Product not found');
        }

        return $product;
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Product $product
     * @return bool
     */
    public function deleteProductAbsolutely($product)
    {
        foreach ($product->leadtimes as $leadtime) {
            $leadtime->options()->delete();

            $leadtime->delete();
        }

        foreach ($product->costs as $cost) {
            foreach ($cost->rules as $rule) {
                $rule->tiers()->delete();

                $rule->delete();
            }

            $cost->delete();
        }

        foreach ($product->factors as $factor) {
            $factor->options()->delete();

            $factor->delete();
        }

        return $product->delete();
    }

    /**
     * @param int $versionId
     * @param string $code
     * @param int|null $exceptId
     * @return bool
     */
    public function isProductExists($versionId, $code, $exceptId = null)
    {
        $query = Product::where('version_id', $versionId)->where('code', $code);

        if (is_numeric($exceptId) && $exceptId > 0) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}
