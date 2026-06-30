<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = ['filename', 'original_filename', 'path', 'mime_type', 'file_size', 'disk', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return asset($this->path);
    }

    public function thumbnail(): string
    {
        return $this->url();
    }

    public static function uploadPath(): string
    {
        return 'images';
    }

    public static function uploadDisk(): string
    {
        return 'public_images';
    }
}
