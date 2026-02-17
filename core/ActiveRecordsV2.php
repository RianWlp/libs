<?php

namespace RianWlp\Libs\core;

use RianWlp\Libs\utils\DotEnv;
use Exception;
use stdClass;

class ActiveRecordsV2
{
    protected string $table_name;
    protected string $primary_key;

    protected $connect;

    /**
     * Construtor da classe ActiveRecordsV2.
     *
     * @param mixed          $connect    Instância de conexão com o banco de dados.
     * @param stdClass|null  $attributes Objeto contendo os atributos para inicializar a instância.
     */
    public function __construct($connect, ?stdClass $attributes = null)
    {
        $this->connect = $connect;

        if ($attributes !== null) {
            foreach ($attributes as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Salva o registro no banco de dados, realizando inserção ou atualização conforme necessário.
     *
     * @return bool Retorna true se a operação foi bem-sucedida, caso contrário, false.
     */
    public function store(): bool
    {
        $primary_key = trim($this->primary_key);

        $id = $this->$primary_key ?? null;
        return $id ? $this->update() : $this->insert();
    }

    private function insert(): bool
    {
        $vars = self::getVars();
        if (empty($vars)) {
            return false;
        }

        $columns      = array_keys($vars);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $columns_list      = implode(',', $columns);
        $placeholders_list = implode(',', $placeholders);

        $sql  = "INSERT INTO {$this->table_name} ($columns_list) VALUES ($placeholders_list)";
        $stmt = $this->connect->getConnect()->prepare($sql);

        foreach ($columns as $column) {
            $stmt->bindValue(":$column", $vars[$column]);
        }

        return $stmt->execute();
    }

    private function update(): bool
    {
        $id_field = $this->primary_key;
        $table    = $this->table_name;
        $vars     = self::getVars();

        // Acho que isso nao faz muito sentido
        if (empty($vars[$id_field])) {
            return false;
        }

        $updated_at = DotEnv::get('UPDATED_AT_FIELD');

        // Remove ID da lista de atualização
        $id_value = $vars[$id_field];
        unset($vars[$id_field]);

        // Gera pares key = :key
        $set_clauses = [];
        foreach ($vars as $key => $value) {
            $set_clauses[] = "$key = :$key";
        }

        if (empty($set_clauses)) {
            return false;
        }

        $set_sql = implode(', ', $set_clauses);

        $sql = "UPDATE {$table} SET $updated_at = CURRENT_TIMESTAMP, {$set_sql} WHERE {$id_field} = :{$id_field};";
        $stmt = $this->connect->getConnect()->prepare($sql);

        $stmt->bindValue(":{$id_field}", $id_value);
        foreach ($vars as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * Retorna um array de objetos contendo todos os registros da tabela
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        $sql = "SELECT * FROM {$this->table_name};";
        $stmt = $this->connect->getConnect()->query($sql);

        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        return array_map(function ($row) {
            return $this->hydrate($row);
        }, $rows);
    }

    /**
     * Retorna um array de objetos contendo os registros paginados
     *
     * @param integer $page  Página atual
     * @param integer $limit Número de registros por página
     *
     * @return array|null
     */
    public function getAllPaginated(int $page = 1, int $limit = 10): array
    {
        $page  = max(1, $page);
        $limit = max(1, $limit);

        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM {$this->table_name} LIMIT :limit OFFSET :offset";
        $stmt = $this->connect->getConnect()->prepare($sql);

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        return array_map(function ($row) {
            return $this->hydrate($row);
        }, $rows);
    }

    /**
     * Undocumented function
     *
     * Exemplos de utilização:
     * [
     *     'ano' => ['operator' => '=', 'value' => 2020]
     * ]
     *
     * [
     *     'nome' => [
     *         'operator' => 'ILIKE',
     *         'value'    => '%civic%'
     *     ]
     * ]
     *
     * [
     *     'id' => [
     *         'operator' => 'IN',
     *         'value'    => [1, 2, 3]
     *     ]
     * ]
     *
     * // TODO: montar uma função própria minha no futuro
     * @param array $filters
     * @return array
     */
    public function getBy(stdClass $filters): array
    {
        $table = $this->table_name;

        if (empty($filters)) {
            return [];
        }

        $allowed_operators = [
            '=',
            '!=',
            '>',
            '<',
            '>=',
            '<=',
            'LIKE',
            'ILIKE',
            'IN'
        ];

        $conditions = [];
        $params     = [];

        foreach ($filters as $column => $config) {

            $operator = strtoupper($config['operator'] ?? '=');
            $value    = $config->value ?? null;

            if (!in_array($operator, $allowed_operators)) {
                throw new \InvalidArgumentException("Operador inválido: {$operator}");
            }

            // IN precisa ser tratado diferente
            if ($operator === 'IN' && is_array($value)) {

                $in_params = [];

                foreach ($value as $index => $val) {
                    $param = ":{$column}_{$index}";
                    $in_params[] = $param;
                    $params[$param] = $val;
                }

                $conditions[] = "{$column} IN (" . implode(',', $in_params) . ")";
                continue;
            }

            $param = ':' . $column;
            $conditions[] = "{$column} {$operator} {$param}";
            var_dump($conditions);
            die;
            $params[$param] = $value;
        }

        $where = implode(' AND ', $conditions);
        $sql   = "SELECT * FROM {$table} WHERE {$where};";

        $stmt = $this->connect->getConnect()->prepare($sql);

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        return array_map([$this, 'hydrate'], $rows);
    }

    /**
     * Retorna um array de objetos contendo os registros filtrados pela chave valor informada
     *
     * @param string $key   Coluna que vai ser filtrada
     * @param string $value Valor que vai ser filtrado
     *
     * @return array|null
     */
    public function getByKey(string $key, string $value): ?array
    {
        $table = $this->table_name;

        $sql = "SELECT * FROM $table WHERE $key = :$key;";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$key", $value);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

        return array_map(fn($row) => $this->hydrate($row), $rows) ?: null;
    }

    /**
     * Retorna um objeto contendo o registro filtrado pelo ID informado
     *
     * @param int $id Id do registro
     *
     * @return static|null Retorna o objeto do registro ou null se não encontrado.
     *
     * @throws Exception Se nao for encontrado name retorna que o registro nao foi encontrato 404
     */
    public function getById(int $id): ?static
    {
        $sql = "SELECT * FROM {$this->table_name} WHERE id = :id";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_OBJ);

        return ($row != false) ? $this->hydrate($row) : null;
    }

    /**
     * Realiza a exclusão lógica (soft delete) de um registro com base em uma coluna e valor específicos.
     *
     * @param string $column Nome da coluna para filtrar o registro.
     * @param object $object Objeto que tera a informacao capturada para realizar a operacao.
     *
     * @return bool Retorna true se a operação foi bem-sucedida, caso contrário, false.
     *
     * @throws Exception Se o campo deleted_at não estiver configurado no arquivo .env.
     */
    public function softDeleteBy(string $column, object $object): bool
    {
        $table = $this->table_name; // Nome da tabela definido na classe

        $deleted_at = DotEnv::get('DELETED_AT_FIELD');

        // Assim previne SQL injection
        $sql = "UPDATE {$table}
                    SET {$deleted_at} = CURRENT_TIMESTAMP
                WHERE {$column} = :value";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(':value', $object->$column);

        return $stmt->execute();
    }

    /**
     * Realiza a exclusão permanente (hard delete) de um registro
     *
     * @return bool Retorna true se a operação foi bem-sucedida, caso contrário, false.
     */
    public function hardDeleteAll(): bool
    {
        $table = $this->table_name;

        $sql = "DELETE FROM $table;";
        $stmt = $this->connect->getConnect()->prepare($sql);

        return $stmt->execute();
    }

    /**
     * Realiza a exclusão permanente (hard delete) de um registro com base em uma coluna e valor específicos.
     *
     * @param string $column Nome da coluna para filtrar o registro.
     * @param object $object Objeto que tera a informacao capturada para realizar a operacao.
     *
     * @return bool Retorna true se a operação foi bem-sucedida, caso contrário, false.
     */
    public function hardDeleteBy(string $column, object $object): bool
    {
        $table = $this->table_name;
        $sql = "DELETE FROM $table WHERE $column = :$column;";

        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$column", $object->$column);

        return $stmt->execute();
    }

    /**
     * Retorna o ID do último registro inserido na tabela
     *
     * @return int Retorna um inteiro representando o ID do último registro.
     */
    public function getLastId(): int
    {
        // Isso aqui vou ter que dar uma olhada
        $id    = $this->primary_key;
        $table = $this->table_name;

        $sql = "SELECT MAX($id) FROM $table;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_OBJ)->max;
    }

    /**
     * Retorna o último registro inserido na tabela
     *
     * @return object|null Retorna o objeto do último registro ou null se não houver registros.
     */
    public function getLastRow(): ?static
    {
        $id    = $this->primary_key;
        $table = $this->table_name;

        $sql = "SELECT * FROM $table ORDER BY $id DESC LIMIT 1;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->execute();

        return $this->hydrate($stmt->fetch(\PDO::FETCH_OBJ));
    }

    /**
     * Verifica se um registro existe com base em uma chave e valor específicos.
     *
     * @param string $key   Nome da coluna para filtrar o registro.
     * @param string $value Valor da coluna para identificar o registro.
     *
     * @return object|null Retorna o objeto do registro se encontrado, caso contrário, null.
     */
    public function exists(string $key, string $value)
    {
        $table = $this->table_name;

        $sql = "SELECT $key FROM $table WHERE $key = :$key;";
        $stmt = $this->connect->getConnect()->prepare($sql);
        $stmt->bindValue(":$key", $value);

        $stmt->execute();

        return $this->hydrate($stmt->fetch(\PDO::FETCH_OBJ));
    }

    // ----------------------------- Metodos internos da classe

    /**
     * Busca e retorna as variáveis de instância do objeto atual, excluindo propriedades específicas.
     *
     * @return array
     */
    protected function getVars(): array
    {
        $vars = get_object_vars($this);
        unset($vars['connect']);
        unset($vars['table_name']);
        unset($vars['primary_key']);

        $vars = array_filter($vars, function ($var) {
            return !empty($var);
        });

        return $vars;
    }

    /**
     * Hidrata um objeto genérico em uma instância da classe atual.
     *
     * @param object|null $object Objeto genérico a ser hidratado.
     *
     * @return object|null Instância da classe atual ou null se o objeto for nulo.
     */
    private function hydrate(object|array|null $object): ?static
    {
        // Se vier array, converte para objeto genérico
        if (is_array($object)) {
            $object = (object) $object;
        }

        if (!$object) {
            return null;
        }

        $class = get_called_class();
        return new $class($this->connect, $object);
    }
}
