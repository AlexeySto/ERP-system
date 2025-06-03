<?php

namespace app\validators;

class DbDateValidator extends \yii\validators\DateValidator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->format = 'php:d.m.Y';
        if (empty($this->timestampAttributeFormat)) $this->timestampAttributeFormat = 'php:Y-m-d';
        if (empty($this->timestampAttribute)) $this->timestampAttribute = $this->attributes[0] ?? '';
    }
}
