<?php

namespace RianWlp\Libs\core;

use RianWlp\Libs\utils\DotEnv;
use stdClass;

class ActiveRecordsV2
{
    protected string $table_name;
    protected string $primary_key;

    protected $connect;

    public function __construct($connect)
    {
        $this->connect = $connect;
    }

    // Sei la, ta meio bosta esse metodo
    public function store()
    {
        $id = trim($this->primary_key); // Obtém o nome da chave primária
        if (!empty($id)) {
            // Armazena as propriedades do objeto em uma variável
            $obj_vars = get_object_vars($this);
            return isset($obj_vars[$id]) && $obj_vars[$id] ? $this->update()  : $this->insert();
        }
    }

    private function insert(): bool
    {
        $firt_arguments   = null;
        $secord_arguments = null;

        // Eu poderia primeiro limpar e depois interar
        $vars             = self::getVars();

        foreach ($vars as $key => $var) {

            if (!$var) {
                continue;
            }
            $firt_arguments   .= "$key,";
            $secord_arguments .= ":$key,";
        }

        $firt_arguments   = trim($firt_arguments, ',');
        $secord_arguments = trim($secord_arguments, ',');

        $sql  = "INSERT INTO {$this->table_name} ($firt_arguments) VALUES ($secord_arguments)";
        $stmt = $this->connect->getConnect()->prepare($sql);

        $firt_arguments = explode(',', $firt_arguments);

        foreach ($firt_arguments as $key => $column) {

            $column = array_shift(explode(' = ', $column));
            $stmt->bindValue(":$column", (string)$vars[$column]);
        }
        return $stmt->execute();
    }

    private function update(): bool
    {
        $id     = $this->primary_key;
        $tabela = $this->table_name;

        $arguments = null;
        $vars      = self::getVars();

        $updated_at = DotEnv::get('UPDATED_AT_FIELD');
        if (DotEnv::get('UPDATED_AT_FIELD')) {
            $vars[$updated_at] = date('Y-m-d H:i:s');
        }

        foreach ($vars as $key => $var) {

            if ((!$var) || ($key == $id)) {
                continue;
            }
            $arguments .= "$key = :$key,";
        }
        $arguments = trim($arguments, ',');

        $sql = "UPDATE $tabela SET $arguments WHERE $id = :$id;";

        $arguments = $arguments . ',id = :id';

        $stmt = $this->connect->getConnect()->prepare($sql);

        $arguments = explode(',', $arguments);
        foreach ($arguments as $key => $column) {

            $column = array_shift(explode(' = ', $column));
            $stmt->bindValue(":$column", $vars[$column]);
        }
        return $stmt->execute();
    }

    private function getVars(): array
    {
        $vars = get_object_vars($this);
        unset($vars['connect']);
        unset($vars['table_name']);
        unset($vars['primary_key']);

        return $vars;
    }

    public function getAll(): ?array
    {
        $tabela = $this->table_name;

        $sql = "SELECT * FROM $tabela";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        $ocorrencias = null;
        while ($ocorrencia = $stmt->fetchObject()) {
            $ocorrencias[] = $ocorrencia;
        }
        return $ocorrencias;
    }

    public function getAllByKeys(array $filters): array
    {
        $tabela = $this->table_name;

        // Constrói a cláusula WHERE dinamicamente com base no array de filtros
        $where_clauses = [];
        foreach ($filters as $key => $value) {
            $where_clauses[] = "$key = :$key";
        }

        $whereSql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        $sql = "SELECT * FROM $tabela $whereSql";

        $stmt = $this->connect->getConnect()->prepare($sql);

        // Associa os valores dinamicamente
        foreach ($filters as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        $ocorrencias = [];
        while ($ocorrencia = $stmt->fetchObject()) {
            $ocorrencias[] = $ocorrencia;
        }

        return $ocorrencias;
    }

    public function getByKeys(array $filters): ?stdClass
    {
        $tabela = $this->table_name;

        // Constrói a cláusula WHERE dinamicamente com base no array de filtros
        $where_clauses = [];
        foreach ($filters as $key => $value) {
            $where_clauses[] = "$key = :$key";
        }

        $whereSql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        $sql = "SELECT * FROM $tabela $whereSql LIMIT 1"; // Adiciona LIMIT 1 para garantir apenas um registro

        $stmt = $this->connect->getConnect()->prepare($sql);

        // Associa os valores dinamicamente
        foreach ($filters as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        // Retorna apenas um único registro como objeto ou null se nenhum for encontrado
        return $stmt->fetchObject() ?: null;
    }

    public function getAllByKey(string $key, string $value): ?array
    {
        $tabela = $this->table_name;

        $sql = "SELECT * FROM $tabela WHERE $key = :value";

        // Prepara e executa a consulta
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        // Busca os resultados e retorna como array
        $ocorrencias = null;
        while ($ocorrencia = $stmt->fetchObject()) {
            $ocorrencias[] = $ocorrencia;
        }

        return $ocorrencias;
    }

    public function getByKey(string $key, string $value): stdClass
    {
        $tabela = $this->table_name;

        $sql = "SELECT * FROM $tabela WHERE $key = :$key LIMIT 1;";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$key", $value);

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function getById(int $id): stdClass
    {
        $tabela = $this->table_name;

        $sql = "SELECT * FROM $tabela WHERE id = :id";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(':id', $id);

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function softDeleteBy(string $column, string $value): bool
    {
        $tabela = $this->table_name; // Nome da tabela definido na classe

        $deleted_at = DotEnv::get('DELETED_AT_FIELD');
        if (DotEnv::get('DELETED_AT_FIELD')) {
            $vars[$deleted_at] = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE $tabela SET $deleted_at = CURRENT_TIMESTAMP WHERE :$column = $value";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$column", $value);

        return $stmt->execute();
    }

    public function hardDeleteAll(): bool
    {
        $tabela = $this->table_name;

        $sql = "DELETE FROM $tabela;";
        $stmt = $this->connect->getConnect()->prepare($sql);

        return $stmt->execute();
    }

    public function hardDeleteBy(string $column, $value): void
    {
        $table = $this->table_name; // Nome da tabela definido na classe
        $sql = "DELETE FROM $table WHERE $column = :value";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$column", $value);

        $stmt->execute();
    }

    public function getLastId(): ?int
    {
        // Isso aqui vou ter que dar uma olhada
        $id     = $this->primary_key;
        $tabela = $this->table_name;

        $sql = "SELECT MAX($id) FROM $tabela;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_OBJ)->max;
    }

    public function exists(string $key, string $value): bool
    {
        $tabela = $this->table_name;

        $sql = "SELECT $key FROM $tabela WHERE $key = :$key LIMIT 1;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$key", $value);

        $stmt->execute();

        // Retorna true se o registro existe, false caso contrário
        return (bool) $stmt->fetchColumn();
    }
}
