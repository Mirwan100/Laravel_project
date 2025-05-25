<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportProjects implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected array $fields;
    protected int   $userId;
    protected string $fileName;

    public function __construct(array $fields, int $userId)
    {
        $this->fields   = $fields;
        $this->userId   = $userId;
        $this->fileName = "exports/projects_{$userId}_" . now()->format('Ymd_His') . '.csv';
    }

    public function handle()
    {
        $stream = fopen('php://temp', 'r+');
        // Header dinamis
        fputcsv($stream, array_map(fn($f)=> Str::title(str_replace('_',' ',$f)), $this->fields));

        // Data
        Project::select($this->fields)
            ->chunk(500, function($projects) use ($stream) {
                foreach ($projects as $p) {
                    fputcsv($stream, array_map(fn($f)=> $p->{$f}, $this->fields));
                }
            });

        rewind($stream);
        Storage::disk('local')->put($this->fileName, stream_get_contents($stream));
        fclose($stream);
    }

    public static function downloadResponse(string $file)
    {
        return response()->streamDownload(
            fn() => print(Storage::disk('local')->get($file)),
            basename($file),
            ['Content-Type' => 'text/csv']
        );
    }
}
