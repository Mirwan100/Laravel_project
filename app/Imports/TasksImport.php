<?php

namespace App\Imports;

use App\Models\Task;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TaskImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Abaikan baris tanpa title
        if (empty($row['title'])) {
            return null;
        }

        return new Task([
            'id'          => (string) Str::uuid(),
            'project_id'  => $row['project_id'] ?? null,
            'title'       => $row['title'],
            'description' => $row['description'] ?? null,
            'metadata'    => $row['metadata'] ?? null,
            'due_at'      => $row['due_at'] ?? now(),
            'done'        => $row['done'] ?? false,
            'created_at'  => $row['created_at'] ?? now(),
            'updated_at'  => now(),
        ]);
    }
}
