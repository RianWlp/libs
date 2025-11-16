<?php

namespace RianWlp\Libs\core;

use RianWlp\Libs\core\ActiveRecords;
use RianWlp\Libs\core\ActiveRecordsV2;

abstract class Entidade extends ActiveRecordsV2
{
    protected ?string $created_at;
    protected ?string $updated_at;
    protected ?string $deleted_at;
}
