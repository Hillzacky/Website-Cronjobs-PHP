<?php
namespace app\controllers;

use app\components\AppController;
use app\models\Log;
use app\models\Schedule;
use app\models\Stat;
use app\models\User;
use Yii;
use yii\filters\AccessControl;

class SiteController extends AppController
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::class,
            'only'=>['index'],
            'rules' => [
                [
                    'allow'=>true,
                    'roles'=>['@'],
                ]
            ],
        ];
        return $behaviors;
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {
        /**
         * @var $user User
         */
        $user = Yii::$app->getUser()->getIdentity();

        $this->getView()->title = Yii::t("app", "Dashboard");

        $upcomingSchedule = Schedule::find()
            ->owner()
            ->enabled()
            ->orderBy("send_at_user ASC")
            ->limit(25)
            ->all();

        $recentLogs = Log::find()
            ->with("schedule")
            ->owner()
            ->orderBy("schedule_time DESC")
            ->limit(25)
            ->all();


        $endDays = $user->getDateObject();
        $beginDays = clone $endDays;
        $beginMonths = clone $endDays;

        $intervalDays = new \DateInterval('P31D');
        $intervalMonths = new \DateInterval('P1Y');

        $beginDays->sub($intervalDays);
        $beginMonths->sub($intervalMonths);

        $last31days = [];
        $last31daysStat = Stat::find()
            ->select(["insert_at", "sum(failed) as totalFailed", "sum(success) as totalSuccess"])
            ->owner()
            ->andWhere(["between", "insert_at", $beginDays->format("Y-m-d"), $endDays->format("Y-m-d")])
            ->groupBy(["insert_at", "DAY(insert_at)"])
            ->asArray()
            ->indexBy("insert_at")
            ->all();

        $dayLoop = $user->getDateObject();
        for($i = 0; $i < 31; $i++) {
            $key = $i==0 ? $dayLoop->format("Y-m-d") : $dayLoop->modify("previous day")->format("Y-m-d");
            $last31days[] = $last31daysStat[$key] ?? [
                'insert_at' => $key,
                'totalFailed' => 0,
                'totalSuccess' => 0
            ];
        }
        $last31days = array_reverse($last31days);

        $last12months = [];
        $last12monthsStat = Stat::find()
            ->select(['DATE_FORMAT(insert_at,"%Y-%m") as month', 'sum(failed) as totalFailed', 'sum(success) as totalSuccess', 'insert_at'])
            ->owner()
            ->andWhere(["between", "insert_at", $beginMonths->format("Y-m-d"), $endDays->format("Y-m-d")])
            ->groupBy(["insert_at", "MONTH(insert_at)"])
            ->orderBy("insert_at DESC")
            ->asArray()
            ->indexBy("month")
            ->all();

        $monthLoop = $user->getDateObject();
        for($i = 0; $i < 12; $i++) {
            $key = $i==0 ? $monthLoop->format("Y-m") : $monthLoop->modify('first day of previous month')->format("Y-m");
            $last12months[] = $last12monthsStat[$key] ?? [
                'month' => $key,
                'totalFailed' => 0,
                'totalSuccess' => 0
            ];
        }
        $last12months = array_reverse($last12months);


        return $this->render('index', compact('upcomingSchedule', 'recentLogs', 'last31days', 'last12months'));
    }
}
