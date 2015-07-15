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
    protected $deleteStmt;

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
            "DELETE FROM `ping_result` " .
            "WHERE " .
                "`date` < :date AND " .
                "`total` IS NULL";
        $this->deleteStmt = $this->oDB->prepare($query);
    }

    /**
     * Stores result.
     *
     * @param  array $fields
     * @return void
     */
    public function store(array $fields)
    {
        $query =
            "INSERT INTO `ping_result` (`" .
            implode('`, `', array_keys($fields)) .
            "`) VALUES (:" .
            implode(', :', array_keys($fields)) .
            ")";
        $stmt = $this->oDB->prepare($query);

        $this->prepareRecord($fields);
        $stmt->execute($fields);
    }

    /**
     * Returns min/max dates.
     *
     * @param  array $filter
     * @return mixed
     */
    public function getBorderDates(array $filter = array())
    {
        /*
        $filter[] = array(
            'field' => 'total',
            'op'    => '',
            'value' => '=!IS NULL',
        );
        */
        $query =
            "SELECT " .
                "MIN(`date`) `min_date`, " .
                "MAX(`date`) `max_date` " .
            "FROM `ping_result`";
        if (sizeof($filter) > 0) {
            $query .= " WHERE " . $this->getFilterSQL($filter);
        }
        $stmt = $this->oDB->prepare($query);
        $index = 0;
        $this->bindFilterValues($stmt, $filter, $index);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_ASSOC);

        return $return;
    }

    /**
     * Returns records.
     *
     * @param  array  $fields
     * @param  array  $filter
     * @param  int    $start
     * @param  int    $limit
     * @param  string $groupBy
     * @return array
     */
    public function get(array $fields, array $filter, $start, $limit, $groupBy = '')
    {
        $query =
            "SELECT " . implode(", ", $fields). " " .
            "FROM `ping_result` ";
        if (sizeof($filter) > 0) {
            $query .= "WHERE " . $this->getFilterSQL($filter);
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
        $this->bindFilterValues($stmt, $filter, $index);
        if ($start || $limit) {
            $stmt->bindValue(++$index, $start, PDO::PARAM_INT);
            $stmt->bindValue(++$index, $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $return;
    }

    /**
     * Deletes records.
     *
     * @param  string $endDate
     * @return void
     */
    public function delete($endDate)
    {
        $record = array(
            'date' => $endDate,
        );
        $this->prepareRecord($record);
        $this->deleteStmt->execute($record);

        $this->oDB->exec("VACUUM");
    }
}
