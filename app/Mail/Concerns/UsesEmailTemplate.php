<?php

namespace App\Mail\Concerns;

use App\Models\EmailSetting;
use Illuminate\Mail\Mailables\Content;

trait UsesEmailTemplate
{
    protected function template(string $key): ?EmailSetting
    {
        return EmailSetting::where('key', $key)->first();
    }

    protected function envelopeSubject(?EmailSetting $template, string $fallback, array $variables = []): string
    {
        if ($template && $template->subject) {
            return $template->renderContent($template->subject, $variables);
        }

        return $fallback;
    }

    protected function dynamicContent(?EmailSetting $template, array $variables = []): Content
    {
        return new Content(
            view: 'emails.dynamic',
            with: [
                'emailSetting' => $template,
                'content' => $template->renderBody($variables),
                'app_name' => config('app.name'),
            ],
        );
    }
}
