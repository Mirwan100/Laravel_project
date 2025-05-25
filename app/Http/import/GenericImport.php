<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class GenericImport implements ToModel
{
    protected $modelClass;

    public function __construct(Model $model)
    {
        $this->modelClass = get_class($model);
    }

    public function model(array $row)
    {
        return new $this->modelClass($row);
    }
}
