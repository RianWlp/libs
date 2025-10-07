<?php

namespace RianWlp\Libs\core;

use RianWlp\Libs\core\ActiveRecords;

abstract class Entidade extends ActiveRecords
{
    protected ?string $created_at;
    protected ?string $updated_at;
    protected ?string $deleted_at;
}
