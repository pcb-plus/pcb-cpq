<?php

namespace PcbPlus\PcbCpq\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MultisortCostsValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($data)
    {
        $validator = Validator::make($data, [
            '*.id' => 'required|integer|min:0',
            '*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
