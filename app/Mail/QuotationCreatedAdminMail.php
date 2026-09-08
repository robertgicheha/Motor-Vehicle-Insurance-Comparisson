<?php

namespace App\Mail;

use App\Models\Quotation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationCreatedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quotation $quotation, public User $admin) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A Quotation ' . $this->quotation->quotation_number . ' has been created successfully',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quotation-created-admin',
            with: [
                'quotation' => $this->quotation,
                'admin' => $this->admin,
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $this->quotation]);

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'quotation-' . str_replace(['/', '\\'], '-', $this->quotation->quotation_number) . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
