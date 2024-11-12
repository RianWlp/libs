<?php

namespace Libs\routes;

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

    public static function private () {
        var_dump('merda');die();
    }

    public static function post($pattern, $callback, $namespace = null)
    {
        return self::getRouter()->post($pattern, $callback, $namespace);
    }

    public static function get($pattern, $callback, $namespace = null)
    {
        return self::getRouter()->get($pattern, $callback, $namespace);
    }

    public static function put($pattern, $callback, $namespace = null)
    {
        return self::getRouter()->put($pattern, $callback, $namespace);
    }

    public static function delete($pattern, $callback, $namespace = null)
    {
        return self::getRouter()->delete($pattern, $callback, $namespace);
    }

    public static function patch($pattern, $callback, $namespace = null)
    {
        return self::getRouter()->patch($pattern, $callback, $namespace);
    }

    public static function resolve($pattern)
    {
        // $boolean = self::getRouter()->resolve($pattern);
        // if (!(is_null($boolean))) {
        //     return $boolean;
        // }
        // echo '<pre>';
        // var_dump($pattern);
        // echo '</pre>';
        // die();
        return self::getRouter()->resolve($pattern);
    }

    public static function translate($pattern, $params)
    {
        return self::getRouter()->translate($pattern, $params);
    }
}
