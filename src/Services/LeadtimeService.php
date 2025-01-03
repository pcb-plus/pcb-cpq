<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Facades\DB;
use PcbPlus\PcbCpq\Concerns\HasLeadtime;
use PcbPlus\PcbCpq\Concerns\HasProduct;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Leadtime;
use PcbPlus\PcbCpq\Validators\CreateLeadtimeValidator;
use PcbPlus\PcbCpq\Validators\MultisortLeadtimesValidator;
use PcbPlus\PcbCpq\Validators\UpdateLeadtimeValidator;

class LeadtimeService
{
    use HasVersion, HasProduct, HasLeadtime;

    /**
     * @param int $productId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Leadtime
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function createLeadtime($productId, $data)
    {
        // Validate the input data
        $validator = new CreateLeadtimeValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to create leadtime
        return DB::transaction(function () use ($product, $validated) {
            $leadtime = Leadtime::create(array_merge($validated, [
                'product_id' => $product->id
            ]));

            foreach ($validated['options'] as $option) {
                $leadtime->options()->create($option);
            }

            return $leadtime;
        });
    }

    /**
     * @param int $leadtimeId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Leadtime
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function updateLeadtime($leadtimeId, $data)
    {
        // Validate the input data
        $validator = new UpdateLeadtimeValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $leadtime = $this->findLeadtimeOrAbort($leadtimeId);
        $product = $this->findProductOrAbort($leadtime->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to update the leadtime
        return DB::transaction(function () use ($leadtime, $validated) {
            $leadtime->update($validated);

            $existingOptionIds = $leadtime->options()->pluck('id');

            foreach ($validated['options'] as $option) {
                if (is_numeric($index = $existingOptionIds->search($option['id']))) {
                    $existingOptionIds->forget($index);
                    $leadtime->options()->find($option['id'])->update($option);
                } else {
                    $leadtime->options()->create($option);
                }
            }

            foreach ($existingOptionIds as $id) {
                $leadtime->options()->find($id)->delete();
            }

            return $leadtime;
        });
    }

    /**
     * @param int $leadtimeId
     * @return bool
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function deleteLeadtime($leadtimeId)
    {
        // Retrieve the necessary models
        $leadtime = $this->findLeadtimeOrAbort($leadtimeId);
        $product = $this->findProductOrAbort($leadtime->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to delete the leadtime
        return DB::transaction(function () use ($leadtime) {
            $leadtime->options()->delete();

            return $leadtime->delete();
        });
    }

    /**
     * @param int $productId
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function multisortLeadtimes($productId, $data)
    {
        // Validate the input data
        $validator = new MultisortLeadtimesValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Multi sort leadtimes
        foreach ($validated as $item) {
            $leadtime = Leadtime::where('product_id', $productId)
                ->where('id', $item['id'])
                ->first();

            if (! $leadtime) {
                throw new RuntimeException('Leadtime ID must be valid');
            }

            $leadtime->sort_order = $item['sort_order'];

            $leadtime->save();
        }
    }
}
