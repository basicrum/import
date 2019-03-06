<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('memory_limit',  '-1');

require_once __DIR__ . '/src/csv.php';
require_once __DIR__ . '/src/reader/compactJson.php';
require_once __DIR__ . '/src/beacon.php';
require_once __DIR__ . '/src/import/batch.php';
require_once __DIR__ . '/src/import/update.php';
require_once __DIR__ . '/src/truncate.php';

class BasicRum_Import
{

    /** @var \BasicRum_Import_Import_Batch */
    private $batchImporter;

    public function run()
    {
        $time_start = microtime(true);

        /**
         * Pass and read CLI arguments
         *
         * php run.php --lines=400 --reset-db
         */
        $cliOption = getopt('',['lines:', 'reset-db', 'file:', 'update-mode']);

        $file = !empty($cliOption['file']) ? $cliOption['file'] : false;

        if (!$file) {
            echo "Please specify file name e.g. \"php run.php --file=import-22.csv \"\n";
            exit;
        }

        echo "Importing: " . $file . "\n";

        if (isset($cliOption['reset-db'])) {
            $this->_truncate();
        }

        $this->batchImporter = new BasicRum_Import_Import_Batch();

        $importLinesCount = !empty($cliOption['lines']) ? (int) $cliOption['lines'] : false;

        $csv = new BasicRum_Import_Csv();
        $beacons = $csv->read($file, $importLinesCount);


//        $reader = new BasicRum_Import_Reader_CompactJson();
//        $beacons = $reader->read($file, $importLinesCount);

        $beaconWorker = new BasicRum_Import_Beacon();

        if (isset($cliOption['update-mode'])) {
            $durations = $beaconWorker->extractPageVisitDurations($beacons);

            $timings = $beaconWorker->extract($beacons);
            $updater = new BasicRum_Import_Import_Update();
            $updater->updateDocumentReady($timings);

            $updater->updatePageVisitsDuration($durations);
        } else {
            $timings = $beaconWorker->extract($beacons);
            $this->batchImporter->save($timings);
            $durations = $beaconWorker->extractPageVisitDurations($beacons);
            $updater = new BasicRum_Import_Import_Update();
            $updater->updatePageVisitsDuration($durations);
        }

        echo 'Imported in seconds: ' . (microtime(true) - $time_start) . "\n";
        echo "------------------------------------------------------------\n";
    }

    private function _truncate()
    {
        $truncator = new BasicRum_Import_Truncate();
        $tables = $truncator->truncateAll();

        echo "Truncated tables: \n";

        foreach ($tables as $table) {
            echo " - " . $table . "\n";
        }
    }

}

$import = new BasicRum_Import();
$import->run();