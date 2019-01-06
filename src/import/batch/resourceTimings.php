<?php

declare(strict_types=1);

require_once __DIR__ . '/resourceTimings/url.php';
require_once __DIR__ . '/../../lib/resourcetimingDecompression.0.3.4.php';

class BasicRum_Import_Import_Batch_ResourceTimings
{

    /** @var BasicRum_Import_Csv_Db_Connection */
    private $_connection;

    /** @var BasicRum_Import_Import_Batch_ResourceTimings */
    private $_resourceTimingsUrlModel;

    /** @var ResourceTimingDecompression_v_0_3_4 */
    private $_resourceDecompressor;

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->_connection              = $connection;
        $this->_resourceTimingsUrlModel = new BasicRum_Import_Import_Batch_ResourceTimings_Url($connection);
        $this->_resourceDecompressor    = new ResourceTimingDecompression_v_0_3_4();
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

        // Test decompress
        foreach ($resourcesBatch as $key => $row) {
            $pageViewId = $key + $lastPageViewId;


            //if ($pageViewId === 342) {
                $decompressed = $this->_resourceDecompressor->decompressResources($row);
                print_r($decompressed);
            //}
        }

        exit;

        $batchUrls = [];

        foreach ($resourcesBatch as $row) {
            $batchUrls = array_merge($batchUrls, $row);
        }

        $batchUrlsParis = $this->_prepareUrlIds($batchUrls);

        $batchInsertArray = [];

        foreach ($resourcesBatch as $key => $resource) {
            $pageViewId = $key + $lastPageViewId;


            $resources = [];

            $startTime = 0;

            $tmingsData = [];

            foreach ($resource as $url => $timing) {
                if ($pageViewId === 342 && 208 == $batchUrlsParis[$url]) {
                    var_dump($batchUrlsParis[$url]);
                    var_dump($timing);
                    var_dump($url);
                    $t = $this->_resourceDecompressor->decodeCompressedResource($timing, 2180);
                    var_dump($t);
                }

                $tmingsData[] = $this->_resourceDecompressor->decodeCompressedResource($timing, $batchUrlsParis[$url]);
            }



            // Sort by starting time
            usort($tmingsData, function($a, $b) {
                return $a['startTime'] - $b['startTime'];
            });

            if ($pageViewId === 342) {
//                print_r($resource);
//                print_r($tmingsData);
            }

            foreach ($tmingsData as $timingData) {

                $insertData = [
                    'url_id'      => $timingData['name'],
                ];

                if ($timingData['startTime'] === 0 ) {
                    $insertData['start'] = '';
                } else {
                    $offset = $timingData['startTime'] - $startTime;
                    if ($offset > 0) {
                        $insertData['start'] = base_convert($offset, 10, 36);
                    } else {
                        $insertData['start'] = '';
                    }
                }

                if ($timingData['duration'] !== 0 ) {
                    $insertData['end'] = base_convert($timingData['duration'], 10, 36);
                }

                if (!isset($insertData['end']) && $insertData['start'] == '') {
                    unset($insertData['start']);
                }

                $resources[] = implode(',', $insertData);

                $startTime = $timingData['startTime'];
            }

            $batchInsertArray[] = [
                'page_view_id'     => $pageViewId,
                'resource_timings' => implode(';',$resources),
            ];
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