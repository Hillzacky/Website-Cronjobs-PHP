<?php
return [
    'sourcePath' => __DIR__. DIRECTORY_SEPARATOR . '..',
    'languages' => ['ru-RU', 'en-US'],
    'translator' => 'Yii::t',
    'sort' => false,
    'removeUnused' => false,
    'only' => ['*.php'],
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/vendor',
        '/runtime',
    ],
    'format' => 'php',
    'messagePath' => __DIR__ . DIRECTORY_SEPARATOR. '..' . DIRECTORY_SEPARATOR . 'messages',
    'overwrite' => true,
    'ignoreCategories' => [
        'yii',
    ],
];