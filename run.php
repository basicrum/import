<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('memory_limit',  '-1');

require_once __DIR__ . '/src/csv.php';
require_once __DIR__ . '/src/beacon.php';
require_once __DIR__ . '/src/query/navigationTimings.php';
require_once __DIR__ . '/src/res_timings/segmentation.php';

class BasicRum_Import
{


    public function run()
    {
        $csv = new BasicRum_Import_Csv();
        $beacons = $csv->read(__DIR__ . '/2018-09-03.csv');
        $beaconWorker = new BasicRum_Import_Beacon();
        $beaconWorker->extract($beacons);

        $segmentize = new BasicRum_Import_ResTimings_Segmentation();

        $resTimings = [];

        foreach ($beacons as $key => $beacon) {
            if (!empty($beacon['restiming'])) {
                $resTimings[$key] = $beacon['restiming'];
            }
        }

        $segments = $segmentize->segmentatize($resTimings);

        foreach ($segments as $k => $data) {
            echo $k . ':  ' . count($data) . "\n";
        }

        //$imported = 'Imported ' . count($beacons) .  ' beacons';

        //echo $imported;
    }

}

$import = new BasicRum_Import();
$import->run();