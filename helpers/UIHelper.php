<?php

namespace app\helpers;

use app\models\UserStatus;
use app\modules\rbac\models\AuthItem;

class UIHelper
{
    public static function getUserRoles()
    {
        return AuthItem::find()->select(['IFNULL(title, description) as title', 'name'])->where(['type' => 1])->indexBy('name')->column();
    }

    public static function getUserStatuses()
    {
        return UserStatus::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}