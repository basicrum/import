<?php

declare(strict_types=1);

class BasicRum_Import_Csv_Query_NavigationTimings
{

    /**
     * @param array $navigationTimings
     * @return string
     */
    public function createTable(array $navigationTimings)
    {
        $small_INT = "`%s` smallint(5) unsigned NOT NULL";

        $smallIntColumns = [];

        $small_INT_List = [
            'dns_duration',
            'connect_duration',
            'first_byte',
            'redirect_duration',
            'last_byte_duration',
            'first_paint',
            'first_contentful_paint'
        ];

        foreach ($small_INT_List as $v) {
            $smallIntColumns[] = sprintf($small_INT, $v);
        }

        $createTable =
            "CREATE TABLE IF NOT EXISTS `navigation_timings` (
              `page_view_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              %s,
              `redirects_count` tinyint(1) NOT NULL DEFAULT 0,
              `url_id` int(11) NOT NULL DEFAULT 0,
              `process_id` char(8) NOT NULL DEFAULT '',
              `guid` char(128) NOT NULL DEFAULT '',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`page_view_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=4;";

        return sprintf($createTable, implode(',', $smallIntColumns));
    }

    /**
     * @param array $navigationTimings
     * @return string
     */
    public function navigationTimingInsert(array $navigationTimings)
    {
        // Temporary hacking
        unset($navigationTimings['url']);
        unset($navigationTimings['user_agent']);

        $t = 'navigation_timings';

        $f = implode(',', array_keys($navigationTimings));
        $v = "'" . implode("','", array_values($navigationTimings)) . "'";

        $q = "INSERT INTO %s (%s) VALUES (%s)";

        return sprintf($q, $t, $f, $v);
    }

    public function urlExists(string $url)
    {
        return 'SELECT id FROM `navigation_timings_urls` where url = "' . $url . '"';
    }

    public function insertUrl(string $url)
    {
        return "INSERT INTO `navigation_timings_urls`
            (url)

            VALUES ('{$url}');";
    }

}