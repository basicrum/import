<?php

declare(strict_types=1);

class BasicRum_Import_ResTimings_Segmentizer
{

    /**
     * @param array $resTimings
     * @return array
     */
    public function segmentatize(array $resTimings)
    {
        $groups = [
            4   => [],
            6   => [],
            8   => [],
            16  => [],
            24  => [],
            32  => [],
            64  => [],
            128 => [],
            256 => [],
            512 => []
        ];

        $segments = array_keys($groups);

        foreach ($resTimings as $resource) {
            foreach ($resource as $timing) {
                $length = strlen($timing);

                foreach ($segments as $segment) {
                    if ($length < $segment ) {
                        $groups[$segment][] = $resource;
                        break;
                    }
                }
            }
        }

        return $groups;
    }
}