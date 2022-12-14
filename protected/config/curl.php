<?php
return [
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_SSL_VERIFYHOST=>0,
    CURLOPT_USERAGENT=>"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36",
    CURLOPT_HEADER=>1,
    CURLOPT_FOLLOWLOCATION=> ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' || ini_get('safe_mode')==''),
];