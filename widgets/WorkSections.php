<?php

namespace app\widgets;

use yii\base\Widget;

class WorkSections extends Widget
{
    public $order;

    public function run()
    {
        return $this->render('work_sections', ['model' => $this->order]);
    }
}
