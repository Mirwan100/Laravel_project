<?php
namespace App\Imports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, ShouldQueue
{
    public function model(array $row)
    {
        $fillable = (new Project)->getFillable();
        $data = [];
        
        foreach ($row as $key => $value) {
            if (in_array($key, $fillable)) {
                $data[$key] = $value;
            }
        }
        
        return new Project($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'uuid' => 'sometimes|uuid',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}