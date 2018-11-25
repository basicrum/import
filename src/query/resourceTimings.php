<?php

declare(strict_types=1);

class BasicRum_Import_Csv_Query_ResourceTimings
{

    /**
     * @param string $url
     * @return string
     */
    public function urlExists(string $url)
    {
        return 'SELECT id FROM `resource_timings_urls` where url = "' . $url . '"';
    }

    /**
     * @param string $url
     * @return string
     */
    public function insertUrl(string $url)
    {
        return "INSERT INTO `resource_timings_urls`
            (url)

            VALUES ('{$url}');";
    }

    /**
     * @param string $trai
     * @param $traiType
     * @return string
     */
    public function traiExists(string $trai, $traiType)
    {
        return 'SELECT id FROM `resource_timings_segment_' . $traiType . '` where base36 = "' . $trai . '"';
    }


    /**
     * @param string $trai
     * @param $traiType
     * @return string
     */
    public function insertTrai(string $trai, $traiType)
    {
        return "INSERT INTO `resource_timings_segment_{$traiType}`
            (base36)

            VALUES ('{$trai}');";
    }

    /**
     * @param array $timingData
     * @return string
     */
    public function insertResourceTimings(array $timingData)
    {

        $fields = implode(',', array_keys($timingData[0]));

        return $query = "INSERT INTO resource_timings
            ({$fields})

            VALUES " . $this->timingValues($timingData);
    }

    /**
     * @param array $pageTimings
     * @return string
     */
    protected function timingValues(array $pageTimings)
    {
        $insert = [];

        foreach ($pageTimings as $data) {
            $values = implode(',', $data);

            $insert[] = "({$values})";
        }

        return implode(',', $insert);
    }

}