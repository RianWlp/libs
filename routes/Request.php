<?php

namespace RianWlp\Libs\routes;

class Request
{
    protected $files;
    protected $base;
    protected $uri;
    protected $method;
    protected $protocol;
    protected $data = [];

    public function __construct()
    {
        // $_REQUEST['uri']; Esse parametro (e) definido dentro do arquivo .htaccess que (e)
        // utilizado pelo Apache

        $this->base     = $_SERVER['REQUEST_URI'];
        $this->uri      = $_SERVER['REQUEST_URI'] ?? '/'; // AQUI DEVE ser assim $this->uri  = $_REQUEST['uri'] ?? '/';
        $this->method   = strtolower($_SERVER['REQUEST_METHOD']);
        $this->protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
        $this->setData();

        if (count($_FILES) > 0) {
            $this->setFiles();
        }
    }

    public function getData()
    {
        return $this->data;
    }

    protected function setData()
    {
        $this->data = [];

        $raw = file_get_contents('php://input');

        // Se o método for POST ou GET, use $_POST e $_GET normalmente
        switch ($this->method) {

            case 'post':
                $this->data = array_merge($this->data, $_POST);
                break;
            case 'get':
                $this->data = array_merge($this->data, $_GET);
                break;
        }

        // Se houver corpo (PUT, PATCH, DELETE também caem aqui)
        if (!empty($raw)) {

            // 1. Tenta decodificar como JSON
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->data = array_merge($this->data, $json);
                self::decodeJsonRecursive();
                return;
            }

            // 2. Se não for JSON, tenta como query-string
            parse_str($raw, $parsed);
            if (!empty($parsed)) {
                $this->data = array_merge($this->data, $parsed);
                self::decodeJsonRecursive();
                return;
            }
        }
    }

    protected function decodeJsonRecursive()
    {
        foreach ($this->data as $key => $data) {
            $json = json_decode($data);
            if ($json) {
                $this->data[$key] = $json;
            }
        }
    }

    protected function setFiles()
    {
        foreach ($_FILES as $key => $value) {
            $this->files[$key] = $value;
        }
    }

    public function base()
    {
        return $this->base;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function method()
    {
        return $this->method;
    }

    public function all()
    {
        return $this->data;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
    }

    public function hasFile($key)
    {
        return isset($this->files[$key]);
    }

    public function file($key)
    {
        if (isset($this->files[$key])) {
            return $this->files[$key];
        }
    }
}
