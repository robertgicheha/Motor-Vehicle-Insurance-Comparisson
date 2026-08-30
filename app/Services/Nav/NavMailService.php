<?php

namespace App\Services\Nav;

use Illuminate\Support\Facades\Log;
use RuntimeException;
use SoapFault;

/**
 * Calls a Microsoft NAV/Business Central Codeunit web service over SOAP+NTLM
 * to trigger an email send. NAV does the actual sending (via its own
 * configured mail account, e.g. noreply@...) — this service only invokes
 * the codeunit function and returns its result.
 *
 * This is used generically, not per-flow: {@see NavTransport} (registered as
 * the "nav" Symfony Mailer transport in config/mail.php) calls sendMail() for
 * every Mail::send()/Mailable/Notification "mail" the app sends, once
 * MAIL_MAILER=nav. NavTransport itself falls back to the "smtp" mailer if
 * isEnabled() is false or the SOAP call throws, so no controller needs to
 * know NAV exists.
 *
 * NAV should return a value that JSON-decodes (or is already) to
 * ['success' => bool, 'message' => string|null]. Any other shape is
 * accepted too — normalizeResult() wraps a bare scalar as ['raw' => ...].
 */
class NavMailService
{
    protected string $wsdl;
    protected string $function;
    protected string $userpwd;

    public function __construct()
    {
        $this->wsdl = (string) config('services.nav.mail_wsdl');
        $this->function = (string) config('services.nav.mail_function', 'SendMail');

        $domain = config('services.nav.ntlm_domain');
        $username = config('services.nav.ntlm_username');
        $password = config('services.nav.ntlm_password');

        $this->userpwd = ($domain ? "{$domain}\\{$username}" : (string) $username) . ':' . $password;
    }

    /**
     * Whether NAV email sending is turned on and minimally configured.
     * False (the safe default) means every send*() call below falls back
     * to Laravel's native Mail system without ever attempting SOAP.
     */
    public function isEnabled(): bool
    {
        return config('services.nav.enabled', false) && $this->wsdl !== '';
    }

    /**
     * Call the configured NAV mail-sending function with the given
     * parameters. Parameter names must match the NAV function's signature
     * exactly (e.g. ['to' => ..., 'subject' => ..., 'body' => ...]) —
     * confirm the real names against the WSDL before use.
     *
     * @throws RuntimeException|SoapFault
     */
    public function sendMail(array $params): array
    {
        return $this->call($this->function, $params);
    }

    /**
     * Verifies the WSDL is reachable and NTLM auth succeeds, without calling
     * any codeunit function (so it never triggers a real email send). Used
     * by the Platform Health "Email Server" check — a plain HTTP GET against
     * an NTLM-protected WSDL would just 401, so this does the real thing.
     */
    public function testConnection(): array
    {
        if ($this->wsdl === '') {
            return ['success' => false, 'message' => 'NAV_MAIL_WSDL is not configured.'];
        }

        if (!class_exists(\SoapClient::class)) {
            return ['success' => false, 'message' => 'The PHP "soap" extension is not enabled on this server.'];
        }

        $wrapperWasRegistered = in_array('http', stream_get_wrappers(), true);
        if ($wrapperWasRegistered) {
            stream_wrapper_unregister('http');
        }
        stream_wrapper_register('http', NTLMStream::class);
        NTLMStream::setCredentials($this->userpwd);

        try {
            new NTLMSoapClient($this->wsdl, [
                'ntlm_userpwd' => $this->userpwd,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]);

            return ['success' => true, 'message' => 'WSDL reachable and NTLM authentication succeeded.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } finally {
            stream_wrapper_restore('http');
        }
    }

    /**
     * Call any function on the configured NAV codeunit over SOAP+NTLM.
     *
     * @throws RuntimeException|SoapFault
     */
    public function call(string $function, array $params): array
    {
        if ($this->wsdl === '') {
            throw new RuntimeException('NAV_MAIL_WSDL is not configured.');
        }

        if (!class_exists(\SoapClient::class)) {
            throw new RuntimeException('The PHP "soap" extension is not enabled on this server — required for NAV integration (see composer.json ext-soap).');
        }

        $wrapperWasRegistered = in_array('http', stream_get_wrappers(), true);
        if ($wrapperWasRegistered) {
            stream_wrapper_unregister('http');
        }
        stream_wrapper_register('http', NTLMStream::class);
        NTLMStream::setCredentials($this->userpwd);

        try {
            $client = new NTLMSoapClient($this->wsdl, [
                'ntlm_userpwd' => $this->userpwd,
                'exceptions' => true,
                'trace' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]);

            Log::info("NAV SOAP call: {$function}", ['params' => $params]);

            $result = $client->__call($function, [$params]);

            return $this->normalizeResult($result);
        } catch (SoapFault $e) {
            Log::error("NAV SOAP call failed: {$function} — " . $e->getMessage());
            throw $e;
        } finally {
            stream_wrapper_restore('http');
        }
    }

    protected function normalizeResult(mixed $result): array
    {
        $returnValue = is_object($result) && property_exists($result, 'return_value')
            ? $result->return_value
            : $result;

        if (is_string($returnValue)) {
            $decoded = json_decode($returnValue, true);
            return (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                ? $decoded
                : ['raw' => $returnValue];
        }

        return (array) $returnValue;
    }
}
