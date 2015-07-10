<?php

namespace AmiLabs\PingReports;

/**
 * Data Access Layer interface.
 */
interface IDataAccessLayer
{
    const GROUP_BY_HOUR = 1;
    const GROUP_BY_DAY  = 2;

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
     * @param  array $fields
     * @return void
     */
    public function store(array $fields);

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
     * @param  array  $fields
     * @param  array  $filter
     * @param  int    $start
     * @param  int    $limit
     * @param  string $groupBy
     * @return array
     */
    public function get(array $fields, array $filter, $start, $limit, $groupBy = '');

    /**
     * Deletes records.
     *
     * @param  string $endDate
     * @return void
     */
    public function delete($endDate);
}
