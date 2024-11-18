<?php

namespace RianWlp\Libs\core;

// use RianWlp\Db\DbConnect;

require 'Entidade.php';
class Teste extends Entidade {

    protected int $id;
    protected string $teste;
    protected string $nome;
    protected string $idade;
    protected string $merda;

}

$teste = new Teste ();
$teste->get(1);
// new Teste(new DbConnect);