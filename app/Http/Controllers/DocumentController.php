<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Models\Audit;
use App\Exports\DocumentExport;
use App\Imports\DocumentImport;
use Maatwebsite\Excel\Facades\Excel;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with('project')->get();
    
        // Base query Documents
        $queryDocs = Document::with('task.project')
            ->when($request->filled('filename'), fn($q) => $q->where('filename', 'LIKE', "%{$request->filename}%"))
            ->when($request->filled('task_id'),   fn($q) => $q->where('task_id', $request->task_id))
            ->when($request->filled('is_verified'), fn($q) => $q->where('is_verified', $request->is_verified));
    
        // Sorting: default by uploaded_at, or by uuid (id) if requested
        $sortBy  = $request->sort_by  ?? 'uploaded_at';
        $sortDir = $request->sort_dir ?? 'desc';
        $documents = $queryDocs
            ->orderBy($sortBy, $sortDir)
            ->paginate(8)
            ->withQueryString();
    
        // Audit Trail: selalu paginate 8, dan filter juga by filename → ambil auditable_id dari dokumen hasil pencarian
        $auditQuery = Audit::with([
                'user',
                'auditable' => fn($q) => $q->withTrashed(),
            ])
            ->where('auditable_type', Document::class)
            // filter audit berdasarkan dokumen yang matching filename
            ->when($request->filled('filename'), function($q) use ($request) {
                $ids = Document::where('filename', 'LIKE', "%{$request->filename}%")->pluck('id');
                $q->whereIn('auditable_id', $ids->all());
            })
            // jika filter by specific document
            ->when($request->filled('document_id'),
                  fn($q) => $q->where('auditable_id', $request->document_id))
            ->orderByDesc('created_at');
    
        $audits = $auditQuery
            ->paginate(8, ['*'], 'audit_page')
            ->withQueryString();
    
        return view('documents.index', compact('documents', 'tasks', 'audits'));
    }
    

    public function create()
    {
        $tasks = Task::with('project')->get();
        return view('documents.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|uuid|exists:tasks,id',
            'file' => 'required|file|mimes:pdf|min:100|max:500',
            'is_verivied' => 'sometimes|boolean'
        ]);

        $file = $request->file('file');
        
        Document::create([
            'task_id' => $validated['task_id'],
            'filename' => $file->getClientOriginalName(),
            'path' => $file->store('documents', 'public'),
            'is_verivied' => $request->has('is_verivied'),
            'uploaded_at' => now(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded!');
    }

    public function edit(Document $document)
    {
        $tasks = Task::with('project')->get();
        return view('documents.edit', compact('document', 'tasks'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'task_id' => 'required|uuid|exists:tasks,id',
            'file' => 'nullable|file|mimes:pdf|max:5120',
            'is_verivied' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->path);
            
            $file = $request->file('file');
            $document->update([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('documents', 'public'),
                'uploaded_at' => now(),
            ]);
        }

        $document->update([
            'task_id' => $validated['task_id'],
            'is_verivied' => $request->has('is_verivied'),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document updated!');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return back()->with('success', 'Document deleted!');
    }

    public function export()
{
    $fields = request()->input('fields', ['uuid', 'name', 'created_at']);
    return Excel::download(new DocumentExport($fields), 'Document.xlsx');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    Excel::import(new DocumentImport, $request->file('file'));
    
    return back()->with('success', 'Import berhasil!');
}
}