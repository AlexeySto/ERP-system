<?php

namespace app\helpers;

use app\models\Notification;
use app\models\Order;
use app\models\Task;
use app\models\User;
use yii\helpers\Url;

class NotificationHelper
{
    const TARGET_TASK = 1;
    const TARGET_ORDER = 2;

    /**
     * @param Task $task
     * @param User $performer
     * @throws \yii\base\InvalidConfigException
     */
    public static function notifyNewTask($task, $performer) {
        if($performer->phone) {
            $notification = Notification::findOne(self::TARGET_TASK);
            $message = $notification->template;
            $message = str_replace('[setter]', $task->setter->fio, $message);
            $message = str_replace('[deadline]', date('d.m.Y H:i', strtotime($task->deadline)), $message);
            $message = str_replace('[link]', Url::base(true).'/tasks/edit?id='.$task->id, $message);
            $message = str_replace('[comment]', $notification->comments, $message);
            \Yii::$app->wazzup->sendMessage($performer->phone, $message);
        }
    }

    /**
     * @param Order $order
     * @throws \yii\base\InvalidConfigException
     */
    public static function notifyPlanningDateChanged($order) {
        $notification = Notification::findOne(self::TARGET_ORDER);
        $message = $notification->template;
        $message = str_replace('[link]', Url::base(true).'/planning?id='.$order->id, $message);
        $message = str_replace('[comment]', $notification->comments, $message);
        $managers = User::find()->where(['id' => \Yii::$app->authManager->getUserIdsByRole('Manager')])->all();
        foreach ($managers as $manager) {
            if($manager->phone) {
                \Yii::$app->wazzup->sendMessage($manager->phone, $message);
            }
        }
    }
}