<?php

namespace common\models;

use yii\db\ActiveRecord;


class Status extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%status}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required']
        ];
    }

    public static function getAsArray(){
        $settings=static::find()->all();
        foreach ($settings as $item) {
            $result[$item['id']]=$item['name'];
        }
        return $result;
    }
}
