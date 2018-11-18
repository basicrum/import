<?php

declare(strict_types=1);

class ResourceTimingString
{


    /**
     * @param array $resTimings
     * @parem string $prefix
     *
     * @return array
     */
    public function extractPairs(array $resTimings, string $prefix = '')
    {
        $resources = [];

        foreach ($resTimings as $key => $value) {
            $node = $value;
            $nodeKey = $prefix . $key;

            if (is_string($node)) {
                $resources[$nodeKey] = $node;
            } else {
                $nodeResources = $this->extractPairs($node, $nodeKey);
                $resources = array_merge($resources, $nodeResources);
            }
        }

        return $resources;
    }

}