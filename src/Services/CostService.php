<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Facades\DB;
use PcbPlus\PcbCpq\Concerns\HasCost;
use PcbPlus\PcbCpq\Concerns\HasProduct;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Cost;
use PcbPlus\PcbCpq\Validators\CreateCostValidator;
use PcbPlus\PcbCpq\Validators\MultisortCostsValidator;
use PcbPlus\PcbCpq\Validators\UpdateCostValidator;

class CostService
{
    use HasVersion, HasProduct, HasCost;

    /**
     * @param int $productId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Cost
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function createCost($productId, $data)
    {
        // Validate the input data
        $validator = new CreateCostValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Check if the cost already exists
        if ($this->isCostExists($product->id, $validated['code'])) {
            throw new RuntimeException('Cost already exists');
        }

        // Create cost
        return Cost::create(array_merge($validated, [
            'product_id' => $product->id
        ]));
    }

    /**
     * @param int $costId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Cost
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function updateCost($costId, $data)
    {
        // Validate the input data
        $validator = new UpdateCostValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $cost = $this->findCostOrAbort($costId);
        $product = $this->findProductOrAbort($cost->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Check if the cost already exists
        if ($this->isCostExists($product->id, $validated['code'], $cost->id)) {
            throw new RuntimeException('Cost already exists');
        }

        // Update the cost
        $cost->update($validated);

        return $cost;
    }

    /**
     * @param int $costId
     * @return bool
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function deleteCost($costId)
    {
        // Retrieve the necessary models
        $cost = $this->findCostOrAbort($costId);
        $product = $this->findProductOrAbort($cost->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to delete the cost
        return DB::transaction(function () use ($cost) {
            foreach ($cost->rules as $rule) {
                $rule->tiers()->delete();

                $rule->delete();
            }

            return $cost->delete();
        });
    }

    /**
     * @param int $productId
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function multisortCosts($productId, $data)
    {
        // Validate the input data
        $validator = new MultisortCostsValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Multi sort costs
        foreach ($validated as $item) {
            $cost = Cost::where('product_id', $productId)
                ->where('id', $item['id'])
                ->first();

            if (! $cost) {
                throw new RuntimeException('Cost ID must be valid');
            }

            $cost->sort_order = $item['sort_order'];

            $cost->save();
        }
    }
}
