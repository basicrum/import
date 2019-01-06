<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('memory_limit',  '-1');

require_once __DIR__ . '/src/csv.php';
require_once __DIR__ . '/src/beacon.php';
require_once __DIR__ . '/src/import/batch.php';

class BasicRum_Import
{

    /** @var \BasicRum_Import_Import_Batch */
    private $batchImporter;


    public function __construct()
    {
        $this->batchImporter = new BasicRum_Import_Import_Batch();
    }

    public function run()
    {
        /**
         * Pass and read CLI arguments
         *
         * php run.php --lines=200 --reset-db
         */
        $cliOption = getopt('',['lines:', 'reset-db']);

        $importLinesCount = !empty($cliOption['lines']) ? (int) $cliOption['lines'] : false;

        $csv = new BasicRum_Import_Csv();
        $beacons = $csv->read(__DIR__ . '/../hl/2018-12-09.csv', $importLinesCount);
        $beaconWorker = new BasicRum_Import_Beacon();
        $timings = $beaconWorker->extract($beacons);

        $this->batchImporter->save($timings);
    }

}

$import = new BasicRum_Import();
$import->run();