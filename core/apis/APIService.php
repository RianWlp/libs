<?php

namespace RianWlp\Libs\core\api;
use stdClass;

class APIService
{
    protected string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    protected function request(string $method, string $endpoint, array $params = []): ?stdClass
    {
        $url = "{$this->baseUrl}/$endpoint";

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ];

        if (!empty($params)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($params);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception("CURL Error: " . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new \Exception("HTTP Error $httpCode: $response");
        }

        return json_decode($response);
    }
}
