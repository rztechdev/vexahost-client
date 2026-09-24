<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file'              => 'required|file|max:20480', // 20MB max
            'documentable_type' => 'required|in:project,task',
            'documentable_id'   => 'required|integer',
        ]);

        if ($request->documentable_type === 'project') {
            $documentable = Project::findOrFail($request->documentable_id);
        } else {
            $documentable = Task::findOrFail($request->documentable_id);
        }

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('documents', 'public');

        $documentable->documents()->create([
            'file_path'   => $filePath,
            'file_name'   => $fileName,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function download(Document $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
