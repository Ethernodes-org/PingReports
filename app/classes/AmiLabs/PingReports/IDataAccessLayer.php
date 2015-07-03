<?php

namespace AmiLabs\PingReports;

/**
 * Data Access Layer interface.
 */
interface IDataAccessLayer
{
    /**
     * Initializes layer.
     *
     * @param  array $config
     * @return void
     */
    public function init(array $config = array());

    /**
     * Stores result.
     *
     * @param  string $date
     * @param  string $service
     * @param  string $status
     * @param  double $connectTime
     * @param  double $totalTime
     * @return void
     */
    public function store($date, $service, $status, $connectTime, $totalTime);

    /**
     * Returns min/max dates.
     *
     * @param  string $service
     * @return mixed
     */
    public function getBorderDates($service);

    /**
     * Returns specified servce records.
     *
     * @param  array $aFields
     * @param  array $aFilter
     * @param  int   $start
     * @param  int   $limit
     * @param  string $groupBy
     * @return array
     */
    public function get(array $aFields, array $aFilter, $start, $limit, $groupBy = '');
}
