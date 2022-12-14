<?php
return [
    /**
     * Apps timezone. List of supported timezones: http://php.net/manual/en/timezones.php
     */
    'timezone'=>'Europe/Vilnius',
	
    /**
     * Number of predictions
     */
    'schedulePrediction'=>200,
	
    /**
     * Ajax preload (create/edit)
     */
    'ajaxPrediction'=>20,
	
    /**
     * Cron Job response length in email
     */
    'kbEmailOutput'=>5,

    /**
     * Default value is 16.384. Number of UTF-8 characters to log.
     */
    'dbLogResponse'=>'16384',
	
    /**
     * Long name of App
     */
    'longAppName'=>'Web Cronjobs',
    
	/**
     *  Short name of App
     */
    'shortAppName'=>'<i class="far fa-clock"></i>',
	
    /**
     * Cookie path
     * If you installed script in subdir. for example: http://my-domain.com/webcron, then the cookiePath must be: /webcron
     * If you installed script in web root. for example: http://my-domain.com, then the cookiePath must be: /
     */
    'cookiePath'=>'/',

    /**
     * Whether cookie should be sent via secure connection
     */
    'cookieSecure'=>false,

    /**
     * SameSite prevents the browser from sending this cookie along with cross-site requests.
     */
    'cookieSameSite'=>'Lax',

    /**
     * Whether CSRF validation should be enabled
     */
    'enableCsrfValidation'=>true,

    /**
     * The "from" email which is used in email notification
     */
    "notificationFrom"=>"no-reply@domain.com",

    /**
     * Base URL of the App. Used for console program
     * If you installed script in subdirectory, then the URL must be in a following format: http://my-domain.com/sub-dir
     * If you installed script in web root, then the URL must be in a following format: http://my-domain.com
     */
    "baseUrl"=>'http://cron.codecanyon',

    /**
     * List of supported languages
     */
    'languages'=>[
        'en-US'=>'English',
        'ru-RU'=>'Русский',
    ],

    /**
     * Secret key used for cookies encoding
     */
	'cookieValidationKey'=>'TXntdwyZRXYBNV05PZK6cfw1f8cp1vpv',

    /**
     * The key to run cron jobs handler via web (http(s))
     */
    'webHandlerKey'=>'',

    /**
     * If proc_open function exists then user can setup shell commands
     */
    'canSetupProcess'=>function_exists('proc_open'),

    /**
     * Number of cURL commands per batch request
     */
    'batchUrl'=>10,

    /**
     * Number of processes per batch execution
     */
    'batchCommand'=>10,

    /**
     * cURL default options
     */
    'curl'=>require 'curl.php',
];
