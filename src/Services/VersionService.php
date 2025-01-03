<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PcbPlus\PcbCpq\Concerns\HasProduct;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Version;
use PcbPlus\PcbCpq\Validators\CreateVersionValidator;
use PcbPlus\PcbCpq\Validators\UpdateVersionValidator;

class VersionService
{
    use HasVersion, HasProduct;

    /**
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function createVersion($data)
    {
        // Validate the input data
        $validator = new CreateVersionValidator();
        $validated = $validator->validate($data);

        // Create version
        return Version::create(array_merge($validated, [
            'number' => Str::uuid()->toString(),
            'is_submitted' => false,
            'is_active' => false,
        ]));
    }

    /**
     * @param int $versionId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function updateVersion($versionId, $data)
    {
        // Validate the input data
        $validator = new UpdateVersionValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Update the version
        $version->update($validated);

        return $version;
    }

    /**
     * @param int $versionId
     * @return bool
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function deleteVersion($versionId)
    {
        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is deletable
        $this->validateVersionIsDeletable($version);

        // Begin transaction to delete the version
        return DB::transaction(function () use ($version) {
            foreach ($version->products as $product) {
                $this->deleteProductAbsolutely($product);
            }

            return $version->delete();
        });
    }

    /**
     * @param int $versionId
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function submitVersion($versionId)
    {
        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is submittable
        $this->validateVersionIsSubmittable($version);

        // Submit the version
        $version->update(['is_submitted' => true]);

        return $version;
    }

    /**
     * @param int $versionId
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function unsubmitVersion($versionId)
    {
        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is unsubmittable
        $this->validateVersionIsUnsubmittable($version);

        // unsubmit the version
        $version->update(['is_submitted' => false]);

        return $version;
    }

    /**
     * @param int $versionId
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function activateVersion($versionId)
    {
        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is activable
        $this->validateVersionIsActivable($version);

        // Begin transaction to activate the version
        return DB::transaction(function () use ($version) {
            Version::query()->update(['is_active' => false]);

            $version->update(['is_active' => true]);

            return $version;
        });
    }

    /**
     * @param int $versionId
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function replicateVersion($versionId)
    {
        // Retrieve the necessary models
        $oldVersion = Version::with([
            'products.factors.options',
            'products.costs.rules.tiers',
            'products.leadtimes.options',
        ])->find($versionId);

        if (! $oldVersion) {
            throw new RuntimeException('Version not found');
        }

        // Begin transaction to replicate the version
        return DB::transaction(function () use ($oldVersion) {
            $newVersion = $oldVersion->replicate();
            $newVersion->name = $oldVersion->name . '(copy)';
            $newVersion->number = Str::uuid()->toString();
            $newVersion->is_submitted = false;
            $newVersion->is_active = false;
            $newVersion->copy_id = $oldVersion->id;
            $newVersion->save();

            foreach ($oldVersion->products as $oldProduct) {
                $newProduct = $oldProduct->replicate();
                $newProduct->version_id = $newVersion->id;
                $newProduct->copy_id = $oldProduct->id;
                $newProduct->save();

                foreach ($oldProduct->factors as $oldFactor) {
                    $newFactor = $oldFactor->replicate();
                    $newFactor->product_id = $newProduct->id;
                    $newFactor->copy_id = $oldFactor->id;
                    $newFactor->save();

                    foreach ($oldFactor->options as $oldFactorOption) {
                        $newFactorOption = $oldFactorOption->replicate();
                        $newFactorOption->factor_id = $newFactor->id;
                        $newFactorOption->copy_id = $oldFactorOption->id;
                        $newFactorOption->save();
                    }
                }

                foreach ($oldProduct->costs as $oldCost) {
                    $newCost = $oldCost->replicate();
                    $newCost->product_id = $newProduct->id;
                    $newCost->copy_id = $oldCost->id;
                    $newCost->save();

                    foreach ($oldCost->rules as $oldRule) {
                        $newRule = $oldRule->replicate();
                        $newRule->cost_id = $newCost->id;
                        $newRule->copy_id = $oldRule->id;
                        $newRule->save();

                        foreach ($oldRule->tiers as $oldTier) {
                            $newTier = $oldTier->replicate();
                            $newTier->rule_id = $newRule->id;
                            $newTier->copy_id = $oldTier->id;
                            $newTier->save();
                        }
                    }
                }

                foreach ($oldProduct->leadtimes as $oldLeadtime) {
                    $newLeadtime = $oldLeadtime->replicate();
                    $newLeadtime->product_id = $newProduct->id;
                    $newLeadtime->copy_id = $oldLeadtime->id;
                    $newLeadtime->save();

                    foreach ($oldLeadtime->options as $oldLeadtimeOption) {
                        $newLeadtimeOption = $oldLeadtimeOption->replicate();
                        $newLeadtimeOption->leadtime_id = $newLeadtime->id;
                        $newLeadtimeOption->copy_id = $oldLeadtimeOption->id;
                        $newLeadtimeOption->save();
                    }
                }
            }

            return $newVersion;
        });
    }

    /**
     * @param int $versionId
     * @return \PcbPlus\PcbCpq\Models\Version|null
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function aggregateVersion($versionId)
    {
        return Version::with([
            'products' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.factors' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.factors.options' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.costs' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.costs.rules' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.costs.rules.tiers' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.leadtimes' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'products.leadtimes.options' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
        ])->find($versionId);
    }
}
