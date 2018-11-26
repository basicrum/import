<?php

declare(strict_types=1);

class BasicRum_Import_Import_Batch_ResourceTimings_Url
{

    /** @var BasicRum_Import_Csv_Db_Connection */
    private $_connection;

    /** @var array */
    private $_urlsPairs = [];

    /** @var int */
    private $_pairsCount = 0;

    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->_connection = $connection;

        $this->_reloadPairs();

        $this->_pairsCount = count($this->_urlsPairs);
    }


    /**
     * Returns pair ['navigation timing key array key' => 'url id']
     *
     * @param array $data
     *
     * @return array
     */
    public function insertUrls(array $data)
    {
        $pairs = [];

        $newUrlsForInsert = [];

        foreach ($data as $key => $row) {
            /**
             * @todo: So far I remove the get params to test how much DB space will be saved.
             * Reconsider anyway save some GET params somewhere in case we want to query them later.
             */
            $url = explode('?', $key)[0];

            if (isset($this->_urlsPairs[$url])) {
                $pairs[$key] = $this->_urlsPairs[$url];
            } else {
                $this->_pairsCount++;
                $newUrlsForInsert[$key] = $url;

                // Speculatively append to current url pairs
                $this->_urlsPairs[$url] = $this->_pairsCount;
                $pairs[$key] = $this->_pairsCount;
            }
        }

        if (!empty($newUrlsForInsert)) {
            $q = $this->_insertNewUrlsQuery($newUrlsForInsert);

            $this->_connection->run($q);
        }

        return $pairs;
    }

    private function _reloadPairs()
    {
        $q = "SELECT id, url from resource_timings_urls";

        $res = $this->_connection->run($q);

        /** @todo: Idea we may not iterate in order to fill $this->_urlsPairs
         * but we can use offset $key + 1 to identify the URL primary key.
         *
         * Not sure how much speed we can save from this.
         */
        $data = $res->fetch_all();

        foreach ($data as $row) {
            $this->_urlsPairs[$row[1]] = $row[0];
        }
    }

    /**
     * @param array $urls
     *
     * @return string
     */
    private function _insertNewUrlsQuery(array $urls)
    {
        return "INSERT INTO resource_timings_urls
            (url)

            VALUES ('" . $this->_generateValues($urls) . "')";
    }

    /**
     * @param array $urls
     * @return string
     */
    private function _generateValues(array $urls)
    {
        return implode("'),('", $urls);
    }

}