<?php

namespace RianWlp\Db;

class DbConnect
{
    private static \PDO $connect;

    /**
     * Construtor que aceita um caminho de arquivo de configuração JSON
     */

    public function __construct(mixed $connection = null)
    {
        try {

            // Se o parâmetro for um array, converte para objeto
            $connection = is_array($connection) ? (object) $connection : $connection;

            if (is_object($connection)) {
                self::initializeConnection($connection);
                return;
            }

            if (is_string($connection)) {

                if (file_exists($connection)) {
                    // Se o parâmetro for uma string, presume-se que seja um caminho de arquivo JSON
                    $json = json_decode(file_get_contents($connection));
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Erro ao decodificar o JSON do arquivo.');
                    }
                    self::initializeConnection($json);
                } else {
                    throw new \Exception('O arquivo especificado não foi encontrado.');
                }
            }

            if (is_null($connection)) {
                // Se nenhum parâmetro for fornecido, usa um arquivo de configuração padrão
                $defaultFile = __DIR__ . '/../config/eventos.json';
                if (file_exists($defaultFile)) {
                    $json = json_decode(file_get_contents($defaultFile));
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Erro ao decodificar o JSON do arquivo padrão.');
                    }
                    self::initializeConnection($json);
                } else {
                    throw new \Exception('Arquivo de configuração padrão não encontrado.');
                }
                return;
            }
            throw new \Exception('Tipo de conexão não suportado.');
        } catch (\Exception $e) {
            die("Erro de configuração: " . $e->getMessage());
        }

        // try {
        // if (!isset(self::$connect)) {

        //     if (empty($file)) {
        //         $file = __DIR__ . '/../config/eventos.json';
        //         $json = json_decode(file_get_contents($file));
        //         self::$connect = new \PDO("pgsql:host={$json->host} port={$json->port} dbname={$json->name} user={$json->user} password={$json->pass}");
        //         self::$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        //     } else {
        //         $this->initializeFromFile($file);
        //     }
        // }
        // } catch (\Exception $e) {
        //     die("Erro de configuração: " . $e->getMessage());
        // }
    }

    /**
     * Inicializa a conexão a partir de um arquivo JSON
     */
    private function initializeFromFile(string $file): void
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new \Exception("Arquivo de configuração não encontrado ou não pode ser lido: {$file}");
        }

        $json = json_decode(file_get_contents($file));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Erro ao decodificar o arquivo JSON: ' . json_last_error_msg());
        }

        $this->initializeConnection($json);
    }

    /**
     * Inicializa a conexão a partir de um JSON dinâmico
     */
    public static function initializeFromJson(object $config): void
    {
        (new self(''))->initializeConnection($config);
        // if (!isset(self::$connect)) {
        //     (new self(""))->initializeConnection($config);
        // }
    }

    /**
     * Configura a conexão com o banco a partir de um objeto JSON
     */
    private function initializeConnection(object $config): void
    {
        if (!isset($config->host, $config->port, $config->name, $config->user, $config->pass)) {
            throw new \Exception('Configuração inválida. Certifique-se de que todas as propriedades (host, port, name, user, pass) estão presentes.');
        }

        try {
            self::$connect = new \PDO(
                "pgsql:host={$config->host};port={$config->port};dbname={$config->name}",
                $config->user,
                $config->pass
            );

            self::$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Retorna a conexão do banco
     */
    public static function getConnect(): \PDO
    {
        if (!isset(self::$connect)) {
            throw new \Exception("Conexão ainda não foi inicializada.");
        }

        return self::$connect;
    }

    // BEGIN
    // COMMIT
    // ROLLBACK
    // ABORT
}
