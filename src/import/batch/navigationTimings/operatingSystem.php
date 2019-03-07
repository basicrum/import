<?php

declare(strict_types=1);

require __DIR__ . '/../../../../vendor/autoload.php';

class BasicRum_Import_Batch_NavigationTimings_OperatingSystem
{

    private $osCodeIdMap = [];

    /** @var BasicRum_Import_Csv_Db_Connection */
    private $connection;

    public function __construct(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->connection = $connection;
        $this->createTable($connection);
        $this->reloadCodeIdMap($connection);
    }

    /**
     * @param string $name
     * @return int
     */
    public function getOsIdByName(string $name) : int
    {
        if (empty($name)) {
            $name = 'Unknown';
        }

        $code = $this->getCodeByName($name);

        return isset($this->osCodeIdMap[$code]) ?
            $this->osCodeIdMap[$code] : $this->insertOs($name, $code);
    }

    /**
     * @param string $name
     * @param string $code
     * @return int
     */
    private function insertOs(string $name, string $code) : int
    {
        $name = trim($name);

        $sql = "INSERT INTO `operating_systems` (`label`, `code`) VALUES ('{$name}', '{$code}')";

        $this->connection->run($sql);

        $id = count($this->osCodeIdMap) + 1;

        $this->osCodeIdMap[$code] = $id;

        return $id;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getCodeByName(string $name)
    {
        return strtolower(
            str_replace(
                ' ',
                '_',
                trim(
                    $name
                )
            )
        );
    }

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    private function createTable(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $query = 'CREATE TABLE IF NOT EXISTS `operating_systems` (
                  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
                  `label` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT \'\',
                  `code` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT \'\',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;';

        $connection->run($query);
    }

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    private function reloadCodeIdMap(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $q = "SELECT `id`, `code` from operating_systems";

        $res = $this->connection->run($q);

        $data = $res->fetch_all();

        foreach ($data as $row) {
            $this->osCodeIdMap[$row[1]] = $row[0];
        }
    }

}