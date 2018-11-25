<?php

declare(strict_types=1);

require_once __DIR__ . '/resourceTimings/url.php';

class BasicRum_Import_Import_Batch_ResourceTimings
{

    /** @var BasicRum_Import_Csv_Db_Connection */
    private $_connection;

    /**
     * @var BasicRum_Import_Import_Batch_ResourceTimings
     */
    private $_resourceTimingsUrlModel;

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->_connection = $connection;
        $this->_resourceTimingsUrlModel = new BasicRum_Import_Import_Batch_ResourceTimings_Url($connection);
    }

    /**
     * @param array $batch
     * @param int $lastPageViewId
     */
    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $resourcesBatch = [];

        // Basic filtering for rows that have restiming
        foreach ($batch as $key => $row) {
            if (!empty($row['restiming'])) {
                $resourcesBatch[$key] = $row['restiming'];
            }
        }

        $batchUrls = [];

        foreach ($resourcesBatch as $row) {
            $batchUrls = array_merge($batchUrls, $row);
        }

        $batchUrlsParis = $this->_prepareUrlIds($batchUrls);

        $batchInsertArray = [];

        foreach ($resourcesBatch as $key => $resource) {
            $pageViewId = $key + $lastPageViewId;

            foreach ($resource as $url => $timing) {
                $batchInsertArray[] = [
                    'page_view_id' => $pageViewId,
                    'url_id'       => $batchUrlsParis[$url],
                    'trai_id'      => 0,
                    'trai_type'    => 4
                ];
            }
        }

        $q = $this->_insert($batchInsertArray);

        $this->_connection->run($q);
    }

    /**
     * @param array $urlsBatch
     * @return array
     */
    private function _prepareUrlIds(array $urlsBatch)
    {
        return $this->_resourceTimingsUrlModel->insertUrls($urlsBatch);
    }

    /**
     * @param array $batch
     * @return string
     */
    private function _insert(array $batch)
    {
        $fieldsArr =  array_keys($batch[0]);

        $fields = implode(',', $fieldsArr);

        return $query = "INSERT INTO resource_timings
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