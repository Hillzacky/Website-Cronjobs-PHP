<?php
namespace app\components;

use app\models\User;
use yii\web\Controller;
use yii;
use yii\web\Cookie;

class AppController extends Controller
{
    public $breadcrumbs = [];

    public function init() {
        $this->enableCsrfValidation = Yii::$app->params['enableCsrfValidation'];

        parent::init();
        $this->layout = Yii::$app->user->getIsGuest() ? "guest" : "authorized";
        $this->on(Controller::EVENT_BEFORE_ACTION, function() {
            $this->getView()->registerJs("WebCronApp.init();", yii\web\View::POS_READY);
        });
        $this->setUpLanguage();
        if(!Yii::$app->getUser()->getIsGuest() AND Yii::$app->getUser()->getIdentity()->lang_id != Yii::$app->language) {
            User::updateAll([
                "lang_id"=>Yii::$app->language,
            ], [
                "id"=>Yii::$app->getUser()->getId(),
            ]);
        }
    }

    /**
     * @param $class string class Name
     * @param $id int
     * @return yii\db\ActiveRecordInterface
     * @throws yii\web\HttpException
     */
    public function loadModel($class, $id) {
        /**
         * @var $class yii\db\ActiveRecordInterface
         */
        if(!$model = $class::find()->owner()->andWhere(["id"=>$id])->one()) {
            throw new yii\web\HttpException(404, Yii::t("app", "The page you are looking for doesn't exists"));
        }
        return $model;
    }

    public function goToPreviousPage($default) {
        $gBack = Yii::$app->request->get("back");
        $ref = $gBack ? $gBack : Yii::$app->request->getReferrer();
        $back = $ref ? $ref : $default;
        return $this->redirect($back);
    }

    protected function setUpLanguage() {
        $languages = Yii::$app->params['languages'];
        $cookies = Yii::$app->request->cookies;
        $responseCookies = Yii::$app->response->cookies;

        if(isset($languages[Yii::$app->request->get("language")])) {
            $lang = Yii::$app->request->get("language");
            $cookie = new Cookie();
            $cookie->expire = time() + (60 * 60 * 24 * 365); // 1 year
            $cookie->name = "language";
            $cookie->value = $lang;
            $cookie->path = Yii::$app->params['cookiePath'];
            $cookie->secure = Yii::$app->params['cookieSecure'];
            $cookie->sameSite = Yii::$app->params['cookieSameSite'];
            $responseCookies->add($cookie);
        } elseif($cookies->has("language")) {
            $lang = $cookies->getValue("language");
        } else {
            $lang = Yii::$app->request->getPreferredLanguage();
        }
        if(!isset($languages[$lang])) {
            $lang = Yii::$app->language;
        }

        Yii::$app->language = $lang;
    }
}