<?php

namespace PcbPlus\PcbCpq\Enums;

class LogicalOperatorEnum
{
    const AND = 'and';
    const OR = 'or';

    /**
     * @return array
     */
    public static function all()
    {
        return [
            [
                'name' => 'and',
                'description' => '并且',
                'operator' => self::AND,
            ],
            [
                'name' => 'or',
                'description' => '或者',
                'operator' => self::OR,
            ],
        ];
    }
}
