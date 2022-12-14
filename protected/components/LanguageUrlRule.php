<?php
namespace app\components;

use yii\web\UrlRule;

class LanguageUrlRule extends UrlRule
{
    public function createUrl($manager, $route, $params) {
        if(!isset($params['language'])) {
            $params['language'] = \Yii::$app->language;
        }
        return parent::createUrl($manager, $route, $params);
    }
}