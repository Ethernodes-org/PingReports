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
$view = $request->get('view', 'uptime');
if (!in_array($view, array('uptime', 'grouped-details', 'details'))) {
    $view = 'uptime';
}

$dal = DataAccess::getLayer($config->get('dataAccess/layer'));
$dal->init($config->get('dataAccess'));

// header('Content-Type: application/json');
header('Content-Type: text/javascript');
echo sprintf(
    "%s([\n" .
    "[\"%s\",%s],\n",
    $request->get('callback', 'callback'),
    $view,
    $view == 'uptime'
        ? sprintf("['%s']", implode("', '", array_map('mapService', $services)))
        : sprintf('"%s"', mapService($service))
);

switch ($view) {
    case 'uptime':
        // Get min date
        $borderDates = $dal->getBorderDates();
        // var_dump($borderDates);die;###
        if (!$borderDates) {
            // No records
            break;
        }
        /*
        $borderDates = array(
            'min_date' => '2015-07-14 09:00:00',
            'max_date' => '2015-07-14 12:00:00',
        );###
        */
        $time = strtotime($borderDates['min_date']);
        $maxDate = substr($borderDates['max_date'], 0, -6);
        $month = 0;
        do {
            $timeFrom = mktime(0, 0, 0, date('m', $time) + $month, 1, date('Y', $time));
            $yearMonthFrom = date('Y-m', $timeFrom) . '-01';
            $timeTo = mktime(0, 0, 0, date('m', $time) + $month + 1, 1, date('Y', $time));
            $yearMonthTo = date('Y-m', $timeTo) . '-01 00:00:00';

            $records = $dal->get(
                array(
                    "`service`",
                    "SUBSTR(`date`, 1, 13) `date_hour`",
                    "(CASE (`status`) WHEN '' THEN `total` ELSE SUM(1) END) `total`",
                    "(CASE (`status`) WHEN '' THEN `failed` ELSE SUM(CASE (`status`) WHEN 'F' THEN 1 ELSE 0 END) END) `failed`",
                ),
                array(
                    array(
                        'field' => 'date',
                        'op'    => '>=',
                        'value' => $yearMonthFrom,
                    ),
                    array(
                        'field' => 'date',
                        'op'    => '<',
                        'value' => $yearMonthTo,
                    ),
                ),
                0,
                0,
                "`service`, SUBSTR(`date`, 1, 13)"
            );
            $dateHourRecords = array();
            foreach ($records as $record) {
                $dateHour = $record['date_hour'];
                $svc      = $record['service'];
                unset($record['date_hour'], $record['service']);
                if (!isset($dateHourRecords[$dateHour])) {
                    $dateHourRecords[$dateHour] = array();
                }
                $dateHourRecords[$dateHour][array_search($svc, $services)] = $record;
            }
            $records = $dateHourRecords;
            unset($dateHourRecords);

            $last = FALSE;
            $rows = array();
            for ($day = 1; $day < 32; ++$day) {
                $date = sprintf("%s-%02d", date('Y-m', $timeFrom), $day);
                for ($hour = 0; $hour < 24; ++$hour) {
                    $dateHour = sprintf("%s %02d", $date, $hour);
                    if ($dateHour . ':59:59' < $borderDates['min_date']) {
                        continue;
                    }
                    $last = $dateHour >= $maxDate;
                    $runsPerHour = $dateHour < '2015-07-04 22' ? 60 : 360;
                    if ($last) {
                        $secondsLeft =
                            strtotime($borderDates['max_date']) -
                            strtotime($dateHour . ':00:00');
                        $runsPerHour =
                            ceil($secondsLeft / (60 == $runsPerHour ? 60 : 10));
                    }
                    $rows[$dateHour] = array();
                    foreach (array_keys($services) as $index) {
                        $rows[$dateHour][$index] = 0;
                    }
                    if (isset($records[$dateHour])) {
                        foreach (array_keys($services) as $index) {
                            if (isset($records[$dateHour][$index])) {
                                $total  = $records[$dateHour][$index]['total'];
                                $failed = $records[$dateHour][$index]['failed'];
                                $rows[$dateHour][$index] =
                                    sprintf(
                                        "%.2f",
                                        ($runsPerHour - ($runsPerHour - $total) - $failed) * 100 / $runsPerHour
                                    );
                            }
                        }
                    }

                    echo sprintf(
                        "[%d,%d,%d,%d,%s]%s\n",
                        date('Y', $timeFrom),
                        date('m', $timeFrom) - 1,
                        $day,
                        $hour,
                        implode(',', $rows[$dateHour]),
                        $last ? '' : ','
                    );
                    if ($last) {
                        break 3;
                    }
                }
            }
            flush();
            ++$month;

        } while (TRUE);

        break; // case 'uptime'

    case 'grouped-details':
    case 'details':
        $start = 0;
        $limit = 500;
        $first = TRUE;
        do {
            $records = $dal->get(
                'details' == $view
                    ? array(
                        'date',
                        'status',
                        'connect_time',
                        'total_time',
                    )
                    : array(
                        'date',
                        'connect_time_avg',
                        'connect_time_max',
                        'total_time_avg',
                        'total_time_max',
                        'total',
                        'failed',
                    ),
                'details' == $view
                    ? array(
                        array(
                            'field' => 'total',
                            'op'    => '',
                            'value' => '=!IS NULL',
                        ),
                        array(
                            'field' => 'service',
                            'value' => $service,
                        ),
                    )
                    : array(
                        array(
                            'field' => 'total',
                            'op'    => '',
                            'value' => '=!IS NOT NULL',
                        ),
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
                echo
                    'details' == $view
                        ? sprintf(
                            "%s[%d,%d,%d,%d,%d,%d,\"%s\",%.3f,%.3f]\n",
                            $first ? '' : ',',
                            date('Y', $time),
                            date('m', $time) - 1,
                            date('d', $time),
                            date('H', $time),
                            date('i', $time),
                            date('s', $time),
                            $record['status'],
                            $record['connect_time'],
                            $record['total_time']
                        )
                        : sprintf(
                            "%s[%d,%d,%d,%d,%.3f,%.3f,%.3f,%.3f,%d,%d]\n",
                            $first ? '' : ',',
                            date('Y', $time),
                            date('m', $time) - 1,
                            date('d', $time),
                            date('H', $time),
                            // date('i', $time),
                            // date('s', $time),
                            $record['total_time_max'],
                            $record['total_time_avg'],
                            $record['connect_time_max'],
                            $record['connect_time_avg'],
                            $record['total'],
                            $record['failed']
                        );
                $first = FALSE;
            }
            flush();

            $start += $limit;
        } while(TRUE);

        break; // case 'details'
}

echo "]);\n";

function mapService($service)
{
    return str_replace('.', '-', $service);
}
