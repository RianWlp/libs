<?php

namespace RianWlp\Libs\routes;

use RianWlp\Db\DbConnect;
use RianWlp\Libs\log\Log;

class Dispatch
{
    // Ta feio
    public const DEFAULT_NAMESPACE = 'App\\controllers\\api\\';

    public function dispach($callback, $params = [], $namespace = self::DEFAULT_NAMESPACE, $is_authenticated = true)
    {
        if (is_callable($callback['callback'])) {
            return call_user_func_array($callback['callback'], array_values($params));
        }

        if (!is_string($callback['callback']) && !str_contains($callback['callback'], '@')) {
            throw new \Exception('Erro ao despachar: método não implementado');
        }

        $callback['callback'] = explode('@', $callback['callback']);
        $controller           = $namespace . $callback['callback'][0];
        $method               = $callback['callback'][1];

        $rc = new \ReflectionClass($controller);
        if (!$rc->isInstantiable() || !$rc->hasMethod($method)) {
            throw new \Exception('Erro ao despachar: controller não pode ser instanciado, ou método não existe');
        }

        // Inicializando as variáveis para log
        $endpoint      = $callback['uri'] ?? 'desconhecido';
        $http_method   = $_SERVER['REQUEST_METHOD'] ?? 'http method desconhecido';
        $data_request  = array_merge($_REQUEST, $params) ?? [];
        $data_response = null;
        $status        = 200;
        $ip_origem     = $_SERVER['REMOTE_ADDR'] ?? 'ip desconhecido';

        try {
            // Chamar o método no controller
            $data_response = call_user_func_array([new $controller, $method], [(object)$data_request]);
        } catch (\Exception $e) {
            $data_response = ['error' => $e->getMessage()];
        }

        // $this->storeLog($endpoint, $httpMethod, $dataRequest, $data_response, (string)$statusCode, $ipOrigem);
        echo $data_response;
    }

    private function storeLog(string $endpoint, string $method, ?array $data_request, ?array $data_response, string $status, ?string $ip_origem): void
    {
        $log = new Log(new DbConnect());

        $log->setEndpoint($endpoint);
        $log->setMetodo($method);
        $log->setDadosRequisicao($data_request);
        $log->setDadosResposta($data_response);
        $log->setStatusHttp($status);
        $log->setIpOrigem($ip_origem);
        $log->store();
    }
}
