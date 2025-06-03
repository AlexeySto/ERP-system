<?php

namespace app\helpers;

class NumberHelper
{
    public const MAX_DECIMAL_DIGITS = 3;

    public const DECIMAL_INPUT_CONFIG = [
        'alias' =>  'decimal',
        'radixPoint' => ',',
        'allowMinus' => false,
        'inputType' => 'number',
        'digits' => 2,
        'min' => 0,
        'max' => 99999999,
    ];

    public const CURRENCY_INPUT_CONFIG = [
        'alias' =>  'currency',
        'radixPoint' => ',',
        'allowMinus' => false,
        'inputType' => 'number',
        'groupSeparator' => ' ',
        'autoGroup' => true
    ];

    public const BUNDLES_INPUT_CONFIG = [
        'alias' =>  'decimal',
        'radixPoint' => ',',
        'allowMinus' => false,
        'inputType' => 'number',
        'digits' => 2,
        'min' => 0,
        'max' => 9999,
    ];

    public const LENGTH_INPUT_CONFIG = [
        'alias' => 'integer',
        'allowMinus' => false,
        'inputType' => 'number',
        'min' => 0,
        'max' => 9999,
    ];

    public const AREA_INPUT_CONFIG = [
        'alias' =>  'decimal',
        'radixPoint' => ',',
        'allowMinus' => false,
        'inputType' => 'number',
        'digits' => 3,
        'min' => 0,
        'max' => 9999,
    ];

    public const WEIGHT_INPUT_CONFIG = [
        'alias' =>  'decimal',
        'radixPoint' => ',',
        'allowMinus' => false,
        'inputType' => 'number',
        'digits' => 3,
        'min' => 0,
        'max' => 9999,
    ];

    public const TIME_INPUT_CONFIG = [
        'alias' =>  'decimal',
        'radixPoint' => ',',
        'allowMinus' => false,
        'inputType' => 'number',
        'digits' => 2,
        'min' => 0,
        'max' => 999,
    ];

    public static function cropDecimals($number, $precision = self::MAX_DECIMAL_DIGITS)
    {
        $multiplier = 10 ** $precision;

        if ((int)($number * $multiplier) <= 0) {
            return 0;
        }

        return rtrim(rtrim(number_format(round($number, $precision), $precision, ',', ''), '0'), ',');
    }
}