<?php

namespace Libs\core;
use Libs\core\ActiveRecords;

abstract class Entidade extends ActiveRecords
{

    /**
     * Atributos de log, podem ser usados para informar quem e quando fez a operacao no registro
     */

    // protected $id_usuario_incluiu;
    protected $data_incluido;
    protected $hora_incluido;

    protected $data_atualizado;
    protected $hora_atualizado;

    // protected $id_usuario_deletou;
    protected $data_deletado;
    protected $hora_deletado;

    /**
     * O metodo store chama o metodo save do ActiveRecords ou seja, delega a sua funcao para outra classe 
     */

    // abstract function store();

    /**
     * O metodo remove faz um HARD DELETE no banco
     */

    // abstract function remove();

    // abstract function load();

    /**
     * O metodo lista vai montar uma lista do objeto com os campos que foram definidos
     * Passa como parametro o que veio da tela para fazer a filtragem no banco 
     */

    // abstract function list($request);

    /**
     * O metodo setId vai fazer o set do que foi pedido pelo controller
     * Se ele pediu, passou no calling que vai ser id ele vai tentar fazer o set se for um numero
     *
     * Esse metodo pode fazer o set de varias informacoes a fim apenas de obter o indice do banco, verificar se ele existe
     */

    // abstract function setId($id, $calling);

    // abstract function __get($property);
}
