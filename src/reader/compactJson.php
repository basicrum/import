<?php

declare(strict_types=1);

class BasicRum_Import_Reader_CompactJson
{

    /**
     * @param $path
     * @param bool $linesCount
     * @return mixed
     */
    public function read($path, $linesCount = false)
    {
        $json = file_get_contents($path);

        $data = [];

        $beacons = json_decode($json, true);

        foreach ($beacons as $beacon) {
            $data[] = [
                0 => $beacon['created_at'],
                1 => $beacon['beacon_data']
            ];
        }

        return $data;
    }

}