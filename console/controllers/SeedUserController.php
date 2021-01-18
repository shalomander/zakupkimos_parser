<?php

namespace console\controllers;

use common\models\User;
use common\models\UserPlan;
use yii\console\Controller;
use common\models\Plan;

class SeedUserController extends Controller
{
    public function actionIndex($email = 'demo@admin.com', $passwd = 'passwd')
    {
        $admin = new User();
        $admin->email = $email;
        $admin->username = 'admin';
        $admin->generateAuthKey();
        $admin->setPassword($passwd);
        $admin->status = User::STATUS_ACTIVE;
//        $admin->full_name = 'Admin';
//        $admin->role = 'admin';
        $admin->save();
    }
}