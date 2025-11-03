<?php

namespace RianWlp\Db;

class DbConnectV2
{
    public function beginTransaction(): void
    {
        $this->connect->getConnect()->beginTransaction();
    }

    public function commit(): void
    {
        $this->connect->getConnect()->commit();
    }

    public function rollback(): void
    {
        $this->connect->getConnect()->rollBack();
    }

    public function abort(): void
    {
        if ($this->connect->getConnect()->inTransaction()) {
            $this->connect->getConnect()->rollBack();
        }
    }
}
