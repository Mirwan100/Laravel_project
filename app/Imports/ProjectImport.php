<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, ShouldQueue
{
    public function model(array $row)
    {
        return new Project([
            'uuid' => $row['uuid'] ?? Str::uuid(),
            'name' => $row['name'],
            'start_at' => $row['start_at'] ?? now(),
            'is_active' => $row['is_active'] ?? true,
            'settings' => $row['settings'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'uuid' => 'sometimes|uuid',
            'start_at' => 'sometimes|date',
            'is_active' => 'sometimes|boolean',
            'settings' => 'sometimes|json',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}