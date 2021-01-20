<?php

namespace common\models;

use yii\db\ActiveRecord;


class Settings extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'required']
        ];
    }

    public static function get($key, $default = null)
    {
        $item = static::find()->where(['key' => $key])->one();
        return $item ? $item->value : $default;
    }

    public static function set($key, $value = null, $strict = true)
    {
        $item = static::find()->where(['key' => $key])->one();
        if (!$item) {
            if (!$strict) {
                $item = new static();
                $item->key = $key;
            } else {
                return null;
            }
        }
        $item->value = $value;
        return $item->save();
    }

    public static function getAsArray($defaults){
        $settings=static::find()->all();
        $result=$defaults;
        foreach ($settings as $item) {
            $result[$item['key']]=$item['value'];
        }
        return $result;
    }
}
