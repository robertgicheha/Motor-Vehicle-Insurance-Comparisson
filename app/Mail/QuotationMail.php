<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\Quotation;
use App\Services\PremiumCalculationService;
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
    protected array $breakdown;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;

        // Recomputed fresh from the current rate configuration — never the
        // quotation's own frozen total_premium/premium columns — so this
        // email always matches what the quotation detail page and PDF show,
        // even for a quotation created before a rate/formula fix landed.
        $this->breakdown = $quotation->company
            ? app(PremiumCalculationService::class)->calculate(
                $quotation->company,
                (float) $quotation->car_valuation,
                $quotation->vehicle_type ?? 'private',
                true,
                $quotation->num_passengers ?? 1,
                true,
                false
            )
            : [];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('quotation'), 'A Quotation ' . $this->quotation->quotation_number . ' has been created successfully', $this->variables()),
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
                'breakdown' => $this->breakdown,
                'url' => route('quotations.show', $this->quotation),
            ],
        );
    }

    protected function variables(): array
    {
        return [
            'customer_name' => $this->quotation->customer_name,
            'quotation_number' => $this->quotation->quotation_number,
            'company' => $this->quotation->company?->name ?? 'N/A',
            'vehicle' => trim(($this->quotation->car_make ?? '') . ' ' . ($this->quotation->car_model ?? '')),
            'valuation' => number_format($this->quotation->car_valuation, 2),
            'premium' => number_format($this->breakdown['basic_premium'] ?? $this->quotation->premium, 2),
            'total_premium' => number_format($this->breakdown['total_premium'] ?? $this->quotation->total_premium, 2),
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
