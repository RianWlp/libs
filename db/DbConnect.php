<?php

namespace RianWlp\Db;
class DbConnect
{
    // Desenvolver classes para mexer com o commit e transacoes
    private static \PDO $connect;

    /**
     * Cria  a conexao com a base de dados e retorna
     */
    public function __construct()
    {
        try {
            if (!isset(self::$connect)) {

                // $json = json_decode(file_get_contents(ROOT_DIR . '/config/usuarios.json'));
                $json = json_decode(file_get_contents('/var/www/html/sistema-eventos-arquitetura-software/libs/config/usuarios.json'));
                self::$connect = new \PDO("pgsql:host={$json->host} port={$json->port} dbname={$json->name} user={$json->user} password={$json->pass}");
                self::$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            return self::$connect;
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Retorna a conexao do banco
     */
    public static function getConnect()
    {
        return self::$connect;
    }

    // BEGIN
    // COMMIT
    // ROLLBACK
    // ABORT
}
