<?php

namespace AmiLabs\PingReports;

/**
 * Data Access Layer interface.
 */
interface IDataAccessLayer{
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
}
