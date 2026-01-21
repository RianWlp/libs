<?php

namespace RianWlp\Libs\core;

use RianWlp\Libs\core\ActiveRecordsV2;
use stdClass;

abstract class Entidade extends ActiveRecordsV2
{
    /**
     * Campos que NÃO podem ser hidratados automaticamente
     */
    protected array $guarded = [];

    protected ?string $created_at;
    protected ?string $updated_at;
    protected ?string $deleted_at;

    public function convertToObject(): stdClass
    {
        $simple_object = new stdClass();
        $properties    = self::getVars($this);

        foreach ($properties as $key => $value) {
            $simple_object->$key = $value;
        }

        return $simple_object;
    }

    protected function fill(stdClass $attributes): void
    {
        $fields = get_object_vars($attributes);
        foreach ($fields as $field => $value) {

            // se estiver na blacklist, ignora
            if (in_array($field, $this->guarded, true)) {
                continue;
            }

            // se a propriedade não existir na entidade, ignora
            if (!property_exists($this, $field)) {
                continue;
            }

            // bloqueia objetos (relacionamentos)
            // if (is_object($value)) {
            //     continue;
            // }

            $this->$field = $value;
        }
    }

    public function loadAggregate(string $property, Entidade $entity): void
    {
        // $this->$property = $entity;
    }
}
