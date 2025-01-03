<?php

namespace PcbPlus\PcbCpq\Enums;

class ArithmeticOperatorEnum
{
    const ADD = '+';
    const SUB = '-';
    const MUL = '*';
    const DIV = '/';

    /**
     * @return array
     */
    public static function all()
    {
        return [
            [
                'name' => 'addition',
                'description' => '加',
                'operator' => self::ADD,
            ],
            [
                'name' => 'subtraction',
                'description' => '减',
                'operator' => self::SUB,
            ],
            [
                'name' => 'multiplication',
                'description' => '乘',
                'operator' => self::MUL,
            ],
            [
                'name' => 'division',
                'description' => '除',
                'operator' => self::DIV,
            ],
        ];
    }
}
