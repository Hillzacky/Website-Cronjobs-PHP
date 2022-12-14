<?php
namespace app\controllers;

use app\components\AppController;
use app\components\Helper;
use app\models\Log;
use Yii;

class LogController extends AppController
{
    public function actionClear() {
        $period = \Yii::$app->request->get("period");
        /**
         * @var $user \app\models\User
         */
        $user = Yii::$app->getUser()->getIdentity();
        $now = Helper::convertToServer($user->getDateObject());
        $condition = [
            'and', ['user_id'=>$user->id],
        ];

        $periods = [
            'one-day'=>'-1 day',
            'one-week'=>'-1 week',
            'one-month'=>'-1 month',
            'three-months'=>'-3 months',
            'six-months'=>'-6 months',
            'one-year'=>'-1 year',
        ];
        if($period = $periods[$period] ?? null) {
            $now->modify($period);
            $condition[] = ["<", "added_at", $now->format("Y-m-d H:i:s")];
        }

        Log::deleteAll($condition);

        Yii::$app->getSession()->setFlash("success", Yii::t("app", "Logs have been cleared"));
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }
}