<?php

namespace PcbPlus\PcbCpq\Enums;

class ComparisonOperatorEnum
{
    const EQ = '==';
    const NEQ = '!=';
    const LT = '<';
    const GT = '>';
    const LTE = '<=';
    const GTE = '>=';

    /**
     * @return array
     */
    public static function all()
    {
        return [
            [
                'name' => 'equal',
                'description' => '等于',
                'operator' => self::EQ,
            ],
            [
                'name' => 'not equal',
                'description' => '不等于',
                'operator' => self::NEQ,
            ],
            [
                'name' => 'less than',
                'description' => '小于',
                'operator' => self::LT,
            ],
            [
                'name' => 'greater than',
                'description' => '大于',
                'operator' => self::GT,
            ],
            [
                'name' => 'less than or equal to',
                'description' => '小于等于',
                'operator' => self::LTE,
            ],
            [
                'name' => 'greater than or equal to',
                'description' => '大于等于',
                'operator' => self::GTE,
            ],
        ];
    }
}
