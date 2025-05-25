<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldQueue;

class ProjectExport implements FromCollection, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    /**
     * @var array<string>
     */
    protected array $fields;

    /**
     * @param  array<string>  $fields
     */
    public function __construct(array $fields = [])
    {
        // default fallback fields
        $this->fields = $fields ?: ['id', 'name', 'created_at'];
    }

    /**
     * Compile data to export
     */
    public function collection()
    {
        return Project::select($this->fields)->get();
    }

    /**
     * Baris header di Excel
     *
     * @return string[]
     */
    public function headings(): array
    {
        // Ubah underscore ke spasi & Title Case
        return array_map(fn($f) => Str::title(str_replace('_', ' ', $f)), $this->fields);
    }

    /**
     * Mapping model ke row array
     *
     * @param  \App\Models\Project  $project
     */
    public function map($project): array
    {
        return array_map(fn($field) => $project->{$field}, $this->fields);
    }
}
