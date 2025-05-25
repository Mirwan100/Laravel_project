<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromArray, WithHeadings
{
    protected $model;
    protected $fields;

    public function __construct($model, $fields)
    {
        $this->model = $model;
        $this->fields = $fields;
    }

    public function array(): array
    {
        return $this->model->newQuery()
            ->select($this->fields)
            ->get()
            ->map(fn ($row) => $row->only($this->fields))
            ->toArray();
    }

    public function headings(): array
    {
        return $this->fields;
    }
}
