<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;
use App\Jobs\ExportProjects;
use App\Jobs\ImportProjects;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted', false);
        $projectId   = $request->input('project_id');

        $query = Project::query();
        if ($showDeleted) {
            $query->withTrashed();
        }
        if ($projectId) {
            $query->where('id', $projectId);
        }
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($sortBy = $request->input('sort_by')) {
            $dir = $request->input('sort_direction', 'asc');
            $query->orderBy($sortBy, $dir);
        }

        $projects    = $query->paginate(8)->appends($request->query());
        $allProjects = Project::orderBy('name')->get(['id', 'name']);

        $auditQuery = Audit::with('user', 'auditable')
            ->where('auditable_type', Project::class);
        if ($projectId) {
            $auditQuery->where('auditable_id', $projectId);
        }
        $audits = $auditQuery->latest()
            ->paginate(8)
            ->appends($request->query());

        $exportableFields = [
            'id',
            'uuid',
            'name',
            'start_at',
            'is_active',
            'settings',
            'created_at',
            'updated_at',
        ];

        return view('projects.index', compact(
            'projects',
            'showDeleted',
            'allProjects',
            'audits',
            'exportableFields'
        ));
    }

    /**
     * Dispatch job untuk export dynamic fields ke CSV (async)
     */
    public function export(Request $request)
    {
        $exportable = ['id','uuid','name','start_at','is_active','settings','created_at','updated_at'];
        $request->validate([
            'fields'   => 'required|array|min:1',
            'fields.*' => 'in:'.implode(',',$exportable),
        ]);

        ExportProjects::dispatch($request->fields, auth()->id());

        return back()->with('export_status', 'Export sedang diproses di background. Silakan cek link download nanti.');
    }

    /**
     * Unduh hasil export terbaru untuk user
     */
    public function downloadExport()
    {
        $files = Storage::disk('local')->files('exports');
        $userPrefix = "exports/projects_" . auth()->id();
        $latest = collect($files)
            ->filter(fn($f) => str_starts_with($f, $userPrefix))
            ->sortDesc()
            ->first();

        if (! $latest) {
            abort(404,'File tidak ditemukan');
        }

        return ExportProjects::downloadResponse($latest);
    }

    /**
     * Import Excel/CSV processing via queue
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        // Simpan file ke storage untuk diproses asynchronous
        $path = $request->file('file')->store('imports');

        // Dispatch job import
        ImportProjects::dispatch($path, auth()->id());

        return back()->with('import_status', 'Import sedang diproses di background. Cek log atau notifikasi setelah selesai.');
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'      => 'required|string|max:255',
            'start_at'  => 'required|date',
            'is_active' => 'boolean',
            'settings'  => 'nullable|json',
        ]);
        $data['id'] = (string) Str::uuid();
        Project::create($data);

        return redirect()->route('projects.index')->with('success','Project created');
    }

    public function show(Project $project)
    {
        $audits = $project->audits()->with('user')->latest()->paginate(8);
        return view('projects.show', compact('project','audits'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $r, Project $project)
    {
        $data = $r->validate([
            'name'      => 'required|string|max:255',
            'start_at'  => 'required|date',
            'is_active' => 'boolean',
            'settings'  => 'nullable|json',
        ]);
        $project->update($data);

        return redirect()->route('projects.index')->with('success','Project updated');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return back()->with('success','Project deleted');
    }

    public function trash()
    {
        $projects = Project::onlyTrashed()->paginate(8);
        return view('projects.trash',compact('projects'));
    }

    public function restore($id)
    {
        Project::withTrashed()->findOrFail($id)->restore();
        return back()->with('success','Project restored');
    }

    public function forceDelete($id)
    {
        Project::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success','Project permanently deleted');
    }

    /**
     * Restore semua trashed projects
     */
    public function restoreAll()
    {
        Project::onlyTrashed()->restore();
        return back()->with('success','Semua project yang di-trash telah dipulihkan');
    }
}
