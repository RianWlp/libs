<?php

namespace RianWlp\Libs\routes;

use RianWlp\Db\DbConnect;
use RianWlp\Libs\log\Log;

class Dispatch
{
    public const DEFAULT_NAMESPACE = 'App\\controllers\\api\\';

    public function dispach($callback, $params = [], $namespace = self::DEFAULT_NAMESPACE, $isAuthenticated = true)
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
        $endpoint     = $callback['uri'] ?? 'desconhecido';
        $httpMethod   = $_SERVER['REQUEST_METHOD'] ?? 'http method desconhecido';
        $dataRequest  = array_merge($_REQUEST, $params) ?? [];
        $dataResponse = null;
        $statusCode   = 200;
        $ipOrigem     = $_SERVER['REMOTE_ADDR'] ?? 'ip desconhecido';

        try {
            // Chamar o método no controller
            $dataResponse = call_user_func_array([new $controller, $method], [(object)$dataRequest]);
        } catch (\Exception $e) {
            $dataResponse = ['error' => $e->getMessage()];
            $statusCode   = 500; // Código de erro interno
        }
        // $this->storeLog($endpoint, $httpMethod, $dataRequest, $dataResponse, (string)$statusCode, $ipOrigem);
        return $dataResponse;
    }

    private function storeLog(string $endpoint, string $method, ?array $dataRequest, ?array $dataResponse, string $statusCode, ?string $ipOrigem): void
    {
        $log = new Log(new DbConnect());

        $log->setEndpoint($endpoint);
        $log->setMetodo($method);
        $log->setDadosRequisicao($dataRequest);
        $log->setDadosResposta($dataResponse);
        $log->setStatusHttp($statusCode);
        $log->setIpOrigem($ipOrigem);
        $log->store();
    }
}
