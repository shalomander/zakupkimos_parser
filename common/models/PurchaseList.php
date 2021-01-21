<?php

namespace common\models;

use yii\base\Model;
use yii\db\ActiveRecord;

class PurchaseList extends ActiveRecord
{

    public function rules()
    {
        return [
        ];
    }

    public static function tableName()
    {
        return '{{%purchase_list}}';
    }

    public static function setStatus($id, $status_id)
    {
        $purchase = static::findOne($id);
        if ($purchase) {
            $purchase->status_id = $status_id;
            $purchase->save();
        }
    }
}
