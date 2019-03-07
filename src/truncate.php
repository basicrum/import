<?php

declare(strict_types=1);

require_once __DIR__ . '/db/connection.php';

class BasicRum_Import_Truncate
{

    public function truncateAll()
    {
        $connection = new BasicRum_Import_Csv_Db_Connection();

        $tables = [
            'navigation_timings',
            'navigation_timings_urls',
            'resource_timings',
            'resource_timings_urls',
            'navigation_timings_user_agents',
            'operating_systems',
            'device_types',
            'visits_overview',
            'navigation_timings_query_params'
        ];

        foreach ($tables as $table) {
            $connection->run('TRUNCATE TABLE ' . $table);
        }

        return $tables;
    }

}