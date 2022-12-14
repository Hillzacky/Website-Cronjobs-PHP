<?php
return [
    'class' => 'yii\swiftmailer\Mailer',
    'viewPath' => '@app/mail',
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'mail.domain.com', // Replace with your mail server host
        'username' => 'foo', // Replace with your own user name
        'password' => 'bar', // Replace with your own user password
        'port' => 587,
        'encryption' => 'tls',
    ],
    /*
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'tls.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 587,
            'encryption' => 'tls',
        ],
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'ssl.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 465,
            'encryption' => 'ssl',
        ],
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'localhost',
            'port' => '25',
        ],

        'transport' => [
            'class' => 'Swift_SendmailTransport',
        ],
     */
];