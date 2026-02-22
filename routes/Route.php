<?php

namespace RianWlp\Libs\routes;

final class Route
{
    protected static $router;

    private function __construct() {}

    public static function getRouter()
    {
        if (empty(self::$router)) {
            self::$router = new Router;
        }
        return self::$router;
    }

    public static function post($pattern, $callback, $namespace = null, $isAuthenticated = true)
    {
        return self::getRouter()->post($pattern, $callback, $namespace, $isAuthenticated);
    }

    public static function get($pattern, $callback, $namespace = null, $isAuthenticated = true)
    {
        return self::getRouter()->get($pattern, $callback, $namespace, $isAuthenticated);
    }

    public static function put($pattern, $callback, $namespace = null, $isAuthenticated = true)
    {
        return self::getRouter()->put($pattern, $callback, $namespace, $isAuthenticated);
    }

    public static function delete($pattern, $callback, $namespace = null, $isAuthenticated = true)
    {
        return self::getRouter()->delete($pattern, $callback, $namespace, $isAuthenticated);
    }

    public static function patch($pattern, $callback, $namespace = null, $isAuthenticated = true)
    {
        return self::getRouter()->patch($pattern, $callback, $namespace, $isAuthenticated);
    }

    public static function resolve($pattern)
    {
        // var_dump($pattern);die;
        return self::getRouter()->resolve($pattern);
    }

    public static function translate($pattern, $params)
    {
        return self::getRouter()->translate($pattern, $params);
    }
}
