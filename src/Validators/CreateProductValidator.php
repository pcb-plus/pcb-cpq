<?php

namespace PcbPlus\PcbCpq\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateProductValidator
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
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
