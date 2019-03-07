<?php

declare(strict_types=1);

require __DIR__ . '/../../../../vendor/autoload.php';

class BasicRum_Import_Batch_NavigationTimings_DeviceType
{

    private $deviceCodeInternalIdMap = [
        'mobile'  => 1,
        'desktop' => 2,
        'tablet'  => 3,
        'bot'     => 4,
        'other'   => 5
    ];

    private $deviceCodeLabeldMap = [
        'mobile'  => 'Mobile',
        'desktop' => 'Desktop',
        'tablet'  => 'Tablet',
        'bot'     => 'Bot',
        'other'   => 'Other'
    ];

    /**
     * @param string $code
     * @return int
     */
    public function getDeviceTypeIdByCode(string $code) : int
    {
        return isset($this->deviceCodeInternalIdMap[$code]) ?
            $this->deviceCodeInternalIdMap[$code] : $this->deviceCodeInternalIdMap['other'];
    }

    /**
     * @param BasicRum_Import_Csv_Db_Connection $connection
     */
    public function initDbRecords(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $this->createTable($connection);

        $countQuery = 'SELECT COUNT(*) as cnt FROM `device_types`';

        $res = $connection->run($countQuery);
        $data = mysqli_fetch_assoc($res);

        if($data['cnt'] == 0) {
            $insData = [];

            foreach ($this->deviceCodeLabeldMap as $code => $label) {
                $insData[] =
                    '(' . implode(
                            ',',
                            [
                                $this->getDeviceTypeIdByCode($code),
                                '\''. $label . '\'',
                                '\'' . $code . '\''
                            ]
                        )
                    . ')';
            }

            $columns = implode(", ",['id', 'label', 'code']);
            $valueSets  = implode(", ", $insData);
            $sql = "INSERT INTO `device_types` ($columns) VALUES $valueSets";

            $connection->run($sql);
        }
    }

    private function createTable(BasicRum_Import_Csv_Db_Connection $connection)
    {
        $query = 'CREATE TABLE IF NOT EXISTS `device_types` (
                  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
                  `label` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT \'\',
                  `code` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT \'\',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;';

        $connection->run($query);
    }

}