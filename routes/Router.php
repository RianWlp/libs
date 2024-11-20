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

    public function get($pattern, $callback, $namespace)
    {
        $this->routeCollection->add('get', $pattern, $callback, $namespace);
        return $this;
    }

    public function post($pattern, $callback, $namespace)
    {
        $this->routeCollection->add('post', $pattern, $callback, $namespace);
        return $this;
    }

    public function put($pattern, $callback, $namespace)
    {
        $this->routeCollection->add('put', $pattern, $callback, $namespace);
        return $this;
    }

    public function delete($pattern, $callback, $namespace)
    {
        $this->routeCollection->add('delete', $pattern, $callback, $namespace);
        return $this;
    }

    public function patch($pattern, $callback, $namespace)
    {
        $this->routeCollection->add('patch', $pattern, $callback, $namespace);
        return $this;
    }

    public function where($requestType, $pattern) {}

    public function find($requestType, $pattern)
    {
        return $this->routeCollection->where($requestType, $pattern);
    }

    private function dispach($route, $params)
    {
        return $this->dispacher->dispach($route->callback, $params);
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
            return $this->dispach($route, $params, null);
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

    public function secure($requestType, $pattern, $callback, $namespace = null)
    {
        // Insere uma verificação explícita para rotas seguras
        $this->routeCollection->add($requestType, $pattern, function ($params) use ($callback) {

            $token = new Token();
            if (!isset($params['token']) || !($token->isValid($params['token']))) {
                // throw new \Exception('Token inválido ou ausente');
            }
            return call_user_func($callback, $params);
        }, $namespace);
        return $this;
    }
}
