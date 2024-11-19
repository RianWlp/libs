<?php

namespace RianWlp\Libs\core;

class HttpClient
{
    private string $baseUrl;
    private array $authHeaders;

    public function __construct(string $baseUrl, array $auth)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->authHeaders = [
            'Authorization' => $auth['type'] . ' ' . $auth['token']
        ];
    }

    public function get(string $endpoint, array $headers = []): array
    {
        return $this->request('GET', $endpoint, null, $headers);
    }

    public function post(string $endpoint, $data, array $headers = []): array
    {
        return $this->request('POST', $endpoint, $data, $headers);
    }

    public function put(string $endpoint, $data, array $headers = []): array
    {
        return $this->request('PUT', $endpoint, $data, $headers);
    }

    public function delete(string $endpoint, array $headers = []): array
    {
        return $this->request('DELETE', $endpoint, null, $headers);
    }

    private function request(string $method, string $endpoint, $data = null, array $headers = []): array
    {
        // sudo apt-get install php8.3-curl
        $url = $this->baseUrl . $endpoint;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Define os cabeçalhos para a requisição, incluindo o cabeçalho de autenticação
        $defaultHeaders = array_merge(['Content-Type: application/json'], $this->authHeaders, $headers);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders);

        // Se houver dados a serem enviados (por exemplo, em POST ou PUT), adiciona os dados no corpo da requisição
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Executa a requisição e coleta a resposta
        $responseBody = curl_exec($curl);

        // Verificação de erro do CURL
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception("Erro ao executar a requisição CURL: $error");
        }

        // Obtém o código de status da resposta
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Verifica se a resposta é um JSON válido
        $data = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Erro ao decodificar a resposta JSON.");
        }

        return [
            'status' => $statusCode,
            'body'   => $data
        ];
    }
}
