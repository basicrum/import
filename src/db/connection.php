<?php

declare(strict_types=1);

class BasicRum_Import_Csv_Db_Connection
{

    private $host     = '192.168.99.100';
    private $username = 'root';
    private $password = 'root';
    private $database = 'basicrum_backoffice';
    private $port     = '3306';

    /** @var  mysqli */
    private $connection;

    public function __construct()
    {
        $this->$connection = mysqli_connect(
            $this->host,
            $this->username,
            $this->password,
            $this->database,
            $this->port
        );
    }

    /**
     * @param string $query
     * @return bool|mysqli_result
     */
    public function run(string $query)
    {
        return $this->connection->query($query);
    }

}