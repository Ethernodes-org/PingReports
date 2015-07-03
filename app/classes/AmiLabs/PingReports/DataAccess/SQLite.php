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

    /**
     * Returns records.
     *
     * @param  array $aFields
     * @param  array $aFilter
     * @param  int   $start
     * @param  int   $limit
     * @return array
     */
    public function get(array $aFields, array $aFilter, $start, $limit)
    {
        $aFields = array_map(array($this, 'sanitizeFieldName'), $aFields);
        $query =
            "SELECT `" . implode("`, `", $aFields). "` " .
            "FROM `ping_result` ";
        if(sizeof($aFilter) > 0){
            $query .= "WHERE " . $this->getFilterSQL($aFilter);
        }
        $query .=
            "ORDER BY `date` ASC " .
            "LIMIT ?, ?";
        $stmt = $this->oDB->prepare($query);
        $index = 0;
        $this->bindFilterValues($stmt, $aFilter, $index);
        $stmt->bindValue(++$index, $start, PDO::PARAM_INT);
        $stmt->bindValue(++$index, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $return;
    }
}
