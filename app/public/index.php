<?php

// error_reporting(E_ALL);
define('APP_PATH', dirname(__FILE__) . '/..');

defined('BATCH_EXEC_ENVIRONMENT')
|| define('BATCH_EXEC_ENVIRONMENT',
(getenv('BATCH_EXEC_ENVIRONMENT') ? getenv('BATCH_EXEC_ENVIRONMENT')  : 'development'));

// PHP5.3以降では実装されているget_called_classをシミュレート実装
if (!function_exists('get_called_class')) {
    function get_called_class() {
        $bt = debug_backtrace();
        $lines = file($bt[1]['file']);
        preg_match(
            '/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/',
            $lines[$bt[1]['line']-1],
            $matches
        );
        
        return $matches[1];
    }
}

try {
    require_once APP_PATH . '/public/SplClassLoader.php';

    $classLoader = new SplClassLoader('Hmv', APP_PATH . '/../vendor/Hmv4');
    $classLoader->setNamespaceSeparator('_');
    $classLoader->register();

    $classLoader = new SplClassLoader('Tasks', APP_PATH . '/src');
    $classLoader->setNamespaceSeparator('_');
    $classLoader->register();

    $classLoader = new SplClassLoader(null, APP_PATH . '/../vendor');
    $classLoader->setNamespaceSeparator('_');
    $classLoader->register();

    $configPath = dirname(__FILE__) . '/../config/config.'. BATCH_EXEC_ENVIRONMENT . '.php';
    $config = @include $configPath;
    if($config === false) {
        throw new Exception('config load error: "' . $configPath . '" file is not found.');
    }

    // PSR-0に則りきれないのでここで強制的にrequire_onceしてしまう
    require_once APP_PATH . '/../vendor/Qdmail/qdmail.php';
    require_once APP_PATH . '/../vendor/Qdmail/qdsmtp.php';
    require_once APP_PATH . '/../vendor/Amazon/S3.php';

    include APP_PATH . '/config/services.php';

    $runner = new Teamlab_Batch_Task_Runner($di);
    $runner->run();

} catch (Exception $e) {
    
    /** @var Hmv_Mail $mailer */
    $mailer = $di->get('mailer');
 
    Hmv_Log::error($e->getMessage());
    Hmv_Log::error($e->getTraceAsString());

    echo $e->getMessage();
}
