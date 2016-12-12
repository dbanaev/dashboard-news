<?php

namespace app\helpers;

use app\models\User;
use Yii;

class DashboardMenu {

    public static function getMenu() {

        $menuItems = [];

        if (!Yii::$app->user->isGuest) {

            $menuItems[] = [
                'label' => 'Новости',
                'url' => ['news/index'],
                'active' => (Yii::$app->controller->id == 'news')
            ];

            if (Yii::$app->user->identity->role == User::ROLE_ROOT) {
                $menuItems[] = [
                    'label' => 'Категории',
                    'url' => ['category/index'],
                    'active' => (Yii::$app->controller->id == 'category')
                ];

                $menuItems[] = [
                    'label' => 'Пользователи',
                    'url' => ['user/index'],
                    'active' => (Yii::$app->controller->id == 'user')
                ];
            }

            if (Yii::$app->asAnotherUser->check()) {
                $menuItems[] = [
                    'label' => 'Выйти из пользователя (' . Yii::$app->user->identity->login . ')',
                    'url' => ['dashboard/return-root'],
                ];
            }

            $menuItems[] = [
                'label' => 'Выйти',
                'url' => ['dashboard/logout'],
                'linkOptions' => [
                    'data-method' => 'post'
                ]
            ];
        }

        return $menuItems;
    }
}