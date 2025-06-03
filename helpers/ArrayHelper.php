<?php

namespace app\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    public static function addKeyPrefix($prefix, $array)
    {
        return array_combine(
            array_map(
                function($k) use ($prefix) {return $prefix . $k; },
                array_keys($array)
            ),
            $array);
    }
}