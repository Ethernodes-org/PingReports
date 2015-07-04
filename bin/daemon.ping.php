<?php

use AmiLabs\DevKit\Registry;
use AmiLabs\PingReports\DataAccess;

$appName = 'ping';
require_once '../app/init.php';

if (2 != $argc) {
    echo
        "Ping daemon\n\n",
        "Usage: /path/to/php daemon.ping.php service\n\n";
    die;
}

$service = $argv[1];
$config = Registry::useStorage('CFG')->get();

if (!isset($config['services'][$service])) {
    echo sprintf("Unknown service alias '%s'!\n\n", $service);
    die;
}

file_put_contents(
    dirname(__FILE__) . '/daemon.ping.log',
    sprintf(
        "[%s] %s\n",
        date('Y-m-d H:i:s'),
        $service
    ),
    FILE_APPEND | LOCK_EX
);

$serviceConfig = $config['services'][$service];
foreach ($serviceConfig['code'] as $statement) {
    eval($statement);
}

$response = NULL;
$success  = FALSE;
$time     = microtime(TRUE);
try {
    $response =
        call_user_func_array(
            array($caller, $serviceConfig['method']),
            $serviceConfig['args']
        );
    if (is_callable(array($caller, 'getTransport'))) {
        $transportInfo = $caller->getTransport()->getInfo();
    } else if (is_callable(array($caller, 'getInfo'))) {
        $transportInfo = $caller->getInfo();
    } else {
        $time = microtime(TRUE) - $time;
        $transportInfo = array(
            'connect_time' => 0,
            'total_time'   => $time,
        );
    }

    // $success variable could be set
    foreach ($serviceConfig['checkCode'] as $statement) {
        eval($statement);
    }
} catch (Exception $exception) {
    // throw $exception;
    $time = microtime(TRUE) - $time;
    $transportInfo = array(
        'connect_time' => $time,
        'total_time'   => $time,
    );
}

$dal = DataAccess::getLayer($config['dataAccess']['layer']);
$dal->init($config['dataAccess']);
$dal->store(
    date('Y-m-d H:i:s'),
    $service,
    $success ? 'S' : 'F',
    $transportInfo['connect_time'],
    $transportInfo['total_time']
);
