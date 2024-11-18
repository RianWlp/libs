<?php

namespace RianWlp\Libs\core;

use RianWlp\Libs\core\ActiveRecords;
use stdClass;

abstract class Entidade extends ActiveRecords
{
    /**
     * Atributos de log, podem ser usados para informar quem e quando fez a operacao no registro
     */

    protected $dt_criacao;
    protected $dt_incluido;
    protected $hora_incluido;

    protected $dt_atualizado;
    protected $hora_atualizado;

    protected $dt_deletado;
    protected $hora_deletado;

    // O método get retorna um objeto stdClass
    // public function get(int $id): stdClass
    // {
    //     $explode = explode('\\', get_called_class());
    //     $classe  = end($explode);

    //     // $obj = (object)array('id' => 1, 'nome' => 'rianwlp', 'teste' => 'teste');
    //     $obj = $this->getById((int)$id);
    //     if ($obj === null) {
    //         throw new \Exception("O $classe com ID $id não encontrado.");
    //     }

    //     $vars = get_class_vars(get_called_class());

    //     $objNew = new stdClass();
    //     $objNew->dt_criacao    = $this->dt_criacao;
    //     $objNew->dt_atualizado = $this->dt_atualizado;
    //     $objNew->dt_deletado   = $this->dt_deletado;

    //     foreach ($vars as $key => $var) {

    //         $objNew->$key = $obj->{$key};
    //     }

    //     return $objNew;
    // }

    public function getDtCriacao(): ?string
    {
        return $this->dt_criacao;
    }

    public function getDtIncluido(): ?string
    {
        return $this->dt_incluido;
    }

    public function getDtAtualizado(): ?string
    {
        return $this->dt_atualizado;
    }

    public function getDtDeletado(): ?string
    {
        return $this->dt_deletado;
    }


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
