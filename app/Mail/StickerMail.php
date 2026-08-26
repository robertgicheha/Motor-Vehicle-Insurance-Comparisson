<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\Sticker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StickerMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public Sticker $sticker;

    public function __construct(Sticker $sticker)
    {
        $this->sticker = $sticker;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('sticker'), 'Insurance Certificate - ' . $this->sticker->sticker_number, $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('sticker');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            view: 'emails.sticker',
            with: [
                'sticker' => $this->sticker,
                'url' => route('stickers.show', $this->sticker),
            ],
        );
    }

    protected function variables(): array
    {
        return [
            'customer_name' => $this->sticker->customer_name,
            'sticker_number' => $this->sticker->sticker_number,
            'company' => $this->sticker->company?->name ?? 'N/A',
            'vehicle' => trim(($this->sticker->car_make ?? '') . ' ' . ($this->sticker->car_model ?? '')),
            'valuation' => number_format($this->sticker->car_valuation, 2),
            'premium' => number_format($this->sticker->premium, 2),
            'view_url' => route('stickers.show', $this->sticker),
            'app_name' => config('app.name'),
        ];
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.sticker', ['sticker' => $this->sticker]);
        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                "certificate-{$this->sticker->sticker_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
