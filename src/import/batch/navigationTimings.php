<?php

declare(strict_types=1);

require_once __DIR__ . '/navigationTimings/url.php';
require_once __DIR__ . '/navigationTimings/userAgent.php';

class BasicRum_Import_Import_Batch_NavigationTimings
{


    /** @var BasicRum_Import_Csv_Db_Connection */
    private $_connection;

    /**
     * @var BasicRum_Import_Import_Batch_NavigationTimings_Url
     */
    private $_navigationTimingsUrlModel;

    /**
     * @var BasicRum_Import_Import_Batch_NavigationTimings_UserAgent
     */
    private $_navigationTimingsUserAgentModel;

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->_connection = $connection;
        $this->_navigationTimingsUrlModel = new BasicRum_Import_Import_Batch_NavigationTimings_Url($connection);
        $this->_navigationTimingsUserAgentModel = new BasicRum_Import_Import_Batch_NavigationTimings_UserAgent($connection);
    }

    /**
     * @param array $batch
     */
    public function batchInsert(array $batch)
    {
        $batch = $this->_prepareUrlIds($batch);
        $batch = $this->_prepareUserAgentIds($batch);
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


            $batch[$key]['url_id'] = $urls[$key];
        }

        return $batch;
    }

    /**
     * @param array $batch
     * @return array
     */
    private function _prepareUserAgentIds(array $batch)
    {
        $userAgents = $this->_navigationTimingsUserAgentModel->insertUserAgents($batch);

        foreach ($batch as $key => $row) {
            unset($batch[$key]['user_agent']);

            $batch[$key]['user_agent_id']  = $userAgents[$key]['id'];
            $batch[$key]['device_type_id'] = $userAgents[$key]['device_type_id'];
            $batch[$key]['os_id']          = $userAgents[$key]['os_id'];
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

    public function getLastId()
    {
        $q = "SELECT MAX(`page_view_id`) FROM navigation_timings";

        $res = $this->_connection->run($q);

        $data = $res->fetch_row();

        return !empty($data[0]) ? (int) $data[0] : 0;
    }

}