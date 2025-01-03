<?php

namespace PcbPlus\PcbCpq\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateRuleValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($data)
    {
        $validator = Validator::make($data, [
            'price' => 'required|numeric|min:0',
            'is_unit' => 'required|bool',
            'multiplier_expression' => 'present|string|required_if:is_unit,true',
            'multiplier_description' => 'present|string|required_if:is_unit,true',
            'is_conditional' => 'required|bool',
            'condition_expression' => 'present|string|required_if:is_conditional,true',
            'condition_description' => 'present|string|required_if:is_conditional,true',
            'is_tiered' => 'required|bool',
            'tiers' => 'present|array|required_if:is_tiered,true',
            'tiers.*.price' => 'required|numeric|min:0',
            'tiers.*.condition_expression' => 'required|string',
            'tiers.*.condition_description' => 'required|string',
            'tiers.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
