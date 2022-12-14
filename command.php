<?php
class ExecutableFinder
{
    private $suffixes = array('.exe', '.bat', '.cmd', '.com');
    /**
     * Replaces default suffixes of executable.
     *
     * @param array $suffixes
     */
    public function setSuffixes(array $suffixes)
    {
        $this->suffixes = $suffixes;
    }
    /**
     * Adds new possible suffix to check for executable.
     *
     * @param string $suffix
     */
    public function addSuffix($suffix)
    {
        $this->suffixes[] = $suffix;
    }
    /**
     * Finds an executable by name.
     *
     * @param string $name      The executable name (without the extension)
     * @param string $default   The default to return if no executable is found
     * @param array  $extraDirs Additional dirs to check into
     *
     * @return string The executable path or default value
     */
    public function find($name, $default = null, array $extraDirs = array())
    {
        if (ini_get('open_basedir')) {
            $searchPath = explode(PATH_SEPARATOR, ini_get('open_basedir'));
            $dirs = array();
            foreach ($searchPath as $path) {
                // Silencing against https://bugs.php.net/69240
                if (@is_dir($path)) {
                    $dirs[] = $path;
                } else {
                    if (basename($path) == $name && is_executable($path)) {
                        return $path;
                    }
                }
            }
        } else {
            $dirs = array_merge(
                explode(PATH_SEPARATOR, getenv('PATH') ?: getenv('Path')),
                $extraDirs
            );
        }
        $suffixes = array('');
        if ('\\' === DIRECTORY_SEPARATOR) {
            $pathExt = getenv('PATHEXT');
            $suffixes = $pathExt ? explode(PATH_SEPARATOR, $pathExt) : $this->suffixes;
        }
        foreach ($suffixes as $suffix) {
            foreach ($dirs as $dir) {
                if (is_file($file = $dir.DIRECTORY_SEPARATOR.$name.$suffix) && ('\\' === DIRECTORY_SEPARATOR || is_executable($file))) {
                    return $file;
                }
            }
        }
        return $default;
    }
}
class PhpExecutableFinder
{
    private $executableFinder;
    public function __construct()
    {
        $this->executableFinder = new ExecutableFinder();
    }
    /**
     * Finds The PHP executable.
     *
     * @param bool $includeArgs Whether or not include command arguments
     *
     * @return string|false The PHP executable path or false if it cannot be found
     */
    public function find($includeArgs = true)
    {
        $args = $this->findArguments();
        $args = $includeArgs && $args ? ' '.implode(' ', $args) : '';
        // HHVM support
        if (defined('HHVM_VERSION')) {
            return (getenv('PHP_BINARY') ?: PHP_BINARY).$args;
        }
        // PHP_BINARY return the current sapi executable
        if (PHP_BINARY && in_array(PHP_SAPI, array('cli', 'cli-server', 'phpdbg')) && is_file(PHP_BINARY)) {
            return PHP_BINARY.$args;
        }
        if ($php = getenv('PHP_PATH')) {
            if (!is_executable($php)) {
                return false;
            }
            return $php;
        }
        if ($php = getenv('PHP_PEAR_PHP_BIN')) {
            if (is_executable($php)) {
                return $php;
            }
        }
        $dirs = array(PHP_BINDIR);
        if ('\\' === DIRECTORY_SEPARATOR) {
            $dirs[] = 'C:\xampp\php\\';
        }
        return $this->executableFinder->find('php', false, $dirs);
    }
    /**
     * Finds the PHP executable arguments.
     *
     * @return array The PHP executable arguments
     */
    public function findArguments()
    {
        $arguments = array();
        if (defined('HHVM_VERSION')) {
            $arguments[] = '--php';
        } elseif ('phpdbg' === PHP_SAPI) {
            $arguments[] = '-qrr';
        }
        return $arguments;
    }
}

$phpFinder = new PhpExecutableFinder();
$phpPath = $phpFinder->find();
$scriptPath = dirname(__FILE__).DIRECTORY_SEPARATOR."protected".DIRECTORY_SEPARATOR."yii";
$param = "exec";
$schedule = "* * * * *";
$null = ">/dev/null 2>&1";

$isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

$defaultWin = "php.exe";
$defaultLinux = "/usr/bin/php";
$found = true;

if(!$phpPath) {
    $phpPath = $isWin ? $defaultWin : $defaultLinux;
    $found = false;
}

$command = $isWin ? sprintf("%s %s %s %s", $schedule, $phpPath, $scriptPath, $param) : sprintf("%s %s %s %s %s", $schedule, $phpPath, $scriptPath, $param, $null);
?>
<html>
<head>
<title>Cron job command builder</title>
</head>
<body>
<p>
    You need to use following command to setup following cron job command to handle all your cron jobs created via web interface. <a href="http://docs.php8developer.com/web-cronjobs/#/installation?id=cron-jobs" target="_blank">Read here</a> where to put this command.
    <br><br>
    Command: <strong><?= $command ?></strong>
</p>
<? if(!$found): ?>
    <div style="border: 1px solid red; background-color: #ff8469; padding: 20px; color: #fff;">
        We did not find PHP bin path. This is an estimate command. You need to ask your hosting provider whether following PHP bin path is correct: <strong><?= $phpPath ?></strong>
    </div>
<? endif; ?>
</body>
</html>


