<?php

declare(strict_types=1);

require_once __DIR__ . '/batch/navigationTimings.php';
require_once __DIR__ . '/../db/connection.php';


class BasicRum_Import_Import_Batch
{

    private $_batchSize;

    private $_connection;

    private $_navigationTimings;

//    private $navigationTimingId;
//    private $navigationTimingUrlId;
//
//
//    private $resourceTimingId;
//    private $resourceUrlTimingId;
//
//    private $resourceTimingsSegmentsIds = [];

    /**
     * @param int $batchSize
     *
     */
    public function __construct(int $batchSize = 200)
    {
        $this->_batchSize  = $batchSize;
        $this->_connection = new BasicRum_Import_Csv_Db_Connection();

        $this->_navigationTimings = new BasicRum_Import_Import_Batch_NavigationTimings($this->_connection);
    }

    /**
     * @param array $data
     */
    public function save(array $data)
    {
        $batch = [];

        $counter = 0;

        foreach ($data as $page) {
            $counter++;

            $batch[] = $page;

            if (0 === $counter % $this->_batchSize) {
                $this->process($batch);
                unset($batch);
                $batch = [];
            }
        }

        // In case we have leftovers or initial batch wasn't completely fulfilled
        if (!empty($batch)) {
            $this->process($batch);
        }
    }

    /**
     * @param array $views
     */
    private function process(array $views)
    {
        $this->_navigationTimings->batchInsert($views);
    }

}