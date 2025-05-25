<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua nama project untuk dropdown filter
        $projects = Project::pluck('name', 'id');

        // Base query dengan relasi project
        $query = Task::with('project');

        // Jika route ke trash, tampilkan hanya yang terhapus
        if ($request->routeIs('tasks.trash')) {
            $query->onlyTrashed();
        }

        // Filter berdasarkan project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search berdasarkan title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $allowed = ['id', 'title', 'project', 'due_at'];
        $sortBy = in_array($request->get('sort_by'), $allowed)
            ? $request->get('sort_by')
            : 'due_at';
        $sortOrder = $request->get('sort_order') === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'project') {
            $query->join('projects', 'tasks.project_id', '=', 'projects.id')
                  ->orderBy('projects.name', $sortOrder)
                  ->select('tasks.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginate tasks
        $tasks = $query->paginate(10)->appends($request->query());

        // Ambil audit untuk task yang ditampilkan
        $taskIds = $tasks->pluck('id')->toArray();
        $audits = Audit::where('auditable_type', Task::class)
            ->whereIn('auditable_id', $taskIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('tasks.index', compact('tasks', 'projects', 'audits'));
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
            'project_id' => 'required|exists:projects,id',
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'metadata'   => 'nullable|json',
            'due_at'     => 'required|date',
            'done'       => 'boolean',
        ]);

        $data['id'] = (string) Str::uuid();
        Task::create($data);

        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil dibuat.');
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
            'project_id' => 'required|exists:projects,id',
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'metadata'   => 'nullable|json',
            'due_at'     => 'required|date',
            'done'       => 'boolean',
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil dipindah ke trash.');
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
}
