<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TasksExport implements FromQuery, WithHeadings, ShouldQueue
{
    protected array $fields;

    public function __construct(array $fields = ['id','title','due_at','done'])
    {
        $this->fields = $fields;
    }

    public function query()
    {
        return Task::query();
    }

    public function headings(): array
    {
        return $this->fields;
    }
}
