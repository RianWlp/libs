<?php

namespace RianWlp\Libs\core;

use RianWlp\Db\DbConnect;

abstract class ActiveRecords
{
    protected $connect;

    public function __construct(DbConnect $connect)
    {
        $this->connect = $connect;
    }

    public function store()
    {
        $id = trim($this::_primaryKey); // Obtém o nome da chave primária
        if (!empty($id)) {
            $objectVars = get_object_vars($this); // Armazena as propriedades do objeto em uma variável
            // return isset($objectVars[$id]) && $objectVars[$id]  ? $this->update($this->connect)  : $this->insert($this->connect);
            return isset($objectVars[$id]) && $objectVars[$id]  ? $this->update()  : $this->insert();
        }

        // $id = trim($this::_primaryKey);
        // if (!empty($id)) {
        //     return get_object_vars($this)[$id] ? $this->update($this->connect) : $this->insert($this->connect);
        // }
    }

    private function executeSQL($stmt): void
    {
        $stmt->execute();
    }

    private function insert()
    {
        $tabela = $this::_tableName;

        $firtArguments   = null;
        $secordArguments = null;
        $vars = self::getVars();

        foreach ($vars as $key => $var) {

            if (!$var) {
                continue;
            }
            $firtArguments   .= "$key,";
            $secordArguments .= ":$key,";
        }

        $firtArguments   = trim($firtArguments, ',');
        $secordArguments = trim($secordArguments, ',');

        $sql  = "INSERT INTO $tabela ($firtArguments) VALUES ($secordArguments)";
        $stmt = $this->connect->getConnect()->prepare($sql);

        $firtArguments = explode(',', $firtArguments);

        foreach ($firtArguments as $key => $column) {

            $column = array_shift(explode(' = ', $column));
            $stmt->bindValue(":$column", (string)$vars[$column]);
        }

        $stmt->execute();
        return $this->getLastId();
    }

    private function update()
    {
        $id     = $this::_primaryKey;
        $tabela = $this::_tableName;

        $arguments = null;
        $vars      = self::getVars();

        if (!$vars['dt_atualizado']) {
            $vars['dt_atualizado'] = date('Y-m-d H:i:s');
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
            if ($column == 'dt_atualizado') {
                $stmt->bindValue(":$column", date('Y-m-d H:i:s'));
                continue;
            }
            $stmt->bindValue(":$column", $vars[$column]);
        }

        self::executeSQL($stmt);
    }

    private function getVars(): array
    {
        $vars = get_object_vars($this);
        unset($vars['connect']);

        return $vars;
    }

    public function getAll()
    {
        $tabela = $this::_tableName;

        $sql = "SELECT * FROM $tabela";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        $ocorrencias = null;
        while ($ocorrencia = $stmt->fetchObject()) {
            $ocorrencias[] = $ocorrencia;
        }

        return $ocorrencias;
    }

    public function loadAll()
    {
        $tabela = $this::_tableName;

        $sql = "SELECT * FROM $tabela";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        $ocorrencias = null;
        while ($ocorrencia = $stmt->fetchObject()) {
            $ocorrencias[] = $ocorrencia;
        }

        return (object)$ocorrencias;
    }

    // public function load(DbConnect $connect)
    // Esse metodo acho que vai ser removido
    public function load(int $id)
    {
        $id     = $this::_primaryKey;
        $tabela = $this::_tableName;

        $var = get_object_vars($this)[$id];
        $sql = "SELECT * FROM $tabela WHERE $id = :$id";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$id", $var);

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function getAllByKeys(array $filters): array
    {
        $tabela = $this::_tableName;

        // Constrói a cláusula WHERE dinamicamente com base no array de filtros
        $whereClauses = [];
        foreach ($filters as $key => $value) {
            $whereClauses[] = "$key = :$key";
        }

        $whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

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

    public function getByKeys(array $filters)
    {
        $tabela = $this::_tableName;

        // Constrói a cláusula WHERE dinamicamente com base no array de filtros
        $whereClauses = [];
        foreach ($filters as $key => $value) {
            $whereClauses[] = "$key = :$key";
        }

        $whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

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

    public function getAllByKey(string $key, string $value)
    {
        $tabela = $this::_tableName;

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

    public function getByKey(string $key, string $value)
    {
        $tabela = $this::_tableName;

        $sql = "SELECT * FROM $tabela WHERE $key = :$key LIMIT 1;";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$key", $value);

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function getById(int $id)
    {
        $tabela = $this::_tableName;

        $sql = "SELECT * FROM $tabela WHERE id = :id";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(':id', $id);

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function softDelete()
    {
        $id     = $this::_primaryKey;
        $tabela = $this::_tableName;

        $var = get_object_vars($this)[$id];
        $sql = "UPDATE $tabela SET dt_deletado = CURRENT_TIMESTAMP WHERE :$id = $id";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$id", $var);

        self::executeSQL($stmt);
    }

    public function hardDelete()
    {
        $id     = $this::_primaryKey;
        $tabela = $this::_tableName;

        $var = get_object_vars($this)[$id];

        $sql = "DELETE FROM $tabela WHERE :$id = $id;";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$id", $var);

        self::executeSQL($stmt);
    }

    public function hardDeleteAll()
    {
        $tabela = $this::_tableName;

        $sql = "DELETE FROM $tabela;";
        $stmt = $this->connect->getConnect()->prepare($sql);

        $stmt->execute();
    }

    public function hardDeleteBy(string $column, $value): void
    {
        $table = $this::_tableName; // Nome da tabela definido na classe
        $sql = "DELETE FROM $table WHERE $column = :value";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(':value', $value);

        self::executeSQL($stmt);
    }

    public function getLastId()
    {
        // Isso aqui vou ter que dar uma olhada
        $id     = $this::_primaryKey;
        $tabela = $this::_tableName;

        $sql = "SELECT MAX($id) FROM $tabela;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_OBJ)->max;
    }

    public function exists(string $key, string $value): bool
    {
        $tabela = $this::_tableName;

        $sql = "SELECT $key FROM $tabela WHERE $key = :$key LIMIT 1;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$key", $value);

        $stmt->execute();

        // Retorna true se o registro existe, false caso contrário
        return (bool) $stmt->fetchColumn();
    }
}
