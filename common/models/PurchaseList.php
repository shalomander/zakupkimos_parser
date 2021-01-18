<?php
namespace common\models;
use yii\base\Model;
use yii\db\ActiveRecord;

class PurchaseList extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    public static function tableName()
    {
        return '{{%purchase_list}}';
    }

}
