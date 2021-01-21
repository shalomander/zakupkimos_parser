<?php

namespace console\controllers;

use common\models\Status;
use yii\console\Controller;
class SeedStatusController extends Controller
{
    public function actionIndex()
    {
        $statuses = [
            'Не просмотрено',
            'Просмотрено',
        ];
        array_walk($statuses, function ($value) {
            $status = new Status();
            $status->name = $value;
            $status->save();
        });
    }
}