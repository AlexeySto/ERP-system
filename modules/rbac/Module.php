<?php

namespace app\modules\rbac;

use yii\base\BootstrapInterface;

/**
 * rbac module definition class
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\rbac\controllers';

    public $fullAccess = [];

    public function bootstrap($app) {
        $app->getUrlManager()->addRules(
            [
                '<module:rbac>/<action:(.*)>' => '<module>/default/<action>'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
