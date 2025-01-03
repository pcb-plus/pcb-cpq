<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Facades\DB;
use PcbPlus\PcbCpq\Concerns\HasCost;
use PcbPlus\PcbCpq\Concerns\HasProduct;
use PcbPlus\PcbCpq\Concerns\HasRule;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Rule;
use PcbPlus\PcbCpq\Validators\CreateRuleValidator;
use PcbPlus\PcbCpq\Validators\MultisortRulesValidator;
use PcbPlus\PcbCpq\Validators\UpdateRuleValidator;

class RuleService
{
    use HasVersion, HasProduct, HasCost, HasRule;

    /**
     * @param int $costId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Rule
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function createRule($costId, $data)
    {
        // Validate the input data
        $validator = new CreateRuleValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $cost = $this->findCostOrAbort($costId);
        $product = $this->findProductOrAbort($cost->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to create rule
        return DB::transaction(function () use ($cost, $validated) {
            $rule = Rule::create(array_merge($validated, [
                'cost_id' => $cost->id
            ]));

            if ($rule->is_tiered) {
                foreach ($validated['tiers'] as $tier) {
                    $rule->tiers()->create($tier);
                }
            }

            return $rule;
        });
    }

    /**
     * @param int $ruleId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Rule
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function updateRule($ruleId, $data)
    {
        // Validate the input data
        $validator = new UpdateRuleValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $rule = $this->findRuleOrAbort($ruleId);
        $cost = $this->findCostOrAbort($rule->cost_id);
        $product = $this->findProductOrAbort($cost->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to update rule
        return DB::transaction(function () use ($rule, $validated) {
            $rule->update($validated);

            $existingTierIds = $rule->tiers()->pluck('id');

            if ($rule->is_tiered) {
                foreach ($$validated['tiers'] as $tier) {
                    if (is_numeric($index = $existingTierIds->search($tier['id']))) {
                        $existingTierIds->forget($index);
                        $rule->tiers()->find($tier['id'])->update($tier);
                    } else {
                        $rule->tiers()->create($tier);
                    }
                }
            }

            foreach ($existingTierIds as $id) {
                $rule->tiers()->find($id)->delete();
            }

            return $rule;
        });
    }

    /**
     * @param int $ruleId
     * @return bool
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function deleteRule($ruleId)
    {
        // Retrieve the necessary models
        $rule = $this->findRuleOrAbort($ruleId);
        $cost = $this->findCostOrAbort($rule->cost_id);
        $product = $this->findProductOrAbort($cost->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to delete the rule
        return DB::transaction(function () use ($rule) {
            $rule->tiers()->delete();

            return $rule->delete();
        });
    }

    /**
     * @param int $costId
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function multisortRules($costId, $data)
    {
        // Validate the input data
        $validator = new MultisortRulesValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $cost = $this->findCostOrAbort($costId);
        $product = $this->findProductOrAbort($cost->product_id);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Multi sort rules
        foreach ($validated as $item) {
            $rule = Rule::where('cost_id', $costId)
                ->where('id', $item['id'])
                ->first();

            if (! $rule) {
                throw new RuntimeException('Rule ID must be valid');
            }

            $rule->sort_order = $item['sort_order'];

            $rule->save();
        }
    }
}
