<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $fillable = [
        'key',
        'subject',
        'body',
        'type',
        'letterhead_company',
        'letterhead_color',
        'letterhead_logo',
        'letterhead_address',
        'letterhead_website',
        'letterhead_logo_header',
        'letterhead_logo_footer',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'letterhead_logo_header' => 'boolean',
            'letterhead_logo_footer' => 'boolean',
        ];
    }

    public function renderBody(array $variables = []): string
    {
        return $this->renderContent($this->body ?? '', $variables);
    }

    public function renderContent(string $content, array $variables = []): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($variables) {
            $value = $variables[$matches[1]] ?? null;

            if (is_callable($value)) {
                return (string) call_user_func($value);
            }

            return $value !== null ? (string) $value : $matches[0];
        }, $content) ?? $content;
    }

    public function letterheadConfig(): array
    {
        return [
            'company' => $this->letterhead_company ?: config('app.name'),
            'color' => $this->letterhead_color ?: '#004f8c',
            'address' => $this->letterhead_address ?: 'MIC Global Risks (Kenya) Limited, P.O. Box 10000 - 00100 Nairobi, Kenya',
            'website' => $this->letterhead_website ?: 'www.micglobalrisks.com',
        ];
    }

    public function logoPath(): ?string
    {
        if ($this->letterhead_logo) {
            $path = public_path('storage/' . $this->letterhead_logo);
            if (file_exists($path)) {
                return $path;
            }
        }

        $default = public_path('images/MIC-Global-Risks_logo_no_background.png');

        return file_exists($default) ? $default : null;
    }
}
