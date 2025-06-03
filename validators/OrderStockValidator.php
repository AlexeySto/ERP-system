<?php

namespace app\validators;

use app\models\Order;

class OrderStockValidator extends \yii\validators\Validator
{
    public function init()
    {
        parent::init();
        $this->message = 'Нет достаточного количества компонентов на складе!';
    }

    public function validateAttribute($model, $attribute)
    {
        if (!($model instanceof Order)) {
            $this->message = 'Ошибка входных данных!';
            $this->addError($model, $attribute, $this->message);
            return false;
        }
        $model->clearStockErrors();

        $prodId = $model->product_id;
        $prodQ = $model->quantity;
        if (!$prodId || !($prod = ProductCommon::findOne($prodId))) {
            $this->message = 'Производимый товар не найден!';
            $this->addError($model, $attribute, $this->message);
            return false;
        }

        $isOk = true;
        foreach ($prod->editConsumables as $cons) {
            $itemId = $cons['item_id'] ?? 0;
            $needed = number_format(($cons['q'] ?? 0) * $prodQ, $prod->dec_numbers, '.', '');
            if ($itemId && $needed) {
                $consumable = Consumable::findActiveOne($itemId);
                if (!$consumable || $consumable->available < $needed) {
                    $model->addStockError([$consumable, $needed]);
                    $isOk = false;
                }
            }
        }
        if ($prod->type === 1) {
            foreach ($prod->editWorkPieces as $wp) {
                $itemId = $wp['item_id'] ?? 0;
                $needed = number_format(($wp['q'] ?? 0) * $prodQ, $prod->dec_numbers, '.', '');
                if ($itemId && $needed) {
                    $workPiece = WorkPiece::findActiveOne($itemId);
                    if (!$workPiece || $workPiece->available < $needed) {
                        $model->addStockError([$workPiece, $needed]);
                        $isOk = false;
                    }
                }
            }
        }
        if (!$isOk) {
            $this->addError($model, $attribute, $this->message);
            return false;
        }
        return true;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return '';
    }
}
