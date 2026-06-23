<?php

namespace App\Mail;

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
    use Queueable, SerializesModels;

    public Sticker $sticker;

    public function __construct(Sticker $sticker)
    {
        $this->sticker = $sticker;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Insurance Certificate - ' . $this->sticker->sticker_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sticker',
            with: [
                'sticker' => $this->sticker,
                'url' => route('stickers.show', $this->sticker),
            ],
        );
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
