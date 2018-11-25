<?php

declare(strict_types=1);

require_once __DIR__ . '/navigationTimings/url.php';

class BasicRum_Import_Import_Batch_NavigationTimings
{


    /** @var BasicRum_Import_Csv_Db_Connection */
    private $_connection;

    /**
     * @var BasicRum_Import_Import_Batch_NavigationTimings_Url
     */
    private $_navigationTimingsUrlModel;

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->_connection = $connection;
        $this->_navigationTimingsUrlModel = new BasicRum_Import_Import_Batch_NavigationTimings_Url($connection);
    }

    /**
     * @param array $batch
     */
    public function batchInsert(array $batch)
    {
        $batch = $this->_prepareUrlIds($batch);
        $q = $this->_insert($batch);

        $this->_connection->run($q);
    }

    /**
     * @param array $batch
     * @return array
     */
    private function _prepareUrlIds(array $batch)
    {
        $urls = $this->_navigationTimingsUrlModel->insertUrls($batch);

        foreach ($batch as $key => $row) {
            unset($batch[$key]['url']);

            // For testing purposes
            unset($batch[$key]['restiming']);
            unset($batch[$key]['user_agent']);


            $batch[$key]['url_id'] = $urls[$key];
        }

        return $batch;
    }

    /**
     * @param array $batch
     * @return string
     */
    private function _insert(array $batch)
    {
        $fieldsArr =  array_keys($batch[0]);

        $fields = implode(',', $fieldsArr);

        return $query = "INSERT INTO navigation_timings
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