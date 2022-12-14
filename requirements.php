<?php
function checkCaptchaSupport()
{
    if(extension_loaded('imagick'))
    {
        $imagick=new Imagick();
        $imagickFormats=$imagick->queryFormats('PNG');
    }
    if(extension_loaded('gd'))
        $gdInfo=gd_info();
    if(isset($imagickFormats) && in_array('PNG',$imagickFormats))
        return '';
    elseif(isset($gdInfo))
    {
        if($gdInfo['FreeType Support'])
            return '';
        return 'GD installed,<br />FreeType support not installed';
    }
    return 'GD or ImageMagick not installed';
}

function checkPhpExtensionVersion($extensionName, $version, $compare = '>=')
{
    if (!extension_loaded($extensionName)) {
        return false;
    }
    $extensionVersion = phpversion($extensionName);
    if (empty($extensionVersion)) {
        return false;
    }
    if (strncasecmp($extensionVersion, 'PECL-', 5) === 0) {
        $extensionVersion = substr($extensionVersion, 5);
    }

    return version_compare($extensionVersion, $version, $compare);
}

function checkPhpIniOn($name)
{
    $value = ini_get($name);
    if (empty($value)) {
        return false;
    }

    return ((int) $value === 1 || strtolower($value) === 'on');
}

function checkPhpIniOff($name)
{
    $value = ini_get($name);
    if (empty($value)) {
        return true;
    }

    return (strtolower($value) === 'off');
}

function getServerInfo(): string
{
    $info[]= $_SERVER['SERVER_SOFTWARE'] ?? '';
    $info[]=@strftime('%Y-%m-%d %H:%M',time());
    return implode(' ',$info);
}

function checkPhpFunctions(array $functions): string
{
    $not_available = [];
    foreach ($functions as $function) {
        if(!function_exists($function)) {
            $not_available[] = sprintf("<strong>%s</strong> is not available", $function);
        }
    }
    return !empty($not_available) ? join(", ", $not_available) : "";
}

$serverInfo = getServerInfo();

$requirements = array(
    array(
        "rule"=>"PHP version (7.4.0 or higher)",
        "result"=>version_compare(PHP_VERSION, "7.4.0",">="),
        "explanation"=>"PHP 7.4.0 or higher is required.",
        "mandatory"=>true,
    ),
    array (
        "rule"=>'Reflection extension',
        "result"=>class_exists('Reflection', false),
        "explanation"=>"Reflection extension is not loaded",
        "mandatory"=>true,
    ),
    array (
        "rule"=>'PCRE extension',
        "result"=>extension_loaded('pcre'),
        "explanation"=>"PCRE extension is not loaded",
        "mandatory"=>true,
    ),
    array (
        "rule"=>'SPL extension',
        "result"=>extension_loaded('SPL'),
        "explanation"=>"SPL extension is not loaded",
        "mandatory"=>true,
    ),
    array (
        "rule"=>'Ctype extension',
        "result"=>extension_loaded('ctype'),
        "explanation"=>"Ctype extension is not loaded",
        "mandatory"=>true,
    ),
    array(
        "rule"=>'Intl extension',
        "result"=>checkPhpExtensionVersion('intl', '1.0.2', '>='),
        "explanation"=>"Internationalization support. PHP Intl extension 1.0.2 or higher is required",
        "mandatory"=>true,
    ),
    array(
        "rule"=>'ICU version',
        "result"=>defined('INTL_ICU_VERSION') && version_compare(INTL_ICU_VERSION, '49', '>='),
        "explanation"=>"Internationalization support. ICU 49.0 or higher is required",
        "mandatory"=>true,
    ),
    array(
        "rule"=>'ICU Data version',
        "result"=>defined('INTL_ICU_DATA_VERSION') && version_compare(INTL_ICU_DATA_VERSION, '49.1', '>='),
        "explanation"=>"Internationalization support. ICU Data 49.1 or higher is required",
        "mandatory"=>true,
    ),
    array(
        "rule"=>'Bcmath extension',
        "result"=>extension_loaded("bcmath"),
        "explanation"=>"Bcmath extension is not loaded",
        "mandatory"=>true,
    ),
    array(
        "rule"=>'MBString extension',
        "result"=>extension_loaded("mbstring"),
        "explanation"=>'MBString extension is not loaded. Required for multibyte encoding string processing.',
        "mandatory"=>true,
    ),
    array(
        "rule"=>'OpenSSL extension',
        "result"=>extension_loaded('openssl'),
        "explanation"=>'OpenSSL extension is not loaded. Required by encrypt and decrypt methods.',
        "mandatory"=>true,
    ),
    array(
        "rule"=>'PDO extension',
        "result"=>extension_loaded('pdo'),
        "explanation"=>'PDO extension is not loaded',
        "mandatory"=>true,
    ),
    array(
        "rule"=>'PDO MySQL driver',
        "result"=>extension_loaded('pdo_mysql'),
        "explanation"=>'PDO MySQL extension is not loaded',
        "mandatory"=>true,
    ),
    /*array(
        "rule"=>'GD extension with<br />FreeType support<br />or ImageMagick<br />extension with<br />PNG support',
        "result"=>'' === $message=checkCaptchaSupport(),
        "explanation"=>$message,
        "mandatory"=>false,
    ),*/
    array(
        "rule"=>'Fileinfo extension',
        "result"=>extension_loaded("fileinfo"),
        "explanation"=>"Fileinfo extension is not loaded",
        "mandatory"=>true,
    ),
    array(
        "rule"=>'cURL',
        "result"=>function_exists('curl_version'),
        "explanation"=>'cURL is not enabled',
        "mandatory"=>true,
    ),
    array (
        "rule"=>"Socket functions",
        "result"=>("" === $email_func = checkPhpFunctions(["stream_socket_enable_crypto", "stream_socket_client", "fsockopen", "proc_open"])),
        "explanation"=>$email_func. ". These functions must be available in order to send email notifications.",
        "mandatory"=>false,
    ),
    array(
        "rule"=>'Process',
        "result"=>function_exists('proc_open'),
        "explanation"=>'The <strong>proc_open</strong> function is not available or disabled in <strong>php.ini</strong>.
Without access to this function, the program cannot:
<ul>
<li>Send email notifications</li>
<li>Execute shell commands. Only HTTP(S) cron tasks are available to work</li>
</ul>',
        "mandatory"=>false,
    ),
    array(
        "rule"=>'short_open_tag (php.ini directive)',
        "result"=>checkPhpIniOn('short_open_tag'),
        "explanation"=>'You need to enable <strong>short_open_tag</strong> in <strong>php.ini</strong>.',
        "mandatory"=>true,
    ),
    array(
        "rule"=>'Safe mode (php.ini directive) must be disabled',
        "result"=>checkPhpIniOff('safe_mode'),
        "explanation"=>'You need to disable <strong>safe_mode</strong> in <strong>php.ini</strong> to execute large amount of cron jobs at the same time.',
        "mandatory"=>false,
    ),
);

