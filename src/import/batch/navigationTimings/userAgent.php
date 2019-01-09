<?php

declare(strict_types=1);

require __DIR__ . '/../../../../vendor/autoload.php';

class BasicRum_Import_Import_Batch_NavigationTimings_UserAgent
{

    /** @var BasicRum_Import_Csv_Db_Connection */
    private $_connection;

    /** @var array */
    private $_userAgentsPairs = [];

    /** @var int */
    private $_pairsCount = 0;

    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->_connection = $connection;

        $this->_reloadPairs();

        $this->_pairsCount = count($this->_userAgentsPairs);
    }


    /**
     * Returns pair ['navigation timing key array key' => 'user agent id']
     *
     * @param array $data
     *
     * @return array
     */
    public function insertUserAgents(array $data)
    {
        $pairs = [];

        $newUserAgentsForInsert = [];

        foreach ($data as $key => $row) {
            $userAgent = $row['user_agent'];

            if (isset($this->_userAgentsPairs[$userAgent])) {
                $pairs[$key] = $this->_userAgentsPairs[$userAgent];
            } else {
                $this->_pairsCount++;

                $result = new WhichBrowser\Parser($userAgent);

                $newUserAgentsForInsert[$key] = [
                    'user_agent'          => $userAgent,
                    'device_type'         => $result->device->type,
                    'device_model'        => $result->device->model,
                    'device_manufacturer' => $result->device->getManufacturer(),
                    'browser_name'        => $result->browser->getName(),
                    'browser_version'     => $result->browser->getVersion(),
                    'os_name'             => $result->os->getName(),
                    'os_version'          => $result->os->getVersion()
                ];

                // Speculatively append to current user agent pairs
                $this->_userAgentsPairs[$userAgent] = $this->_pairsCount;
                $pairs[$key] = $this->_pairsCount;
            }
        }

        if (!empty($newUserAgentsForInsert)) {
            $q = $this->_insertNewUserAgentsQuery($newUserAgentsForInsert);

            $this->_connection->run($q);
        }

        return $pairs;
    }

    private function _reloadPairs()
    {
        $q = "SELECT id, user_agent from navigation_timings_user_agents";

        $res = $this->_connection->run($q);

        /** @todo: Idea we may not iterate in order to fill $this->_userAgentsPairs
         * but we can use offset $key + 1 to identify the User Agent primary key.
         *
         * Not sure how much speed we can save from this.
         */
        $data = $res->fetch_all();

        foreach ($data as $row) {
            $this->_userAgentsPairs[$row[1]] = $row[0];
        }
    }

    /**
     * @param array $userAgents
     *
     * @return string
     */
    private function _insertNewUserAgentsQuery(array $userAgents)
    {
        $fieldsArr =  array_keys($userAgents[key($userAgents)]);

        $fields = implode(',', $fieldsArr);

        return "INSERT INTO navigation_timings_user_agents
            ({$fields})

            VALUES " . $this->_generateValues($userAgents);
    }

    /**
     * @param array $userAgents
     * @return string
     */
    private function _generateValues(array $userAgents)
    {
        $insert = [];

        foreach ($userAgents as $data) {

            $values = implode("','", $data);

            $insert[] = "('{$values}')";
        }

        return implode(',', $insert);
    }


}