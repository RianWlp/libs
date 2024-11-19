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
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'status' => $statusCode,
            'data' => $responseBody
        ];
    }
}
