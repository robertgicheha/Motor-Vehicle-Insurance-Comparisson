<?php

namespace App\Mail;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Quotation $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Insurance Quotation - ' . $this->quotation->quotation_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation',
            with: [
                'quotation' => $this->quotation,
                'url' => route('quotations.show', $this->quotation),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $this->quotation]);
        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                "quotation-{$this->quotation->quotation_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
