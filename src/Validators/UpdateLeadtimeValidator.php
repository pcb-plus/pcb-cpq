<?php

namespace PcbPlus\PcbCpq\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateLeadtimeValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'is_conditional' => 'required|bool',
            'condition_expression' => 'present|string|required_if:is_conditional,true',
            'condition_description' => 'present|string|required_if:is_conditional,true',
            'options' => 'required|array',
            'options.*.name' => 'required|string|max:64',
            'options.*.show_name' => 'required|string|max:64',
            'options.*.min_days' => 'required|integer|min:0',
            'options.*.max_days' => 'required|integer|min:0',
            'options.*.price' => 'required|numeric|min:0',
            'options.*.is_default' => 'required|bool',
            'options.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
