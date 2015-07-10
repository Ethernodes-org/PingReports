<?php

use AmiLabs\DevKit\Registry;
use AmiLabs\PingReports\DataAccess;

error_reporting(E_ALL);

function writeLog($message)
{
    echo sprintf("[ %s ] %s\n", date('Y-m-d H:i:s'), $message);
    flush();
}

$appName = 'gruppen';
require_once '../app/init.php';

$config = Registry::useStorage('CFG')->get();
$dal = DataAccess::getLayer($config['dataAccess']['layer']);
$dal->init($config['dataAccess']);

$time = mktime(24, 0, 0, date('n'), date('j') - $config['daysNoGruppen'], date('Y'));
$endDate = date('Y-m-d', $time) . ' 00:00:00';

writeLog(sprintf("Grouping records until %s...", $endDate));
$len = 13;
$records = $dal->get(
    array(
        "`service`",
        sprintf("SUBSTR(`date`, 1, %d) `date`", $len),
        "SUM(1) `total`",
        "SUM(CASE (`status`) WHEN 'F' THEN 1 ELSE 0 END) `failed`",
        "(SUM(`connect_time`) / SUM(1)) `connect_time_avg`",
        "MAX(`connect_time`) `connect_time_max`",
        "(SUM(`total_time`) / SUM(1)) `total_time_avg`",
        "MAX(`total_time`) `total_time_max`",
    ),
    array(
        array(
            'field' => 'total',
            'op'    => '',
            'value' => '=!IS NULL',
        ),
        array(
            'field' => 'date',
            'op'    => '<',
            'value' => $endDate,
        ),
    ),
    0,
    0,
    sprintf("`service`, SUBSTR(`date`, 1, %d)", $len)
);

writeLog("Inserting grouped records...");
foreach ($records as $record) {
    $record['date'] .= ':00:00';
    $record['status'] = '';
    $dal->store($record);
}

writeLog("Wiping detailed records...");
$dal->delete($endDate);

writeLog("Complete.");
