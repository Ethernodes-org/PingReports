<?php

namespace AmiLabs\PingReports\DataAccess;

use PDO;
use PDOStatement;
use AmiLabs\DevKit\DataAccessPDO;
use AmiLabs\PingReports\IDataAccessLayer;

/**
 * Data Access Layer.
 */
class SQLite extends DataAccessPDO implements IDataAccessLayer
{
    /**
     * @var \PDOStatement
     */
    protected $storeStmt;

    /**
     * @var \PDOStatement
     */
    protected $borderDatesStmt;

    /**
     * Initializes layer.
     *
     * @param  array $config
     * @return void
     */
    public function init(array $config = array())
    {
        $this->connect($config);

        $query =
            "INSERT INTO `ping_result` " .
            "(`date`, `service`, `status`, `connect_time`, `total_time`) " .
            "VALUES " .
            "(:date, :service, :status, :connect_time, :total_time)";
        $this->storeStmt = $this->oDB->prepare($query);

        $query =
            "SELECT " .
                "MIN(`date`) `min_date`, " .
                "MAX(`date`) `max_date` " .
            "FROM `ping_result` " .
            "WHERE " .
                "`service` = :service";
        $this->borderDatesStmt = $this->oDB->prepare($query);
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
     * Returns min/max dates.
     *
     * @param  string $service
     * @return mixed
     */
    public function getBorderDates($service)
    {
        $record = array(
            'service' => $service,
        );
        $this->prepareRecord($record);
        $this->borderDatesStmt->execute($record);
        $return = $this->borderDatesStmt->fetch(PDO::FETCH_ASSOC);

        return $return;
    }

    /**
     * Returns records.
     *
     * @param  array  $aFields
     * @param  array  $aFilter
     * @param  int    $start
     * @param  int    $limit
     * @param  string $groupBy
     * @return array
     */
    public function get(array $aFields, array $aFilter, $start, $limit, $groupBy = '')
    {
        $query =
            "SELECT " . implode(", ", $aFields). " " .
            "FROM `ping_result` ";
        if (sizeof($aFilter) > 0) {
            $query .= "WHERE " . $this->getFilterSQL($aFilter);
        }
        if ('' !== $groupBy) {
            $query .= "GROUP BY " . $groupBy . " ";
        }
        $query .= "ORDER BY `date` ASC ";
        if ($start || $limit) {
            $query .= "LIMIT ?, ?";
        }
        $stmt = $this->oDB->prepare($query);
        $index = 0;
        $this->bindFilterValues($stmt, $aFilter, $index);
        if ($start || $limit) {
            $stmt->bindValue(++$index, $start, PDO::PARAM_INT);
            $stmt->bindValue(++$index, $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $return;
    }
}
