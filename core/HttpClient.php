<?php

namespace RianWlp\Libs\core;

use RianWlp\Db\DbConnect;
use RianWlp\Libs\log\Log;

class HttpClient
{
    private string $baseUrl;
    private array $authHeaders;

    public function __construct(string $baseUrl, array $auth)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->authHeaders = [
            // 'Authorization' => $auth['type'] . ' ' . $auth['token']
            'Authorization' => $auth['type'] . ' ' . $auth['token']
            // Authorization: Bearer <seu_token_aqui>
        ];
    }

    public function get(string $endpoint, $data = null, array $headers = []): array
    {
        return $this->request('GET', $endpoint, $data, $headers);
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

    private function request(string $method, string $endpoint, $dataRequest = null, array $headers = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Configuração de cabeçalhos padrão
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0IiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdCIsImlhdCI6MTczMjk5MDg3NiwiZGF0YSI6eyJpZCI6MTEsInVzZXJuYW1lIjoiUmlhbldscCIsImVtYWlsIjoicmlhbndscEBnbWFpbC5jb20ifX0.R-p1q-wJ09jI4EHJV48Zvrw8g9FAHrFVEAaknxes-Rc';
        $defaultHeaders = [
            'Content-Type: application/json',
            "Authorization: Bearer $token"
        ];
        // Combina os cabeçalhos padrão com os fornecidos
        $finalHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $finalHeaders);

        // Adiciona os dados ao corpo da requisição, se fornecidos
        if (!empty($dataRequest)) {
            $encodedData = json_encode($dataRequest);
            if ($encodedData === false) {
                throw new \Exception("Erro ao codificar os dados da requisição: " . json_last_error_msg());
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedData);
        }

        // Executa a requisição e coleta a resposta
        $responseBody = curl_exec($curl);

        // Verificação de erros no CURL
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception("Erro ao executar a requisição CURL: $error");
        }

        // Obtém o código de status da resposta
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Verifica se a resposta é JSON válida
        $dataResponse = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Erro ao decodificar a resposta JSON: " . json_last_error_msg());
        }

        // Verifica se a variável $_SERVER está disponível (ambiente web)
        // $ipOrigem = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // self::storeLog($endpoint, $method, $dataRequest, $dataResponse, $statusCode, $ipOrigem);

        return [
            'status' => $statusCode,
            'body'   => $dataResponse
        ];
    }

    // private function request(string $method, string $endpoint, $dataRequest = null, array $headers = []): array
    // {
    //     // sudo apt-get install php8.3-curl
    //     $url = $this->baseUrl . $endpoint;

    //     $curl = curl_init($url);
    //     curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //     // Define os cabeçalhos para a requisição, incluindo o cabeçalho de autenticação
    //     // $defaultHeaders = array_merge(['Content-Type: application/json'], $this->authHeaders, $headers);
    //     // curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders);

    //     // Vai ficar hardcode mesmo
    //     $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0IiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdCIsImlhdCI6MTczMjk5MDg3NiwiZGF0YSI6eyJpZCI6MTEsInVzZXJuYW1lIjoiUmlhbldscCIsImVtYWlsIjoicmlhbndscEBnbWFpbC5jb20ifX0.R-p1q-wJ09jI4EHJV48Zvrw8g9FAHrFVEAaknxes-Rc';
    //     $defaultHeaders = [
    //         'Content-Type: application/json',
    //         "Authorization: $token"
    //     ];
    //     curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders);

    //     // Se houver dados a serem enviados (por exemplo, em POST ou PUT), adiciona os dados no corpo da requisição
    //     if ($dataRequest) {
    //         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataRequest));
    //     }

    //     // Executa a requisição e coleta a resposta
    //     $responseBody = curl_exec($curl);

    //     // Verificação de erro do CURL
    //     if (curl_errno($curl)) {
    //         $error = curl_error($curl);
    //         curl_close($curl);
    //         throw new \Exception("Erro ao executar a requisição CURL: $error");
    //     }

    //     // Obtém o código de status da resposta
    //     $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //     curl_close($curl);

    //     // Verifica se a resposta é um JSON válido
    //     $dataResponse = json_decode($responseBody, true);
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         throw new \Exception("Erro ao decodificar a resposta JSON.");
    //     }

    //     // $ipOrigem = $_SERVER;
    //     $ipOrigem = $_SERVER['REMOTE_ADDR'];

    //     self::storeLog($endpoint, $method, $dataRequest, $dataResponse, $statusCode, $ipOrigem);
    //     return [
    //         'status' => $statusCode,
    //         'body'   => $dataResponse
    //     ];
    // }

    // private function storeLog(string $endpoint, string $method, ?array $dataRequest, ?array $dataResponse, string $statusCode, ?string $ipOrigem): void
    // {
    //     $log = new Log(new DbConnect());

    //     // $log->setFkUsuario($fk_usuario);
    //     $log->setEndpoint($endpoint);
    //     $log->setMetodo($method);
    //     $log->setDadosRequisicao($dataRequest);
    //     $log->setDadosResposta($dataResponse);
    //     $log->setStatusHttp($statusCode);
    //     $log->setIpOrigem($ipOrigem);

    //     // Depois, salvamos o log no banco ou fazemos o tratamento necessário.
    //     $log->store();
    // }
}
