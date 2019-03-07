<?php

declare(strict_types=1);

class BasicRum_Import_Batch_NavigationTimings_QueryParams
{

    /** @var BasicRum_Import_Csv_Db_Connection */
    private $connection;

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $batch
     * @param int $lastPageViewId
     */
    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $lastPageViewIdStartOffset = $lastPageViewId + 1;

        $resourcesBatch = [];

        foreach ($batch as $key => $row) {
            if (!empty($row['query_params'])) {
                $resourcesBatch[$key] = mysqli_real_escape_string($this->connection->getConnection(), $row['query_params']);
            }
        }

        $batchInsertArray = [];

        foreach ($resourcesBatch as $key => $resource) {
            $pageViewId = $key + $lastPageViewIdStartOffset;

            $batchInsertArray[] = [
                'page_view_id' => $pageViewId,
                'query_params' => $resource,
            ];
        }

        if (!empty($batchInsertArray)) {
            $q = $this->_insert($batchInsertArray);

            $this->connection->run($q);
        }
    }

    /**
     * @param array $batch
     * @return string
     */
    private function _insert(array $batch)
    {
        $fieldsArr =  array_keys($batch[0]);

        $fields = implode(',', $fieldsArr);

        return $query = "INSERT INTO navigation_timings_query_params
            ({$fields})

            VALUES " . $this->_generateValues($batch);
    }

    /**
     * @param array $batch
     * @return string
     */
    private function _generateValues(array $batch)
    {
        $insert = [];

        foreach ($batch as $data) {
            //$data = array_filter($data, 'is_scalar');

            $values = implode("','", $data);

            $insert[] = "('{$values}')";
        }

        return implode(',', $insert);
    }

}