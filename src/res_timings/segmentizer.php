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
            255 => []
        ];

        $segments = array_keys($groups);

        foreach ($resTimings as $url => $resource) {
            $length = strlen($resource);

            foreach ($segments as $segment) {
                if ($length < $segment ) {
                    $groups[$segment][$url] = $resource;
                    break;
                }
            }
        }

        return $groups;
    }
}