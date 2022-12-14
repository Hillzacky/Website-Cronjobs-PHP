<?php
return [
    'translations'=>[
        'app*'=>[
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/messages',
            'forceTranslation'=>true,
            'fileMap' => [
                'app' => 'app.php',
            ],
        ],
    ],
];
