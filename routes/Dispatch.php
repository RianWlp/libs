<?php

namespace Libs\routes;

use Libs\log\Log;
use Libs\utils\Token;
// use Db\DbConnect;

class Dispatch
{
    public const DEFAULT_NAMESPACE = "App\\controllers\\api\\";

    public function dispach($callback, $params = [], $namespace = self::DEFAULT_NAMESPACE)
    {
        // self::token($params['token']);
        // var_dump($params['token']);
        // die();

        // Antes de poder executar a rota eu preciso validar o TOKEN da mesma
        // Validar se ele (e) valido
        // Validar o tempo restante e se ja nao passou

        // O TOKEN das aplicacoes (user ADM) por ser sem tempo (acho)
        // A pessoa cada vez que faz login recebe um token novo
        // O user na coluna TOKEN vai receber o token que vai ser validado toda vez ao tentar passar por esse caminho aqui
        // assim eu sei que ele esta tentando acessar uma rota e que ele pode fazer isso

        if (is_callable($callback['callback'])) {
            return call_user_func_array($callback['callback'], array_values($params));
        }

        if ((is_string($callback['callback'])) && (str_contains($callback['callback'], '@'))) {

            $callback["callback"] = explode('@', $callback['callback']);
            $controller           = $namespace . $callback["callback"][0];
            $method               = $callback["callback"][1];

            $rc = new \ReflectionClass($controller);

            if (!$rc->isInstantiable() && $rc->hasMethod($method)) {
                throw new \Exception('Erro ao despachar: controller não pode ser instanciado, ou método não exite');
            }

            // Nao faz o tratamento do POST para enviar via parametro por enquanto, devo pegar pelo $_REQUEST ou $_POST
            return call_user_func_array([new $controller, $method], [(object)$params]);
        }
        throw new \Exception('Erro ao despachar: método não implementado');
    }


    // Preciso fazer para a ROTA ser obrigatoria (obrigar o TOKEN)
    // Preciso fazer para o TOKEN nao durar 15 minutos, quando a pessoa der LOGOUT o token (e) invalidado e precisa gerar outro quando entrar novamente,
    // acho que criar o token no Usuario nao (e) o correto

    // public function storeLog(stdClass $params)
    // public function storeLog()
    // {
    //     $connect = new DbConnect();
    //     // $log = new Log($connect);

    //     // $log->setFkUsuario($fk_usuario);
    //     // $log->setEndpoint($endpoint);
    //     // $log->setMetodo($metodo);
    //     // $log->setDadosRequisicao($dados_requisicao);
    //     // $log->setDadosResposta($dados_resposta);
    //     // $log->setStatusHttp($status_http);
    //     // // $log->setDtRequisicao($dt_requisicao);
    //     // $log->setIpOrigem($ip_origem);

    //     // // Depois, salvamos o log no banco ou fazemos o tratamento necessário.
    //     // $log->store();
    // }

    // Acho que isso nao vai ficar aqui
    protected function token(string $token)
    {

        $objToken = new Token();
        var_dump($objToken->validate($token));
    }
}
