<?php

namespace RianWlp\Libs\log;

use RianWlp\Libs\core\Entidade;

class Log extends Entidade
{
    // const PRIMARY_KEY = 'id';
    // const TABLE_NAME  = 'logs';

    protected int     $id;
    protected ?int    $fk_usuario; // Acho que isso deveria ser uma instancia de Usuario
    protected string  $endpoint;
    protected string  $metodo;
    protected ?string $dados_requisicao;
    protected ?string $dados_resposta;
    protected int     $status_http;
    protected string  $dt_requisicao;
    protected ?string $ip_origem;

    public function setFkUsuario(int $fk_usuario): void
    {
        $this->fk_usuario = $fk_usuario;
    }

    public function setEndpoint(string $endpoint): void
    {
        $endpoint = trim($endpoint);
        if (empty($endpoint)) {
            throw new \Exception('O endpoint NAO pode ser null');
        }

        $this->endpoint = $endpoint;
    }

    public function setMetodo(string $metodo): void
    {
        $metodo = trim($metodo);
        if (empty($metodo)) {
            throw new \Exception('O  metodo NAO pode ser null');
        }

        $this->metodo = $metodo;
    }

    public function setDadosRequisicao(?array $dados_requisicao): void
    {
        if (!empty($dados_requisicao)) {
            $this->dados_requisicao = json_encode($dados_requisicao);
        }
    }

    public function setDadosResposta(?array $dados_resposta): void
    {
        if (!empty($dados_resposta)) {
            $this->dados_resposta = json_encode($dados_resposta);
        }
    }

    public function setStatusHttp(int $status_http): void
    {
        $status_http = trim($status_http);
        if (empty($status_http)) {
            throw new \Exception('O verbo HTTP NAO pode ser null');
        }

        $this->status_http = $status_http;
    }

    public function setIpOrigem(?string $ip_origem): void
    {
        $this->ip_origem = $ip_origem;
    }
}
