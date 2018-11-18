<?php

declare(strict_types=1);

require_once __DIR__ . '/db/connection.php';
require_once __DIR__ . '/query/tablesSegmentation.php';

class BasicRum_Import_Importer
{

    /** @var \BasicRum_Import_Csv_Db_Connection */
    private $connection;

    /** @var \BasicRum_Import_Csv_Query_TableSegmentation */
    private $tableSegmentation;


    public function __construct()
    {
        $this->connection         = new BasicRum_Import_Csv_Db_Connection();
        $this->tableSegmentation  = new BasicRum_Import_Csv_Query_TableSegmentation();
    }

    public function save(array $data)
    {
        foreach ($data as $k => $v) {
            $this->connection->run($this->tableSegmentation->createTable($k));
        }
    }

}