<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;
use App\Jobs\ExportTasks;
use App\Exports\TaskExport;
use App\Imports\TaskImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::pluck('name', 'id');
        $query = Task::with('project');

        if ($request->routeIs('tasks.trash')) {
            $query->onlyTrashed();
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $allowed = ['id', 'title', 'project', 'due_at'];
        $sortBy = in_array($request->get('sort_by'), $allowed) ? $request->get('sort_by') : 'due_at';
        $sortOrder = $request->get('sort_order') === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'project') {
            $query->join('projects', 'tasks.project_id', '=', 'projects.id')
                  ->orderBy('projects.name', $sortOrder)
                  ->select('tasks.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate(10)->appends($request->query());

        $taskIds = $tasks->pluck('id')->toArray();
        $audits = Audit::where('auditable_type', Task::class)
            ->whereIn('auditable_id', $taskIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $exportableFields = [
            'id',
            'title',
            'description',
            'metadata',
            'project_id',
            'done',
            'due_at',
            'created_at',
            'updated_at',
        ];

        return view('tasks.index', compact('tasks', 'projects', 'audits', 'exportableFields'));
    }

    public function trash(Request $request)
    {
        return $this->index($request);
    }

    public function create()
    {
        $projects = Project::pluck('name', 'id');
        return view('tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'metadata'    => 'nullable|json',
            'due_at'      => 'required|date',
            'done'        => 'boolean',
        ]);

        $data['id'] = (string) Str::uuid();
        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task berhasil dibuat.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::pluck('name', 'id');
        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'metadata'    => 'nullable|json',
            'due_at'      => 'required|date',
            'done'        => 'boolean',
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task berhasil dipindah ke trash.');
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->restore();
        return back()->with('success', 'Task berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->forceDelete();
        return back()->with('success', 'Task berhasil dihapus permanen.');
    }

    // Export & Download
    public function export(Request $request)
    {
        $exportable = [
            'id','project_id','title','description',
            'metadata','done','due_at','created_at','updated_at'
        ];
        $fields = $request->input('fields', []);
        $request->validate([
            'fields'   => 'required|array|min:1',
            'fields.*' => 'in:'.implode(',',$exportable),
        ]);

        // Panggil job ExportTasks
        ExportTasks::dispatchSync($fields, auth()->id());

        return back()->with('export_status', 'Export selesai! Silakan unduh hasilnya di bawah');
    }

    public function downloadExport()
    {
        $files = Storage::disk('local')->files('exports');
        $prefix = "exports/tasks_" . auth()->id();
        $latest = collect($files)
            ->filter(fn($f) => str_starts_with($f, $prefix))
            ->sortDesc()
            ->first();

        if (! $latest) {
            abort(404, 'File tidak ditemukan');
        }

        return ExportTasks::downloadResponse($latest);
    }

    // Import
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new TaskImport, $request->file('file'));

        return back()->with('success', 'Import berhasil!');
    }
}
