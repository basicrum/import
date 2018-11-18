<?php

declare(strict_types=1);

require_once __DIR__ . '/beacon/resourceTimingString.php';
require_once __DIR__ . '/beacon/navigationTimingsNormalizer.php';

class BasicRum_Import_Beacon
{

    /** @var \ResourceTimingString */
    private $resourceTiming;

    private $navigationTimingsNormalizer;

    public function __construct()
    {
        $this->resourceTiming              = new ResourceTimingString();
        $this->navigationTimingsNormalizer = new BasicRum_Import_Beacon_NavigationTimingsNormalizer();
    }

    /**
     * @param array $beacons
     */
    public function extract(array &$beacons)
    {
        foreach ($beacons as $key => $beacon) {
            if (false === $beacon) {
                continue;
            }

            $date = trim($beacon[0], "'");

            $beacons[$key] = json_decode(trim(ltrim($beacon[1], "'"), "'\n"), true);
            $beacons[$key]['date'] = $date;

            $beacons[$key]['restiming']  = !empty($beacons[$key]['restiming']) ?
                $this->resourceTiming->extractPairs(json_decode($beacons[$key]['restiming'], true))
                : [];

            //$this->navigationTimingsNormalizer($beacons[$key]);
        }
    }

}