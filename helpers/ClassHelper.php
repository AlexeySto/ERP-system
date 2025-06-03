<?php

namespace app\helpers;

class ClassHelper
{
    public static function getBaseClassName($className)
    {
        $path = explode('\\' ,$className);
        return array_pop($path);
    }
}