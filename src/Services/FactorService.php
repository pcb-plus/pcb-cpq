<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Facades\DB;
use PcbPlus\PcbCpq\Concerns\HasFactor;
use PcbPlus\PcbCpq\Concerns\HasProduct;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Factor;
use PcbPlus\PcbCpq\Validators\CreateFactorValidator;
use PcbPlus\PcbCpq\Validators\MultisortFactorsValidator;
use PcbPlus\PcbCpq\Validators\UpdateFactorValidator;

class FactorService
{
    use HasVersion, HasProduct, HasFactor;

    /**
     * @param int $productId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Factor
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function createFactor($productId, $data)
    {
        // Validate the input data
        $validator = new CreateFactorValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Check if the factor already exists
        if ($this->isFactorExists($product->id, $validated['code'])) {
            throw new RuntimeException('Factor already exists');
        }

        // Begin transaction to create factor
        return DB::transaction(function () use ($product, $validated) {
            $factor = Factor::create(array_merge($validated, [
                'product_id' => $product->id
            ]));

            if ($factor->is_optional) {
                foreach ($validated['options'] as $option) {
                    $factor->options()->create($option);
                }
            }

            return $factor;
        });
    }

    /**
     * @param int $factorId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Factor
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function updateFactor($factorId, $data)
    {
        // Validate the input data
        $validator = new UpdateFactorValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $factor = $this->findFactorOrAbort($factorId);
        $product = $this->findProductOrAbort($factor->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Check if the factor already exists
        if ($this->isFactorExists($product->id, $validated['code'], $factor->id)) {
            throw new RuntimeException('Factor already exists');
        }

        // Begin transaction to update the factor
        return DB::transaction(function () use ($factor, $validated) {
            $factor->update($validated);

            $existingOptionIds = $factor->options()->pluck('id');

            if ($factor->is_optional) {
                foreach ($validated['options'] as $option) {
                    if (is_numeric($index = $existingOptionIds->search($option['id']))) {
                        $existingOptionIds->forget($index);
                        $factor->options()->find($option['id'])->update($option);
                    } else {
                        $factor->options()->create($option);
                    }
                }
            }

            foreach ($existingOptionIds as $id) {
                $factor->options()->find($id)->delete();
            }

            return $factor;
        });
    }

    /**
     * @param int $factorId
     * @return bool
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function deleteFactor($factorId)
    {
        // Retrieve the necessary models
        $factor = $this->findFactorOrAbort($factorId);
        $product = $this->findProductOrAbort($factor->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to delete the factor
        return DB::transaction(function () use ($factor) {
            $factor->options()->delete();

            return $factor->delete();
        });
    }

    /**
     * @param int $productId
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function multisortFactors($productId, $data)
    {
        // Validate the input data
        $validator = new MultisortFactorsValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Multi sort factors
        foreach ($validated as $item) {
            $factor = Factor::where('product_id', $productId)
                ->where('id', $item['id'])
                ->first();

            if (! $factor) {
                throw new RuntimeException('Factor ID must be valid');
            }

            $factor->sort_order = $item['sort_order'];

            $factor->save();
        }
    }
}
