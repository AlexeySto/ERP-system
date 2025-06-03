<?php

namespace app\widgets;

class MultipleInput extends \unclead\multipleinput\MultipleInput
{
    /*public $addButtonPosition = [
        self::POS_HEADER,
        self::POS_FOOTER,
    ];*/

    public $iconMap = [
        self::ICONS_SOURCE_GLYPHICONS => [
            'add' => 'actionIcon fa fa-plus',
            'remove' => 'actionIcon fa fa-trash',
            'clone'  => 'glyphicon glyphicon-duplicate',
            'drag-handle'   => 'glyphicon glyphicon-menu-hamburger',
        ]
    ];

    public $addButtonOptions = [
        'class' => 'btn btn-success btn-sm btn-add-row',
    ];

    public $removeButtonOptions = [
        'class' => 'btn btn-danger btn-sm',
    ];

    public $attributeOptions = [
        'enableAjaxValidation' => true,
    ];

}