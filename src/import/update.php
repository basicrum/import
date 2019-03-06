<?php

declare(strict_types=1);

require_once __DIR__ . '/../db/connection.php';


class BasicRum_Import_Import_Update
{

    private $_connection;

    public function __construct()
    {
        $this->_connection = new BasicRum_Import_Csv_Db_Connection();
    }

    public function updatePageVisitsDuration(array $durations)
    {
        foreach ($durations as $d) {

            if (!empty($d['guid'])) {
                $secs = 0;

                if(!empty($d['end'])) {

                    $start = strtotime($d['start']);
                    $end   = strtotime($d['end']);

                    $secs = $end - $start;
                }

                if ($secs > 65535) {
                    $secs = 65535;
                }

                $q = "UPDATE navigation_timings SET stay_on_page_time = " . $secs .  " WHERE created_at = '" . $d['date'] . "' AND guid = '" . $d['guid'] ."' AND process_id = '" . $d['pid'] . "';";
                $this->_connection->run($q);
            }
        }
    }

    public function updateDocumentReady(array $beacons)
    {
        foreach ($beacons as $beacon) {
//            print_r($beacon);

            $date = $beacon['created_at'];

            $data = $beacon;

            if (!empty($data['guid'])) {
                $secs = $data['load_event_end'];

                $q = "UPDATE navigation_timings SET load_event_end = " . $secs .  " WHERE created_at = '" . $date . "' AND guid = '" . $data['guid'] ."' AND process_id = '" . $data['process_id'] . "';";
                $this->_connection->run($q);
            }
        }
    }



}