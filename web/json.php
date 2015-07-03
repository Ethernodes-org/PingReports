<?php

use AmiLabs\DevKit\Request;
use AmiLabs\DevKit\Registry;
use AmiLabs\PingReports\DataAccess;

/**
 * JSON-RPC service.
 */

$appName = 'json';
require_once '../app/init.php';

$config = Registry::useStorage('CFG');
$request = Request::getInstance($config->get('request/type', 'uri'));

$services = array_keys($config->get('services'));
$service = $request->get('service', FALSE);
if (
    FALSE === $service ||
    !in_array($service, $services)
) {
    $service = reset($services);
}

$dal = DataAccess::getLayer($config->get('dataAccess/layer'));
$dal->init($config->get('dataAccess'));

$start = 0;
$limit = 500;

// header('Content-Type: application/json');
header('Content-Type: text/javascript');
echo sprintf(
    "%s([\n",
    $request->get('callback', 'callback')
);

do {
    $records = $dal->get(
        array('date', 'status', 'connect_time', 'total_time'),
        array(
            array(
                'field' => 'service',
                'value' => $service,
            ),
        ),
        $start,
        $limit
    );
    $qty = sizeof($records);
    if (!$qty) {
        break;
    }
    foreach ($records as $index => $record) {
        $time = strtotime($record['date']);
        echo sprintf(
            // "[Date.UTC(%d,%d,%d,%d,%d,%d),%.4f]%s\n",
            "[Date.UTC(%d,%d,%d,%d,%d),%.4f]%s\n",
            date('Y', $time),
            date('m', $time),
            date('d', $time),
            date('H', $time),
            date('i', $time),
            // date('s', $time),
            $record['total_time'],
            ($index + 1) < $qty ? ',' : ''
        );
    }
    flush();

    $start += $limit;
}while(TRUE);

echo "]);\n";
