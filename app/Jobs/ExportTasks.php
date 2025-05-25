<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $fields,
        public int $userId
    ) {}

    public function handle()
    {
        $filename = "exports/tasks/{$this->userId}-".now()->format('Ymd-His').'.csv';
        $handle = fopen(Storage::path($filename), 'w');
        
        // Header
        fputcsv($handle, $this->fields);
        
        // Data
        Task::with('project')->chunk(100, function($tasks) use ($handle) {
            foreach ($tasks as $task) {
                $row = [];
                foreach ($this->fields as $field) {
                    $row[] = match($field) {
                        'done' => $task->done ? 'true' : 'false',
                        'metadata' => json_encode($task->metadata),
                        default => $task->{$field},
                    };
                }
                fputcsv($handle, $row);
            }
        });
        
        fclose($handle);
    }
}