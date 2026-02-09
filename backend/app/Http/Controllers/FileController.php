<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->files();

        if ($request->has('sort_by')) {
            $direction = $request->get('sort_direction', 'asc');
            $query->orderBy($request->sort_by, $direction);
        } else {
            $query->latest();
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate(['file' => 'required|file|max:102400']); // 100MB

        $uploadedFile = $request->file('file');
        $storedName = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->storeAs('files', $storedName, 's3');

        $file = File::create([
            'user_id' => $request->user()->id,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'stored_name' => $storedName,
            'path' => $path,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
        ]);

        return response()->json($file, 201);
    }

    public function download(File $file, Request $request)
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        return Storage::disk('s3')->download($file->path, $file->original_name);
    }

    public function destroy(File $file, Request $request)
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        Storage::disk('s3')->delete($file->path);
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }

    public function share(File $file, Request $request)
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $token = $file->generateShareToken();
        $url = url("/api/files/shared/{$token}");

        return response()->json(['share_url' => $url]);
    }

    public function downloadShared($token)
    {
        $file = File::where('share_token', $token)->firstOrFail();
        return Storage::disk('s3')->download($file->path, $file->original_name);
    }
}