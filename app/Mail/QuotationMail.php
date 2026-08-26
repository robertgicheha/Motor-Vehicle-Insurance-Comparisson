<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
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
    use Queueable, SerializesModels, UsesEmailTemplate;

    public Quotation $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('quotation'), 'Insurance Quotation - ' . $this->quotation->quotation_number, $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('quotation');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            view: 'emails.quotation',
            with: [
                'quotation' => $this->quotation,
                'url' => route('quotations.show', $this->quotation),
            ],
        );
    }

    protected function variables(): array
    {
        return [
            'customer_name' => $this->quotation->user?->full_name ?? 'Valued Customer',
            'quotation_number' => $this->quotation->quotation_number,
            'company' => $this->quotation->company?->name ?? 'N/A',
            'vehicle' => trim(($this->quotation->car_make ?? '') . ' ' . ($this->quotation->car_model ?? '')),
            'valuation' => number_format($this->quotation->car_valuation, 2),
            'premium' => number_format($this->quotation->premium, 2),
            'total_premium' => number_format($this->quotation->total_premium, 2),
            'view_url' => route('quotations.show', $this->quotation),
            'app_name' => config('app.name'),
        ];
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
