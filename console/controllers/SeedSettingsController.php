<?php

namespace console\controllers;

use common\models\Settings;
use yii\console\Controller;

class SeedSettingsController extends Controller
{
    public function actionIndex($notificationEmail = 'demo@admin.com', $from = '23:00', $till = '7:00')
    {
        $row = new Settings();
        $row->key = 'notification_email';
        $row->value = $notificationEmail;
        $row->save();

        $row = new Settings();
        $row->key = 'silent_from';
        $row->value = $from;
        $row->save();

        $row = new Settings();
        $row->key = 'silent_till';
        $row->value = $till;
        $row->save();

        $row = new Settings();
        $row->key = 'last_parser_run';
        $row->value = strtotime("today", time());
        $row->save();

        $row = new Settings();
        $row->key = 'show_purchases_period';
        $row->value = '1 week';
        $row->save();
    }
}