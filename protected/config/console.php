<?php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
$mailer = require(__DIR__ . '/mailer.php');
$urlManager = require(__DIR__ . '/url-manager.php');
$i18n = require(__DIR__ . '/i18n-web.php');
$urlManager = \yii\helpers\ArrayHelper::merge($urlManager, [
    "baseUrl"=>$params['baseUrl'],
    'scriptUrl'=>$params['baseUrl'].'/index.php',
]);
return [
    'id' => 'WebCron console',
    'language'=>'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii', 'app\bootstrap\AppBootstrap'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'mailer' => $mailer,
        'urlManager' => $urlManager,
        'i18n'=>$i18n,
    ],
    'params' => $params,
];
