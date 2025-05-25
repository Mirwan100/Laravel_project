<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericExport;
use App\Imports\GenericImport;

class ExcelController extends Controller
{
    public function exportForm(Request $request, string $form)
    {
        $fields = $request->query('fields', []);
        abort_if(empty($fields), 400, 'Kolom wajib dipilih');

        $model = $this->resolveModel($form);
        return Excel::download(new GenericExport($model, $fields), "$form-export.xlsx");
    }

    public function showImportForm(string $form)
    {
        return view('excel-import', ['form' => $form]);
    }

    public function importForm(Request $request, string $form)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        $model = $this->resolveModel($form);
        Excel::import(new GenericImport($model), $request->file('file'));
        return back()->with('success', 'Import berhasil');
    }

    protected function resolveModel(string $form)
    {
        $map = [
            'projects' => \App\Models\Project::class,
            'tasks' => \App\Models\Task::class,
            'documents' => \App\Models\Document::class,
        ];
        abort_unless(isset($map[$form]), 404);
        return new $map[$form];
    }
}
