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
        $dbMedia = Media::latest()->get()->keyBy('filename');
        $fsMedia = $this->scanFilesystem();

        $media = $fsMedia->map(function ($item) use ($dbMedia) {
            if ($dbMedia->has($item['filename'])) {
                $m = $dbMedia[$item['filename']];
                $item['id'] = $m->id;
                $item['original_filename'] = $m->original_filename;
                $item['file_size'] = $m->file_size;
                $item['created_at'] = $m->created_at;
                $item['in_db'] = true;
            } else {
                $item['id'] = null;
                $item['original_filename'] = $item['filename'];
                $item['file_size'] = $item['size'];
                $item['created_at'] = null;
                $item['in_db'] = false;
            }
            return $item;
        })->sortByDesc('created_at');

        return view('admin.media.index', ['media' => $media]);
    }

    protected function scanFilesystem()
    {
        $dir = public_path('images');
        $items = collect();
        if (!is_dir($dir)) {
            return $items;
        }
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'ico'];
        $iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator);
        foreach ($files as $file) {
            if ($file->isFile() && in_array(strtolower($file->getExtension()), $extensions)) {
                $subPathname = $files->getSubPathname();
                $subPathname = str_replace('\\', '/', $subPathname);
                $relativePath = 'images/' . $subPathname;
                $encodedSegments = array_map('rawurlencode', explode('/', $relativePath));
                $items->push([
                    'filename' => $file->getFilename(),
                    'path' => $relativePath,
                    'url' => asset(implode('/', $encodedSegments)),
                    'size' => $file->getSize(),
                    'last_modified' => $file->getMTime(),
                ]);
            }
        }
        return $items;
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

    /**
     * Resolves a media-relative path to a real filesystem path, rejecting
     * anything (e.g. via "../" traversal) that would resolve outside the
     * public/images media root.
     */
    private function resolveMediaPath(string $relativePath): ?string
    {
        $mediaRoot = realpath(public_path('images'));
        if (!$mediaRoot) {
            return null;
        }

        $real = realpath(public_path(ltrim($relativePath, '/')));
        if ($real === false || !str_starts_with($real, $mediaRoot . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $real;
    }

    public function rename(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_path' => 'required|string',
            'new_name' => 'required|string|regex:/^[a-zA-Z0-9._-]+$/',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldPath = $this->resolveMediaPath($request->old_path);

        if (!$oldPath) {
            return back()->withErrors(['file' => 'File not found.']);
        }

        $dir = dirname($oldPath);
        $newPath = $dir . DIRECTORY_SEPARATOR . $request->new_name;

        if (file_exists($newPath)) {
            return back()->withErrors(['file' => 'A file with that name already exists.']);
        }

        rename($oldPath, $newPath);

        $relativeOld = ltrim($request->old_path, '/');
        $relativeNew = dirname($relativeOld) . '/' . $request->new_name;

        Media::where('path', $relativeOld)->update(['path' => $relativeNew, 'filename' => $request->new_name]);

        AuditService::activity('media_renamed', "Media file renamed: " . basename($oldPath) . " → {$request->new_name}");

        return back()->with('success', 'File renamed successfully.');
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

    public function destroyFile($path)
    {
        $decodedPath = base64_decode($path);
        $filePath = $this->resolveMediaPath($decodedPath);

        if (!$filePath) {
            return back()->withErrors(['file' => 'File not found.']);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        Media::where('path', $decodedPath)->delete();

        AuditService::activity('media_deleted', "Media file deleted: " . basename($decodedPath));

        return back()->with('success', 'File deleted successfully.');
    }

    public function list()
    {
        $dbMedia = Media::latest()->get()->keyBy('filename');
        $fsMedia = $this->scanFilesystem();

        $media = $fsMedia->map(function ($item) use ($dbMedia) {
            return [
                'id' => $dbMedia->has($item['filename']) ? $dbMedia[$item['filename']]->id : null,
                'url' => $item['url'],
                'path' => $item['path'],
                'filename' => $dbMedia->has($item['filename'])
                    ? $dbMedia[$item['filename']]->original_filename
                    : $item['filename'],
                'size' => $item['size'],
                'date' => $dbMedia->has($item['filename'])
                    ? $dbMedia[$item['filename']]->created_at->format('d M Y')
                    : date('d M Y', $item['last_modified']),
            ];
        })->sortByDesc('date')->values();

        return response()->json($media);
    }
}
