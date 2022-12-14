<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=webcron',
    'username' => 'root',
    'password' => 'secret',
    'charset' => 'utf8mb4',
    'tablePrefix'=>'webcron_',
    'enableSchemaCache'=>true,
    'schemaCacheDuration'=>60*60*24*30,
];
