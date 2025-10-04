<?php

namespace App\models;

use RianWlp\Libs\core\Entidade;

use DateTime;
use Exception;

class Usuario extends Entidade
{
    // Melhorar isso aqui, ta bem ruim
    const _primaryKey = 'id';
    const _tableName  = 'usuarios';

    protected string $id;
    protected string $nome;
    protected string $username;
    protected string $email;
    protected string $senha;
    protected ?string $cpf;
    protected ?string $dt_nascimento;
    protected ?string $token;

    // Se esse construtor ficar descomentado o construct do ActiveRecords nao vai receber a instancia do DbConnect
    // public function __construct() {}

    /**
     * @param string $nome
     * @throws Exception
     */
    public function setNome(string $nome = null): void
    {
        $nome = trim($nome);
        if ((isset($this->nome)) && (empty($nome))) {
            return;
        }

        if (empty($nome)) {
            throw new Exception('O nome é obrigatório');
        }
        $this->nome = $nome;
    }

    /**
     * @param string $username
     * @throws Exception
     */
    public function setUsername(string $username = null): void
    {
        $username = trim($username);
        if ((isset($this->username)) && (empty($username))) {
            return;
        }

        if (empty($username)) {
            throw new Exception('O username é obrigatório');
        }
        $this->username = $username;
    }

    /**
     * @param string $email
     * @throws Exception
     */
    public function setEmail(string $email = null): void
    {
        $email = trim($email);
        if ((isset($this->email)) && (empty($email))) {
            return;
        }

        if (empty($email)) {
            throw new Exception('O email é obrigatório.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('O email nao é válido.');
        }
        $this->email = $email;
    }

    /**
     * @param string $senha
     * @throws Exception
     */
    public function setSenha(string $senha = null): void
    {
        $senha = trim($senha);
        if ((isset($this->senha)) && (empty($senha))) {
            return;
        }

        if (empty($senha) || strlen($senha) < 8) {
            throw new Exception('A senha é obrigatória e deve ter no mínimo 8 caracteres.');
        }

        // Faz o encrypt da senha
        $this->senha = hash('sha256', $senha);
    }

    /**
     * @param string $cpf|null
     * @throws Exception
     */
    public function setCPF(string $cpf = null): void
    {
        $cpf = trim($cpf);
        if ((isset($this->cpf)) && (empty($cpf))) {
            return;
        }

        // if ((!empty($cpf)) && ($this->isValidCpf($cpf))) {
        //     throw new Exception('O CPF é inválido.');
        // }
        $this->cpf = 'A';
        // $this->cpf = $cpf;
    }

    /**
     * @param string|null $dt_nascimento
     * @throws Exception
     */
    public function setDataNascimento(string $dt_nascimento = null): void
    {
        $dt_nascimento = trim($dt_nascimento);
        if ((isset($this->dt_nascimento)) && (empty($dt_nascimento))) {
            return;
        }

        // Nao (e) obrigatorio, validar depois com mais calma
        if ($dt_nascimento && $dt_nascimento > new DateTime()) {
            throw new Exception('A data de nascimento não pode ser uma data futura.');
        }
        $this->dt_nascimento = $dt_nascimento;
    }

    /**
     * @param string| $token
     * @throws Exception
     */
    public function setToken(string $token = null)
    {
        $token = trim($token);
        if ((isset($this->token)) && (empty($token))) {
            return;
        }

        if (empty($token)) {
            throw new Exception('O token é obrigatório');
        }
        $this->token = $token;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getCPF(): string
    {
        return $this->cpf;
    }

    public function getDataNascimento(): string
    {
        return $this->dt_nascimento;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function get(int $id): Usuario
    {
        $objUsuario = $this->getById((int)$id);

        // Verifica se os dados do usuário foram encontrados
        if ($objUsuario === null) {
            throw new Exception("Usuário com ID $id não encontrado.");
        }

        // Cria uma nova instância de Usuario e preenche suas propriedades
        $usuario = new Usuario($this->connect);

        $usuario->id             = $objUsuario->id;
        $usuario->nome           = $objUsuario->nome;
        $usuario->username       = $objUsuario->username;
        $usuario->email          = $objUsuario->email;
        $usuario->senha          = $objUsuario->senha;
        $usuario->cpf            = $objUsuario->cpf;
        $usuario->token          = $objUsuario->token;
        $usuario->dt_nascimento  = $objUsuario->dt_nascimento;
        // $usuario->dt_atualizacao = $objUsuario->dt_atualizacao;

        return $usuario;
    }

    // Nao sei se esse (e) o lugar correto de deixar esse metodo, talvez deveria ir em uma controller
    public function getByUsername(string $username): ?Usuario
    {
        $objUsuario = $this->getByKey('username', $username);

        // Verifica se os dados do usuário foram encontrados
        if ($objUsuario === null) {
            throw new Exception("Usuário com username $username não encontrado.");
        }

        // Cria uma nova instância de Usuario e preenche suas propriedades
        $usuario = new Usuario($this->connect);
        $usuario->id            = $objUsuario->id;
        $usuario->nome          = $objUsuario->nome;
        $usuario->username      = $objUsuario->username;
        $usuario->email         = $objUsuario->email;
        $usuario->senha         = $objUsuario->senha;
        $usuario->cpf           = $objUsuario->cpf;
        $usuario->token         = $objUsuario->token;
        $usuario->dt_nascimento = $objUsuario->dt_nascimento;

        return $usuario;
    }

    /**
     * Verifica se o CPF é válido
     * @param string $cpf
     * @return bool
     */
    private function isValidCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
