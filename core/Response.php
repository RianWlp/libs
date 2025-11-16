<?php

namespace RianWlp\Libs\core;

// https://www.php.net/manual/en/function.http-response-code.php
class Response
{
    // Define o cabeçalho Content-Type como JSON
    public static function setJsonContentType()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
    }

    // Define o status HTTP
    public static function setHttpStatus(int $statusCode)
    {
        http_response_code($statusCode);
    }

    public static function sendJsonResponse($data, int $statusCode = 200, string $message = ''): void
    {
        // Defina os cabeçalhos apenas uma vez
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');
        http_response_code($statusCode);

        // Monta a resposta JSON
        $response = [
            'status'  => $statusCode, // Define o código do status HTTP
            'message' => $message,
            'data'    => $data,
        ];

        // Envia a resposta
        echo json_encode($response);
        exit;
    }

    public static function sendJsonResponse2($data, int $statusCode = 200, string $message = ''): void
    {
        // Defina os cabeçalhos apenas uma vez
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Allow-Credentials: true');
            header('Content-Type: application/json');
        }

        // Tratamento para requisições OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        // Define o código do status HTTP
        http_response_code($statusCode);

        // Monta a resposta JSON
        $response = [
            'status'  => $statusCode,
            'message' => $message,
            'data'    => $data,
        ];

        // Envia a resposta
        echo json_encode($response);
        exit;
    }

    public static function sendJsonResponse3($data = null, int $statusCode = 200, string $message = '')
    {
        // nao sei se nao vai dar pau nisso
        // CORS — versão correta para evitar conflitos
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json; charset=utf-8');

        http_response_code($statusCode);

        $success = $statusCode >= 200 && $statusCode < 300;

        $response = [
            'success' => $success,
        ];

        if ($message !== '') {
            $response['message'] = $message;
        }

        if ($success) {
            $response['data'] = $data;
        } else {
            $response['error'] = $data; // detalhes da falha
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
