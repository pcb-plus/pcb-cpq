<?php

namespace PcbPlus\PcbCpq\Services;

use PcbPlus\PcbCpq\Enums\ArithmeticOperatorEnum;
use PcbPlus\PcbCpq\Enums\ArrayOperatorEnum;
use PcbPlus\PcbCpq\Enums\ComparisonOperatorEnum;
use PcbPlus\PcbCpq\Enums\LogicalOperatorEnum;

class OperatorService
{
    /**
     * @return array
     */
    public function getOperators()
    {
        return [
            [
                'name' => 'arithmetic',
                'description' => '算术',
                'operators' => ArithmeticOperatorEnum::all(),
            ],
            [
                'name' => 'array',
                'description' => '数组',
                'operators' => ArrayOperatorEnum::all(),
            ],
            [
                'name' => 'comparison',
                'description' => '比较',
                'operators' => ComparisonOperatorEnum::all(),
            ],
            [
                'name' => 'logical',
                'description' => '逻辑',
                'operators' => LogicalOperatorEnum::all(),
            ]
        ];
    }
}
