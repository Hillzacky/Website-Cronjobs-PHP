<?php
namespace app\bootstrap;

use yii\web\Application;
use yii\web\User;
use app\models\User as ModelUser;
use yii\web\View;
use yii\base\BootstrapInterface;
use Yii;

class AppBootstrap implements BootstrapInterface
{
    public function bootstrap($app) {
        $app->setTimeZone(Yii::$app->params['timezone']);
        $this->setUpCurlOpts($app);
    }

    protected function setUpCurlOpts($app) {
        $curl = Yii::$app->params['curl'];
        if(!isset($curl[CURLOPT_TIMEOUT])) {
            Yii::$app->params['curl'][CURLOPT_TIMEOUT] = 600;
        }
        if(!isset($curl[CURLOPT_CONNECTTIMEOUT])) {
            Yii::$app->params['curl'][CURLOPT_CONNECTTIMEOUT] = 20;
        }
    }
}