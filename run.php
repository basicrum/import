<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('memory_limit',  '-1');

require_once __DIR__ . '/src/csv.php';
require_once __DIR__ . '/src/beacon.php';
require_once __DIR__ . '/src/importer.php';
require_once __DIR__ . '/src/import/batch.php';

class BasicRum_Import
{

    /** @var \BasicRum_Import_Importer */
    private $importer;


    public function __construct()
    {
        //$this->importer    = new BasicRum_Import_Importer();
        $this->importer    = new BasicRum_Import_Import_Batch(400);
    }

    public function run()
    {
        $csv = new BasicRum_Import_Csv();
        $beacons = $csv->read(__DIR__ . '/../2018-09-03.csv');
        $beaconWorker = new BasicRum_Import_Beacon();
        $timings = $beaconWorker->extract($beacons);

        $this->importer->save($timings);
    }

}

$import = new BasicRum_Import();
$import->run();