<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskExport implements FromCollection, WithMapping, WithHeadings
{
    protected array $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function collection()
    {
        // Sertakan trashed untuk konsistensi dengan Project
        return Task::withTrashed()->get();
    }

    public function map($task): array
    {
        return collect($this->fields)->map(fn($f) => data_get($task, $f))->toArray();
    }

    public function headings(): array
    {
        // Buat label dari nama field
        return collect($this->fields)
            ->map(fn($f) => ucwords(str_replace('_', ' ', $f)))
            ->toArray();
    }
}
