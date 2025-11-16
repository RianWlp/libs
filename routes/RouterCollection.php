<?php

namespace RianWlp\Libs\routes;

class RouterCollection
{
    protected $routes_post   = [];
    protected $routes_get    = [];
    protected $routes_put    = [];
    protected $routes_delete = [];
    protected $routes_patch  = [];
    protected $route_names   = [];

    public function add($request_type, $pattern, $callback, $namespace, $isAuthenticated)
    {
        switch ($request_type) {

            case 'post':
                return $this->addPost($pattern, $callback, $namespace, $isAuthenticated);
                break;
            case 'get':
                return $this->addGet($pattern, $callback, $namespace, $isAuthenticated);
                break;
            case 'put':
                return $this->addPut($pattern, $callback, $namespace, $isAuthenticated);
                break;
            case 'patch':
                return $this->addPatch($pattern, $callback, $namespace, $isAuthenticated);
                break;
            case 'delete':
                return $this->addDelete($pattern, $callback, $namespace, $isAuthenticated);
                break;
            default:
                throw new \Exception('Tipo de requisição não implementado');
        }
    }

    protected function strposarray(string $haystack, array $needles, int $offset = 0)
    {
        $result = false;
        if (strlen($haystack) > 0 && count($needles) > 0) {

            foreach ($needles as $element) {
                $result = strpos($haystack, $element, $offset);
                if ($result !== false) {
                    break;
                }
            }
        }
        return $result;
    }

    protected function parsePattern(array $pattern)
    {
        // Define the pattern
        $result['set'] = $pattern['set'] ?? null;
        // Allows route name settings
        $result['as'] = $pattern['as'] ?? null;
        // Allows new namespace definition for Controllers
        // $result['namespace'] = $pattern['namespace'] ?? null;
        return $result;
    }

    protected function toMap($pattern)
    {
        $result = [];

        $needles = ['{', '[', '(', "\\"];

        $pattern = array_filter(explode('/', $pattern));

        foreach ($pattern as $key => $element) {

            $found = $this->strposarray($element, $needles);

            if ($found !== false) {

                if (substr($element, 0, 1) === '{') {

                    $result[preg_filter('/([\{\}])/', '', $element)] = $key - 1;
                } else {
                    $index = 'value_' . !empty($result) ? count($result) + 1 : 1;
                    array_merge($result, [$index => $key - 1]);
                }
            }
        }
        return count($result) > 0 ? $result : false;
    }

    public function where($request_type, $pattern)
    {

        switch ($request_type) {
            case 'post':
                return $this->findPost($pattern);
                break;
            case 'get':
                return $this->findGet($pattern);
                break;
            case 'put':
                return $this->findPut($pattern);
                break;
            case 'patch':
                return $this->findPatch($pattern);
                break;
            case 'delete':
                return $this->findDelete($pattern);
                break;
                defautl:
                throw new \Exception('Tipo de requisição não implementado');
        }
    }

