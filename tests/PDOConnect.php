<?php

class PDOConnect
{
    private string $username;
    private string $password;
    private string $dbname;
    private string $hostname;
    private string $port;
    private string $driverType;

    private ?PDO $pdo = null;
    private ?PDOStatement $preparedStatement = null;
    private ?string $standardStatement = null;

    private function loadConfigurations()
    {
        $this->setUsername(DotEnv::get('API_DB_USER'));
        $this->setPassword(DotEnv::get('API_DB_PASS'));
        $this->setDbName(DotEnv::get('API_DB_NAME'));
        $this->setHostname(DotEnv::get('API_DB_HOST'));
        $this->setPort(DotEnv::get('API_DB_PORT'));
        $this->setDriverType(DotEnv::get('API_DB_TYPE'));

        return true;
    }

    /**
     * Getters & Setters
     */
    private function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }
    public function getPdo()
    {
        return $this->pdo;
    }
    private function setUsername($username)
    {
        $this->username = $username;
    }
    private function getUsername()
    {
        return $this->username;
    }
    private function setPassword($password)
    {
        $this->password = $password;
    }
    private function getPassword()
    {
        return $this->password;
    }
    private function setDbName($dbname)
    {
        $this->dbname = $dbname;
    }
    private function getDbName()
    {
        return $this->dbname;
    }
    private function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }
    private function getHostName()
    {
        return $this->hostname;
    }
    private function setPort($port)
    {
        $this->port = $port;
    }
    private function getPort()
    {
        return $this->port;
    }
    private function setDriverType($driverType)
    {
        $this->driverType = $driverType;
    }
    private function getDriveType()
    {
        return $this->driverType;
    }
}
