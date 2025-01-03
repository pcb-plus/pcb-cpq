<?php

namespace PcbPlus\PcbCpq\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateFactorValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:64',
            'show_name' => 'required|string|max:64',
            'code' => 'required|string|max:64',
            'description' => 'present|string|max:255',
            'is_optional' => 'required|bool',
            'options' => 'present|array|required_if:is_optional,true',
            'options.*.name' => 'required|string|max:64',
            'options.*.show_name' => 'required|string|max:64',
            'options.*.value' => 'required|string|max:64',
            'options.*.description' => 'present|string|max:255',
            'options.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
