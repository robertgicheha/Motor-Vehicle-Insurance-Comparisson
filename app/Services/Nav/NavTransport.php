<?php

namespace App\Services\Nav;

use Illuminate\Support\Facades\Log;
use Stringable;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Part\DataPart;

/**
 * Mail transport that routes every outgoing message through NAV's SOAP
 * mail codeunit instead of SMTP. Registered as the "nav" mailer in
 * config/mail.php — once MAIL_MAILER=nav, every existing Mail::send() /
 * Mailable / Notification "mail" channel call in the app is delivered
 * this way with no changes to those call sites.
 *
 * Resilience is handled entirely here, at the transport level, not in
 * individual controllers: if NAV_MAIL_ENABLED is off, or the SOAP call
 * fails for any reason, the message is sent through the app's "smtp"
 * mailer instead — by grabbing that mailer's own Symfony transport
 * directly (not via Mail::send(), which would just re-enter this same
 * "nav" transport since it's the app default and cause infinite
 * recursion/a stack overflow).
 */
class NavTransport extends AbstractTransport implements Stringable
{
    public function __construct(protected NavMailService $nav)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        if ($this->nav->isEnabled()) {
            try {
                $email = MessageConverter::toEmail($message->getOriginalMessage());

                $this->nav->sendMail([
                    'to' => $this->addressList($email->getTo()),
                    'cc' => $this->addressList($email->getCc()),
                    'bcc' => $this->addressList($email->getBcc()),
                    'from' => $this->addressList($email->getFrom()),
                    'reply_to' => $this->addressList($email->getReplyTo()),
                    'subject' => (string) $email->getSubject(),
                    'body' => (string) ($email->getHtmlBody() ?? $email->getTextBody()),
                    'attachments' => $this->attachments($email),
                ]);

                return;
            } catch (\Throwable $e) {
                Log::warning('NAV mail transport failed, falling back to SMTP: ' . $e->getMessage());
            }
        }

        $this->fallbackTransport()->send($message->getOriginalMessage(), $message->getEnvelope());
    }

    /**
     * The app's "smtp" mailer's own Symfony transport, fetched directly so
     * sending through it never re-enters this "nav" transport.
     */
    protected function fallbackTransport(): \Symfony\Component\Mailer\Transport\TransportInterface
    {
        return app('mail.manager')->mailer('smtp')->getSymfonyTransport();
    }

    /**
     * @param Address[] $addresses
     */
    protected function addressList(array $addresses): array
    {
        return array_map(fn (Address $address) => [
            'address' => $address->getAddress(),
            'name' => $address->getName(),
        ], $addresses);
    }

    protected function attachments(Email $email): array
    {
        $attachments = [];

        foreach ($email->getAttachments() as $part) {
            /** @var DataPart $part */
            $attachments[] = [
                'filename' => $part->getFilename(),
                'mime_type' => $part->getMediaType() . '/' . $part->getMediaSubtype(),
                'content_base64' => base64_encode($part->getBody()),
            ];
        }

        return $attachments;
    }

    public function __toString(): string
    {
        return 'nav';
    }
}
