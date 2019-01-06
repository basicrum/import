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
            'resource_timings_urls'

        ];

        foreach ($tables as $table) {
            $connection->run('TRUNCATE TABLE ' . $table);
        }

        return $tables;
    }

}