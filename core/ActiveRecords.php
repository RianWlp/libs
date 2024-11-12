<?php

namespace Libs\core;
use Db\DbConnect;
use Exception;

abstract class ActiveRecords
{
    protected $connect;

    public function __construct(DbConnect $connect)
    {
        $this->connect = $connect;
    }

    public function store()
    {
        $id = trim($this::_primaryKey);
        if (!empty($id)) {

            return get_object_vars($this)[$id] ? $this->update($this->connect) : $this->insert($this->connect);
        }
    }

    private function insert($connect)
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
        $stmt = $connect->getConnect()->prepare($sql);

        $firtArguments = explode(',', $firtArguments);

        foreach ($firtArguments as $key => $column) {

            $column = array_shift(explode(' = ', $column));
            $stmt->bindValue(":$column", (string)$vars[$column]);
        }

        $stmt->execute();
        return $this->getLastId($connect);
    }

    private function update($connect)
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

        $stmt = $connect->getConnect()->prepare($sql);

        $arguments = explode(',', $arguments);
        foreach ($arguments as $key => $column) {

            $column = array_shift(explode(' = ', $column));
            if ($column == 'dt_atualizado') {
                $stmt->bindValue(":$column", date('Y-m-d H:i:s'));
                continue;
            }
            $stmt->bindValue(":$column", $vars[$column]);
        }

        // try {
        //     $stmt->execute();
        // } catch (\PDOException $e) {
        //     var_dump($e->getMessage());
        // }
        // Da um 500 porem da certo, verificar para arrumar esse problema
        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar o registro com $id $var da tabela $tabela");
        }

        echo "Registro com $id $var atualizado com sucesso.";
    }

    private function getVars(): array
    {
        $vars = get_object_vars($this);
        unset($vars['connect']);

        return $vars;
    }

    public function loadAll(DbConnect $connect)
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

    // public function load(DbConnect $connect)
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

        if (!$stmt->execute()) {
            throw new Exception("Erro ao inativar o registro com $id $var da tabela $tabela.");
        }

        // Opcional: Retorna uma mensagem de sucesso se desejado
        return "Registro com $id $var inativado com sucesso.";
    }

    public function hardDelete()
    {
        $id     = $this::_primaryKey;
        $tabela = $this::_tableName;

        $var = get_object_vars($this)[$id];

        $sql = "DELETE FROM $tabela WHERE :$id = $id;";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$id", $var);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao deletar o registro com $id $var da tabela $tabela.");
        }

        // Opcional: Retorna uma mensagem de sucesso se desejado
        return "Registro com $id $var deletado com sucesso.";
    }

    public function hardDeleteAll()
    {
        $tabela = $this::_tableName;

        $sql = "DELETE FROM $tabela;";
        $stmt = $this->connect->getConnect()->prepare($sql);

        $stmt->execute();
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
}
