<?php
require_once __DIR__ . '/db/connection.php';
require_once __DIR__ . '/query/tablesSegmentation.php';
require_once __DIR__ . '/query/navigationTimings.php';
require_once __DIR__ . '/query/resourceTimings.php';
require_once __DIR__ . '/res_timings/segmentizer.php';

class BasicRum_Import_Importer
{

    /** @var \BasicRum_Import_Csv_Db_Connection */
    private $connection;

    /** @var \BasicRum_Import_Csv_Query_TableSegmentation */
    private $tableSegmentation;

    /** @var \BasicRum_Import_Csv_Query_NavigationTimings */
    private $navigationTimings;

    /** @var \BasicRum_Import_Csv_Query_ResourceTimings */
    private $resourceTimings;

    /** @var \BasicRum_Import_ResTimings_Segmentizer */
    private $segmentizer;

    // Small precaching trick
    /** @var array */
    private $foundUrls = [];

    /** @var array */
    private $foundResourceUrls = [];

    /** @var array */
    private $foundResourceTrais = [];



    public function __construct()
    {
        $this->connection         = new BasicRum_Import_Csv_Db_Connection();
        $this->tableSegmentation  = new BasicRum_Import_Csv_Query_TableSegmentation();
        $this->navigationTimings  = new BasicRum_Import_Csv_Query_NavigationTimings();
        $this->resourceTimings    = new BasicRum_Import_Csv_Query_ResourceTimings();
        $this->segmentizer        = new BasicRum_Import_ResTimings_Segmentizer();
    }

    /**
     * @param array $data
     */
    public function save(array $data)
    {
        $this->connection->run($this->navigationTimings->createTable([]));

        foreach ($data as $k => $v) {
            $resTimings = $v['restiming'];
            unset($v['restiming']);

            //$this->connection->run($this->tableSegmentation->createTable($k));
            $pageViewId = $this->insertNavigationTimings($v);

            if (!empty($resTimings)) {
                $this->insertResourceTimings($resTimings, $pageViewId);
            }
        }
    }

    /**
     * @param array $timings
     *
     * @return int
     */
    private function insertNavigationTimings(array $timings)
    {
//        if (!isset($this->foundUrls[$timings['url']])) {
//            $res = $this->connection->run($this->navigationTimings->urlExists($timings['url']));
//            $data = $res->fetch_assoc();
//        } else {
//            $data = ['id' => $this->foundUrls[$timings['url']]];
//        }


//        if (empty($data)) {
            $this->connection->run($this->navigationTimings->insertUrl($timings['url']));
            $urlId = $this->connection->getLastInsertId();
//        } else {
//            $urlId = $data['id'];
//        }

        $this->foundUrls[$timings['url']] = $urlId;

        $timings['url_id'] = $urlId;

        $this->connection->run($this->navigationTimings->navigationTimingInsert($timings));

        return $this->connection->getLastInsertId();
    }

    /**
     * @param array $timings
     * @param int
     */
    private function insertResourceTimings(array $timings, $pageViewId)
    {
        $insertArr = [];

        $segments = $this->segmentizer->segmentatize($timings);

        foreach ($segments as $segmentKey => $pairs) {
            foreach ($pairs as $url => $trai) {
                $insertArr[] = [
                    'url_id'       => $this->_insertResourceUrl($url),
                    'trai_id'      => $this->_insertResourceTray($trai, $segmentKey),
                    'page_view_id' => $pageViewId,
                    'trai_type'    => $segmentKey
                ];
            }
        }

        $this->connection->run($this->resourceTimings->insertResourceTimings($insertArr));
    }

    private function _insertResourceUrl($url)
    {
        $url = explode('?', $url)[0];

//        if (!isset($this->foundResourceUrls[$url])) {
//            $urlRes = $this->connection->run($this->resourceTimings->urlExists($url));
//            $data = $urlRes->fetch_assoc();
//        } else {
//            $data = ['id' => $this->foundResourceUrls[$url]];
//        }
//
//        if (empty($data)) {
            $this->connection->run($this->resourceTimings->insertUrl($url));
            $urlId = $this->connection->getLastInsertId();
//            $this->foundResourceUrls[$url] = $urlId;
//        } else {
//            $urlId = $data['id'];
//        }

        return $urlId;
    }

    private function _insertResourceTray($trai, $traiType)
    {
//        if (!isset($this->foundResourceTrais[$trai])) {
//            $traiRes = $this->connection->run($this->resourceTimings->traiExists($trai, $traiType));
//            $data = $traiRes->fetch_assoc();
//            $this->foundResourceTrais[$trai] = $data;
//        } else {
//            $data = $this->foundResourceTrais[$trai];
//        }
//
//        if (empty($data)) {
            $this->connection->run($this->resourceTimings->insertTrai($trai, $traiType));
            $traiId = $this->connection->getLastInsertId();
//            $this->foundResourceTrais[$trai] = ['id' => $traiId];
//        } else {
//            $traiId = $data['id'];
//        }

        return $traiId;
    }

}