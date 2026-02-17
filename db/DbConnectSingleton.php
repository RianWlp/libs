<?php

namespace RianWlp\Db;

use RianWlp\Libs\utils\DotEnv;

class DbConnectSingleton
{
    private static \PDO $connect;

    public function __construct($connect = null)
    {
        $API_DB_HOST = DotEnv::get('API_DB_HOST');
        $API_DB_PORT = DotEnv::get('API_DB_PORT');
        $API_DB_USER = DotEnv::get('API_DB_USER');
        $API_DB_PASS = DotEnv::get('API_DB_PASS');
        $API_DB_NAME = DotEnv::get('API_DB_NAME');
        $API_DB_TYPE = DotEnv::get('API_DB_TYPE'); // Ex: 'pgsql' ou 'mysql'

        if (!isset(self::$connect)) {
            try {
                // Monta a string de conexão dinamicamente
                $dsn = "{$API_DB_TYPE}:host={$API_DB_HOST};port={$API_DB_PORT};dbname={$API_DB_NAME}";

                // Cria a conexão PDO
                self::$connect = new \PDO($dsn, $API_DB_USER, $API_DB_PASS);

                // Define o modo de erro para exceções
                self::$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                // Exibe erro de conexão (ou loga, conforme seu sistema)
                die("Erro na conexão com o banco de dados: {$e->getMessage()}");
            }
        }
    }

    /**
     * Retorna a conexão do banco
     */
    public function getConnect(): \PDO
    {
        // Tá bosta, eu sei
        if (self::$connect->inTransaction()) {
            return self::$connect;
        }

        if (!isset(self::$connect)) {
            throw new \Exception('A conexão ainda não foi inicializada!');
        }
        return self::$connect;
    }

    public function beginTransaction(): void
    {
        self::$connect->beginTransaction();
    }

    public function commit(): void
    {
        self::$connect->commit();
    }

    public function rollback(): void
    {
        self::$connect->rollBack();
    }

    public function abort(): void
    {
        if (self::$connect->inTransaction()) {
            self::$connect->rollBack();
        }
    }
}
