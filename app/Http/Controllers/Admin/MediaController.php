<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->paginate(30);
        return view('admin.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $targetDir = public_path('images');
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalFilename);

            $file->move($targetDir, $filename);

            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'path' => 'images/' . $filename,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'disk' => 'public_images',
                'uploaded_by' => auth()->id(),
            ]);

            AuditService::activity('media_uploaded', "Media file uploaded: {$originalFilename}");

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'media' => $media,
                    'url' => $media->url(),
                ]);
            }

            return back()->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Upload failed: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['file' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function destroy(Media $media)
    {
        $filePath = public_path($media->path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        AuditService::activity('media_deleted', "Media file deleted: {$media->original_filename}");

        $media->delete();

        return back()->with('success', 'File deleted successfully.');
    }

    public function list()
    {
        $media = Media::latest()->get()->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->url(),
            'filename' => $m->original_filename,
            'size' => $m->file_size,
            'date' => $m->created_at->format('d M Y'),
        ]);

        return response()->json($media);
    }
}
