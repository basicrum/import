<?php

declare(strict_types=1);

require_once __DIR__ . '/beacon/navigationTimingsNormalizer.php';

class BasicRum_Import_Beacon
{

    /** @var \BasicRum_Import_Beacon_NavigationTimingsNormalizer */
    private $navigationTimingsNormalizer;

    /** @var array */
    private $pageViewUniqueKeys = [];

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

            $pageViewKey = $this->_getPageViewKey($beacons[$key]);

            // We do not mark as page view beacons send when visitor leaves page
            if (isset ($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = ['start' => $date];

            $data[$key] = $this->navigationTimingsNormalizer->normalize($beacons[$key]);

            // Attach Resources
            $data[$key]['restiming']  = !empty($beacons[$key]['restiming']) ?
                json_decode($beacons[$key]['restiming'], true)
                : [];
        }

        return $data;
    }

    public function extractPageVisitDurations(array $beacons)
    {
        foreach ($beacons as $key => $beacon) {
            if (false === $beacon) {
                continue;
            }

            $date = trim($beacon[0], "'");

            $beacons[$key] = json_decode(trim(ltrim($beacon[1], "'"), "'\n"), true);
            $beacons[$key]['date'] = $date;

            $pageViewKey = $this->_getPageViewKey($beacons[$key]);

            // We do not mark as page view beacons send when visitor leaves page
            if (isset ($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = [
                'start' => $date,
                'guid'  => $beacons[$key]['guid'],
                'pid'   => $beacons[$key]['pid'],
                'date'  => $date
            ];
        }

        return $this->pageViewUniqueKeys;
    }

    /**
     * @return array
     */
    public function getPageViewStartEndTimes()
    {
        return $this->pageViewUniqueKeys;
    }

    private function _getPageViewKey(array $data)
    {
        return $data['guid'] . $data['pid'] . md5($data['u']);
    }

}