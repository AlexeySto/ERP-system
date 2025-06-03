<?php

namespace app\models;

use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    /**
     * Returns user, which are not deleted
     *
     * @return self
     */
    public function notDeleted(): self
    {
        return parent::andWhere(['user.is_deleted' => 0]);
    }


}