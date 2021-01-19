<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter'    => [
            'class'    => 'yii\i18n\Formatter',
            'timeZone' => 'Europe/Moscow',
            'dateFormat' => 'php:H:i d.m.Y'
        ],
    ],
];
