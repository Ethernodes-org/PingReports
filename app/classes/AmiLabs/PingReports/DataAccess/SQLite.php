<?php

namespace AmiLabs\PingReports\DataAccess;

use PDO;
use PDOStatement;
use AmiLabs\DevKit\DataAccessPDO;
use AmiLabs\PingReports\IDataAccessLayer;

/**
 * Data Access Layer.
 */
class SQLite extends DataAccessPDO implements IDataAccessLayer{
    /**
     * @var \PDOStatement
     */
    protected $storeStmt;

    /**
     * Initializes layer.
     *
     * @param  array $config
     * @return void
     */
    public function init(array $config = array()){
        $this->connect($config);

        $query =
            "INSERT INTO `ping_result` " .
            "(`date`, `service`, `status`, `connect_time`, `total_time`) " .
            "VALUES " .
            "(:date, :service, :status, :connect_time, :total_time)";
        $this->storeStmt = $this->oDB->prepare($query);
    }

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
    public function store($date, $service, $status, $connectTime, $totalTime)
    {
        $record = array(
            'date'         => $date,
            'service'      => $service,
            'status'       => $status,
            'connect_time' => $connectTime,
            'total_time'   => $totalTime,
        );
        $this->prepareRecord($record);
        $this->storeStmt->execute($record);
    }
}
