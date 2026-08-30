<?php

namespace App\Services\Nav;

/**
 * Custom "http://" stream wrapper that fetches over NTLM via cURL.
 *
 * PHP's SoapClient uses the registered "http" stream wrapper to retrieve
 * the WSDL itself before any SOAP call is made. NAV's web services sit
 * behind NTLM/Windows auth, so the default wrapper can't reach the WSDL
 * at all — this wrapper is registered in its place for the duration of
 * a NAV call (see NavMailService::call()).
 *
 * PHP instantiates stream wrappers internally with no constructor
 * arguments, so credentials can't be injected per-instance — they're
 * set once via the static setCredentials() before the wrapper is used.
 */
class NTLMStream
{
    protected static string $userpwd = '';

    private $ch;
    private string $path = '';
    private string $buffer = '';
    private int $pos = 0;

    public static function setCredentials(string $userpwd): void
    {
        self::$userpwd = $userpwd;
    }

    public function stream_open($path, $mode, $options, &$opened_path): bool
    {
        $this->path = $path;
        $this->createBuffer($path);
        return true;
    }

    public function stream_close(): void
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
    }

    public function stream_read(int $count)
    {
        if (strlen($this->buffer) === 0) {
            return false;
        }
        $read = substr($this->buffer, $this->pos, $count);
        $this->pos += $count;
        return $read;
    }

    public function stream_write(string $data): bool
    {
        return strlen($this->buffer) !== 0;
    }

    public function stream_eof(): bool
    {
        return $this->pos > strlen($this->buffer);
    }

    public function stream_tell(): int
    {
        return $this->pos;
    }

    public function stream_flush(): bool
    {
        $this->buffer = '';
        $this->pos = 0;
        return true;
    }

    public function stream_stat()
    {
        $this->createBuffer($this->path);
        return ['size' => strlen($this->buffer)];
    }

    public function url_stat($path, $flags)
    {
        $this->createBuffer($path);
        return ['size' => strlen($this->buffer)];
    }

    private function createBuffer(string $path): void
    {
        if ($this->buffer !== '') {
            return;
        }

        $this->ch = curl_init($path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($this->ch, CURLOPT_USERPWD, self::$userpwd);
        $this->buffer = (string) curl_exec($this->ch);
        $this->pos = 0;
    }
}
