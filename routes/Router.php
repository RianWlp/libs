<?php

namespace RianWlp\Libs\routes;

use RianWlp\Libs\routes\Dispatch;
use RianWlp\Libs\routes\RouterCollection;
use RianWlp\Libs\utils\Token;

// https://alexandrebbarbosa.wordpress.com/2019/04/23/phpconstruir-um-sistema-de-rotas-para-mvc-terceira-parte/
// https://alexandrebbarbosa.wordpress.com/2019/04/19/phpconstruir-um-sistema-de-rotas-para-mvc-segunda-parte/
// RewriteRule ^([A-Za-z0-9_\\\/\-]+)$ index.php?uri=$1 [QSA,L]

class Router
{
    private $routeCollection;
    private $dispacher;

    public function __construct()
    {
        $this->routeCollection = new RouterCollection;
        $this->dispacher       = new Dispatch;
    }

    public function get($pattern, $callback, $namespace, $isAuthenticated)
    {
        $this->routeCollection->add('get', $pattern, $callback, $namespace, $isAuthenticated);
        return $this;
    }

    public function post($pattern, $callback, $namespace, $isAuthenticated)
    {
        $this->routeCollection->add('post', $pattern, $callback, $namespace, $isAuthenticated);
        return $this;
    }

    public function put($pattern, $callback, $namespace, $isAuthenticated)
    {
        $this->routeCollection->add('put', $pattern, $callback, $namespace, $isAuthenticated);
        return $this;
    }

    public function delete($pattern, $callback, $namespace, $isAuthenticated)
    {
        $this->routeCollection->add('delete', $pattern, $callback, $namespace, $isAuthenticated);
        return $this;
    }

    public function patch($pattern, $callback, $namespace, $isAuthenticated)
    {
        $this->routeCollection->add('patch', $pattern, $callback, $namespace, $isAuthenticated);
        return $this;
    }

    public function find($requestType, $pattern)
    {
        return $this->routeCollection->where($requestType, $pattern);
    }

    private function dispach($route, $params, $namespace, $isAuthenticated)
    {
        return $this->dispacher->dispach($route->callback, $params, $namespace, $isAuthenticated);
    }

    private function notFound()
    {
        return header('HTTP/1.0 404 Not Found', true, 404);
    }

    public function resolve($request)
    {
        $route = $this->find($request->method(), $request->uri());

        if ($route) {
            $params = $route->callback['values'] ? $this->getValues($request->uri(), $route->callback['values']) : $request->getData();

            $headers = getallheaders(); // Extrai o token do cabeçalho
            $token   = $headers['Authorization'] ?? null;

            // Autentica a rota se necessário
            error_log("\n-------------------------\n");
            error_log("\n Deve ser autenticado ??\n");
            error_log("\n $route->callback['isAuthenticated'] \n");
            error_log("\n-------------------------\n");
            if ($route->callback['isAuthenticated'] && !$this->validateToken($token)) {
                http_response_code(401);
                error_log("\n-------------------------\n");
                error_log("\n Nao (e) valido");
                error_log("\n-------------------------\n");
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            return $this->dispach($route, $params, $route->callback['namespace'], $route->callback['isAuthenticated']);
        }
        return $this->notFound();
    }

    protected function getValues($pattern, $positions)
    {
        $result  = [];
        $pattern = array_values(array_filter(explode('/', $pattern)));

        foreach ($pattern as $key => $value) {

            if (in_array($key, $positions)) {
                $result[array_search($key, $positions)] = $value;
            }
        }
        return $result;
    }

    public function translate($name, $params)
    {
        $pattern = $this->routeCollection->isThereAnyHow($name);

        if ($pattern) {

            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $server = $_SERVER['SERVER_NAME'] . '/';
            $uri = [];

            foreach (array_filter(explode('/', $_SERVER['REQUEST_URI'])) as $key => $value) {

                if ($value == 'public') {
                    $uri[] = $value;
                    break;
                }
                $uri[] = $value;
            }
            $uri = implode('/', array_filter($uri)) . '/';

            return $protocol . $server . $uri . $this->routeCollection->convert($pattern, $params);
        }
        return false;
    }

    private function validateToken(?string $token): bool
    {
        if (!$token) return false;

        $objToken = new Token();
        return $objToken->validateToken($token);
    }

    public function where($requestType, $pattern) {}
}
