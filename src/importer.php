<?php

declare(strict_types=1);

require_once __DIR__ . '/db/connection.php';
require_once __DIR__ . '/query/tablesSegmentation.php';
require_once __DIR__ . '/query/navigationTimings.php';

class BasicRum_Import_Importer
{

    /** @var \BasicRum_Import_Csv_Db_Connection */
    private $connection;

    /** @var \BasicRum_Import_Csv_Query_TableSegmentation */
    private $tableSegmentation;

    private $navigationTimings;

    // Small precaching trick
    private $foundUrls = [];

    public function __construct()
    {
        $this->connection         = new BasicRum_Import_Csv_Db_Connection();
        $this->tableSegmentation  = new BasicRum_Import_Csv_Query_TableSegmentation();
        $this->navigationTimings  = new BasicRum_Import_Csv_Query_NavigationTimings();
    }

    /**
     * @param array $data
     */
    public function save(array $data)
    {
        $this->connection->run($this->navigationTimings->createTable([]));

        foreach ($data as $k => $v) {
            //$this->connection->run($this->tableSegmentation->createTable($k));
            $this->insertNavigationTimings($v);
        }
    }

    /**
     * @param array $timings
     */
    private function insertNavigationTimings(array $timings)
    {
        if (!isset($this->foundUrls[$timings['url']])) {
            $res = $this->connection->run($this->navigationTimings->urlExists($timings['url']));
            $data = $res->fetch_assoc();
        } else {
            $data = ['id' => $this->foundUrls[$timings['url']]];
        }


        if (empty($data)) {
            $this->connection->run($this->navigationTimings->insertUrl($timings['url']));
            $urlId = $this->connection->getLastInsertId();
        } else {
            $urlId = $data['id'];
        }

        $this->foundUrls[$timings['url']] = $urlId;

        $timings['url_id'] = $urlId;

        $this->connection->run($this->navigationTimings->navigationTimingInsert($timings));

    }

}