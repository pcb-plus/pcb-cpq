<?php

namespace PcbPlus\PcbCpq\Enums;

class ArrayOperatorEnum
{
    const IN = 'in';
    const NIN = 'not in';

    /**
     * @return array
     */
    public static function all()
    {
        return [
            [
                'name' => 'contain',
                'description' => '包含在',
                'operator' => self::IN,
            ],
            [
                'name' => 'does not contain',
                'description' => '不包含在',
                'operator' => self::NIN,
            ],
        ];
    }
}
