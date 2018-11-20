<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('memory_limit',  '-1');

require_once __DIR__ . '/src/csv.php';
require_once __DIR__ . '/src/beacon.php';
require_once __DIR__ . '/src/query/navigationTimings.php';
require_once __DIR__ . '/src/res_timings/segmentizer.php';
require_once __DIR__ . '/src/importer.php';

class BasicRum_Import
{

    /** @var \BasicRum_Import_ResTimings_Segmentizer */
    private $segmentizer;

    /** @var \BasicRum_Import_Importer */
    private $importer;


    public function __construct()
    {
        $this->segmentizer = new BasicRum_Import_ResTimings_Segmentizer();
        $this->importer    = new BasicRum_Import_Importer();
    }

    public function run()
    {
        $csv = new BasicRum_Import_Csv();
        $beacons = $csv->read(__DIR__ . '/../2018-09-03.csv');
        $beaconWorker = new BasicRum_Import_Beacon();
        $navigationTimings = $beaconWorker->extract($beacons);

        $resTimings = [];

        foreach ($beacons as $key => $beacon) {
            if (!empty($beacon['restiming'])) {
                $resTimings[$key] = $beacon['restiming'];
            }
        }

        $this->importer->save($navigationTimings);

        echo 'end';
        exit;

        $this->importer->save($segments);

        $segments = $this->segmentizer->segmentatize($resTimings);

        $this->importer->save($segments);

        foreach ($segments as $k => $data) {
            echo $k . ':  ' . count($data) . "\n";
        }

        //$imported = 'Imported ' . count($beacons) .  ' beacons';

        //echo $imported;
    }

}

$import = new BasicRum_Import();
$import->run();