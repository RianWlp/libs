<?php

namespace RianWlp\Libs\routes;

use RianWlp\Db\DbConnect;
use RianWlp\Libs\log\Log;

// O dispatch é o que executa a requisição (Entidade Request)
class Dispatch
{
    public const DEFAULT_NAMESPACE = 'App\\Controllers\\Api\\';

    public function dispatch(
        array $route,
        array $params = [],
        string $namespace = self::DEFAULT_NAMESPACE,
        bool $isAuthenticated = true
    ) {

        if (is_callable($route['callback'])) {
            return call_user_func_array(
                $route['callback'],
                array_values($params)
            );
        }

        [$controller, $method] = $this->resolveController(
            $route['callback'],
            $namespace
        );

        $requestData = $this->buildRequestData($params);

        try {
            $response = $controller->$method((object)$requestData);
            $status = 200;
        } catch (\Throwable $e) {

            $response = ['error' => $e->getMessage()];
            $status = 500;
        } finally {

            $this->storeLog(
                $route['uri'] ?? 'unknown',
                $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                $requestData,
                $response ?? null,
                (string)$status,
                $_SERVER['REMOTE_ADDR'] ?? null
            );
        }


        echo $data_response;
    }

    private function resolveController(string $callback, string $namespace): array
    {
        if (!str_contains($callback, '@')) {
            throw new \InvalidArgumentException(
                "Formato inválido de callback. Use Controller@method"
            );
        }

        [$controllerName, $method] = explode('@', $callback);

        $controllerClass = $namespace . $controllerName;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller {$controllerClass} não existe");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Método {$method} não existe");
        }

        return [$controller, $method];
    }

    private function buildRequestData(array $params): array
    {
        return array_merge($_REQUEST ?? [], $params);
    }

    private function storeLog(
        string $endpoint,
        string $method,
        ?array $request,
        mixed $response,
        string $status,
        ?string $ip
    ): void {

        $log = new Log(new DbConnect());

        $log->setEndpoint($endpoint);
        $log->setMetodo($method);
        $log->setDadosRequisicao($request);
        $log->setDadosResposta((array) $response);
        $log->setStatusHttp($status);
        $log->setIpOrigem($ip);

        $log->store();
    }
}