    protected function findPost($pattern_sent)
    {
        $pattern_sent = $this->parseUri($pattern_sent);

        foreach ($this->routes_post as $pattern => $callback) {

            if (preg_match($pattern, $pattern_sent, $pieces)) {

                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;
    }

    protected function findGet($pattern_sent)
    {
        $pattern_sent = $this->parseUri($pattern_sent);
        foreach ($this->routes_get as $pattern => $callback) {

            if (preg_match($pattern, $pattern_sent, $pieces)) {

                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;
    }


    protected function findPut($pattern_sent)
    {
        $pattern_sent = $this->parseUri($pattern_sent);

        foreach ($this->routes_put as $pattern => $callback) {

            if (preg_match($pattern, $pattern_sent, $pieces)) {

                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;
    }

    protected function findPatch($pattern_sent)
    {
        $pattern_sent = $this->parseUri($pattern_sent);

        foreach ($this->routes_patch as $pattern => $callback) {

            if (preg_match($pattern, $pattern_sent, $pieces)) {

                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;
    }

    protected function findDelete($pattern_sent)
    {
        $pattern_sent = $this->parseUri($pattern_sent);

        foreach ($this->routes_delete as $pattern => $callback) {

            if (preg_match($pattern, $pattern_sent, $pieces)) {

                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;
    }

    protected function parseUri($uri)
    {
        return implode('/', array_filter(explode('/', $uri)));
    }

    protected function definePattern(string $pattern): string
    {
        // Remove barras duplicadas
        $pattern = preg_replace('#/{2,}#', '/', $pattern);
        $pattern = trim($pattern, '/');

        // Quebra path e query string
        $parts = explode('?', $pattern, 2);
        $path = $parts[0];
        $query = $parts[1] ?? '';

        // ---------------------------
        // 1. Trata parâmetros do PATH
        // ---------------------------

        // {param}
        $path = preg_replace(
            '/\{([a-zA-Z0-9_-]+)\}/',
            '(?P<$1>[A-Za-z0-9\-_]+)',
            $path
        );

        // {param:regex}
        $path = preg_replace(
            '/\{([a-zA-Z0-9_-]+):([^}]+)\}/',
            '(?P<$1>$2)',
            $path
        );

        // {param?} (opcional)
        $path = preg_replace(
            '/\{([a-zA-Z0-9_-]+)\?\}/',
            '(?P<$1>[A-Za-z0-9\-_]+)?',
            $path
        );

        // ---------------------------
        // 2. Trata parâmetros da QUERY
        // ---------------------------
        if ($query) {

            // Ex: id={id}&tipo={tipo}
            $query = preg_replace(
                '/=\\{([a-zA-Z0-9_-]+)\}/',
                '=(?P<$1>[A-Za-z0-9\-_]+)',
                $query
            );

            $query = preg_replace(
                '/=\\{([a-zA-Z0-9_-]+):([^}]+)\}/',
                '=(?P<$1>$2)',
                $query
            );

            $query = preg_replace(
                '/=\\{([a-zA-Z0-9_-]+)\?\}/',
                '=(?P<$1>[A-Za-z0-9\-_]+)?',
                $query
            );

            $pattern = '^' . $path . '\?' . $query . '$';
        } else {
            $pattern = '^' . $path . '$';
        }

        return '#' . $pattern . '#';
    }

    public function convert($pattern, $params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $positions = $this->toMap($pattern);
        if ($positions === false) {
            $positions = [];
        }
        $pattern = array_filter(explode('/', $pattern));

        if (count($positions) < count($pattern)) {

            $uri = [];
            foreach ($pattern as $key => $element) {
                if (in_array($key - 1, $positions)) {
                    $uri[] = array_shift($params);
                } else {
                    $uri[] = $element;
                }
            }
            return implode('/', array_filter($uri));
        }
        return false;
    }

    public function isThereAnyHow($name)
    {
        return $this->route_names[$name] ?? false;
    }

    protected function addPost($pattern, $callback, $namespace, $isAuthenticated)
    {
        $settings = [];
        if (is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern  = $settings['set'];
        }

        if (empty($settings['namespace'])) {
            $settings['namespace'] = $namespace;
        }

        $values = $this->toMap($pattern);

        $this->routes_post[$this->definePattern($pattern)] = [
            'callback'        => $callback,
            'values'          => $values,
            'namespace'       => $settings['namespace'] ?? null,
            'isAuthenticated' => $isAuthenticated
        ];


        if (isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }

    protected function addGet($pattern, $callback, $namespace, $isAuthenticated)
    {
        $settings = [];
        if (is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern  = $settings['set'];
        }

        if (empty($settings['namespace'])) {
            $settings['namespace'] = $namespace;
        }

        $values = $this->toMap($pattern);

        $this->routes_get[$this->definePattern($pattern)] = [
            'callback'  => $callback,
            'values'    => $values,
            'namespace' => $settings['namespace'] ?? null,
            'isAuthenticated' => $isAuthenticated
        ];

        if (isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }

    // Validar esse metodo
    // Eu apenas copiei entao pode dar algum problema
    protected function addPatch($pattern, $callback, $namespace, $isAuthenticated)
    {
        $settings = [];
        if (is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern  = $settings['set'];
        }

        if (empty($settings['namespace'])) {
            $settings['namespace'] = $namespace;
        }

        $values = $this->toMap($pattern);

        $this->routes_put[$this->definePattern($pattern)] = [
            'callback' => $callback,
            'values' => $values,
            'namespace' => $settings['namespace'] ?? null,
            'isAuthenticated' => $isAuthenticated
        ];
        if (isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }

    protected function addPut($pattern, $callback, $namespace, $isAuthenticated)
    {
        $settings = [];
        if (is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern  = $settings['set'];
        }

        if (empty($settings['namespace'])) {
            $settings['namespace'] = $namespace;
        }

        $values = $this->toMap($pattern);

        $this->routes_put[$this->definePattern($pattern)] = [
            'callback' => $callback,
            'values' => $values,
            'namespace' => $settings['namespace'] ?? null,
            'isAuthenticated' => $isAuthenticated
        ];
        if (isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }

    protected function addDelete($pattern, $callback, $namespace, $isAuthenticated)
    {
        $settings = [];
        if (is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern  = $settings['set'];
        }

        if (empty($settings['namespace'])) {
            $settings['namespace'] = $namespace;
        }

        $values = $this->toMap($pattern);

        $this->routes_delete[$this->definePattern($pattern)] = [
            'callback'        => $callback,
            'values'          => $values,
            'namespace'       => $settings['namespace'] ?? null,
            'isAuthenticated' => $isAuthenticated
        ];

        if (isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }

    protected function definePatternOLD($pattern)
    {
        $pattern = implode('/', array_filter(explode('/', $pattern)));
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

        if (preg_match("/\{[A-Za-z0-9\_\-]{1,}\}/", $pattern)) {
            $pattern = preg_replace("/\{[A-Za-z0-9\_\-]{1,}\}/", "[A-Za-z0-9]{1,}", $pattern);
        }

        return $pattern;
    }
}