$result=1;  // 1: all pass, 0: fail
foreach($requirements as $i=>$requirement) {
    if(!$requirement['result'] && $requirement['mandatory']) {
        $result = 0;
    }
    $requirements[$i]['cell_class'] = $requirement['result'] ? "passed" : ($requirement['mandatory'] ? "failed" : "warning");

    if($requirement['explanation'] === '') {
        $requirements[$i]['explanation']='&nbsp;';
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="content-language" content="en"/>
    <title>Cronjob Manager Requirements Checker</title>
    <style type="text/css">
        body
        {
            background: white;
            font-family:'Lucida Grande',Verdana,Geneva,Lucida,Helvetica,Arial,sans-serif;
            font-size:10pt;
            font-weight:normal;
        }

        #page
        {
            width: 800px;
            margin: 0 auto;
        }

        #header
        {
        }

        #content
        {
        }

        #footer
        {
            color: gray;
            font-size:8pt;
            border-top:1px solid #aaa;
            margin-top:10px;
        }

        h1
        {
            color:black;
            font-size:1.6em;
            font-weight:bold;
            margin:0.5em 0;
        }

        h2
        {
            color:black;
            font-size:1.25em;
            font-weight:bold;
            margin:0.3em 0;
        }

        h3
        {
            color:black;
            font-size:1.1em;
            font-weight:bold;
            margin:0.2em 0;
        }

        table.result
        {
            background:#E6ECFF none repeat scroll 0 0;
            border-collapse:collapse;
            width:100%;
        }

        table.result th
        {
            background:#CCD9FF none repeat scroll 0 0;
            text-align:left;
        }

        table.result th, table.result td
        {
            border:1px solid #BFCFFF;
            padding:0.2em;
        }

        td.passed
        {
            background-color: #60BF60;
            border: 1px solid silver;
            padding: 2px;
        }

        td.warning
        {
            background-color: #efeb5b;
            border: 1px solid silver;
            padding: 2px;
        }

        td.failed
        {
            background-color: #FF8080;
            border: 1px solid silver;
            padding: 2px;
        }
    </style>
</head>
<body>
<div id="page">

    <div id="header">
        <h1>Online Cronjob Manager</h1>
    </div><!-- header-->

    <div id="content">
        <h2>Description</h2>
        <p>
            This script checks if your server configuration meets the requirements
            for running <a href="http://webcron.php8developer.com/">Online Cronjob Manager</a>.
            It checks if the server is running the right version of PHP,
            if appropriate PHP extensions have been loaded, and if php.ini file settings are correct.
        </p>
        <h2>NOTE!</h2>
        <p>
            This is not a cron job <strong>SYSTEM</strong>, you usually find in cPanel (Plesk), or in linux servers where you set a time
            and a command (script) to run.
        </p>

        <h2>Conclusion</h2>
        <p>
            <?php if($result>0): ?>
                Congratulations! Your server configuration satisfies minimum requirements by Online Cronjob Manager.
            <?php else: ?>
                Unfortunately your server configuration does not satisfy the requirements by Online Cronjob Manager.
            <?php endif; ?>
        </p>

        <h2>Details</h2>

        <table class="result">
            <tr><th>Name</th><th>Result</th><th>Memo</th></tr>
            <?php foreach($requirements as $requirement): ?>
                <tr>
                    <td>
                        <?php echo $requirement['rule']; ?>
                    </td>
                    <td class="<?php echo $requirement['cell_class']; ?>">
                        <?php echo ucfirst($requirement['cell_class']) ?>
                    </td>
                    <td>
                        <?php if(!$requirement['result']): ?>
                            <?php echo $requirement['explanation']; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table>
            <tr>
                <td class="passed">&nbsp;</td><td>passed</td>
                <td class="failed">&nbsp;</td><td>failed</td>
                <td class="warning">&nbsp;</td><td>warning</td>
            </tr>
        </table>

    </div><!-- content -->

    <div id="footer">
        <?php echo $serverInfo; ?>
    </div><!-- footer -->

</div><!-- page -->
</body>
</html>