<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CompareAllQuotationsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Collection $quotations Unsaved Quotation instances (one per insurer), as built by
     *                                QuotationController::buildCompareAllQuotations() — used for the email body summary table.
     * @param array $pdfData Combined comparison data built by QuotationController::buildCompareAllPdfData(),
     *                        used to render the same side-by-side comparison PDF as quotation-compare.blade.php.
     */
    public function __construct(public Collection $quotations, public string $customerName, public array $pdfData = [])
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Motor Insurance Quotation Comparison',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.compare-all-quotations',
            with: [
                'quotations' => $this->quotations,
                'customerName' => $this->customerName,
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.compare-all-quotations', $this->pdfData ?: ['quotations' => $this->quotations])
            ->setPaper('a4', 'landscape');

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'compare-all-insurers-' . now()->format('Ymd-His') . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
