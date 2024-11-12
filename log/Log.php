<?php

// Essa model NAO vai poder ficar aqui pois
// Ver para mover ela para outro lugar
namespace RianWlp\Libs\log;
use RianWlp\Libs\core\Entidade;
use Exception;


class Log extends Entidade
{
    private int $id;
    private int $fk_usuario; // Acho que isso deveria ser uma instancia de Usuario
    // private Usuario $fk_usuario;
    private string $endpoint;
    private string $metodo;
    private ?string $dados_requisicao;
    private ?string $dados_resposta;
    private int $status_http;
    private string $dt_requisicao;
    private ?string $ip_origem;

    public function setFkUsuario(int $fk_usuario): void
    {
        $this->fk_usuario = $fk_usuario;
    }

    public function setEndpoint(string $endpoint): void
    {
        $endpoint = trim($endpoint);
        if (empty($endpoint)) {
            throw new Exception('O endpoint NAO pode ser null');
        }

        $this->endpoint = $endpoint;
    }

    public function setMetodo(string $metodo): void
    {
        $metodo = trim($metodo);
        if (empty($metodo)) {
            throw new Exception('O  metodo NAO pode ser null');
        }

        $this->metodo = $metodo;
    }

    public function setDadosRequisicao(?string $dados_requisicao): void
    {
        $this->dados_requisicao = $dados_requisicao;
    }

    public function setDadosResposta(?string $dados_resposta): void
    {
        $this->dados_resposta = $dados_resposta;
    }

    public function setStatusHttp(int $status_http): void
    {
        $status_http = trim($status_http);
        if (empty($status_http)) {
            throw new Exception('O verbo HTTP NAO pode ser null');
        }

        $this->status_http = $status_http;
    }

    public function setIpOrigem(?string $ip_origem): void
    {
        $this->ip_origem = $ip_origem;
    }
}
