<?php

declare(strict_types=1);

require_once __DIR__ . '/beacon/navigationTimingsNormalizer.php';

class BasicRum_Import_Beacon
{

    private $navigationTimingsNormalizer;

    public function __construct()
    {
        $this->navigationTimingsNormalizer = new BasicRum_Import_Beacon_NavigationTimingsNormalizer();
    }

    /**
     * @param array $beacons
     *
     * @return array
     */
    public function extract(array $beacons)
    {
        $data = [];

        foreach ($beacons as $key => $beacon) {
            if (false === $beacon) {
                continue;
            }

            $date = trim($beacon[0], "'");

            $beacons[$key] = json_decode(trim(ltrim($beacon[1], "'"), "'\n"), true);
            $beacons[$key]['date'] = $date;

            $data[$key] = $this->navigationTimingsNormalizer->normalize($beacons[$key]);

            // Attach Resources
            $data[$key]['restiming']  = !empty($beacons[$key]['restiming']) ?
                json_decode($beacons[$key]['restiming'], true)
                : [];
        }

        return $data;
    }

}