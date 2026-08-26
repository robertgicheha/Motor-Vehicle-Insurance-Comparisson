<?php

require_once __DIR__ . '/NTLMUserID.php';

class NTLMSoapClient extends SoapClient
{
    /**
     * Stores headers of the last SOAP request.
     *
     * @var string[]
     */
    protected array $__last_request_headers = [];

    /**
     * Override SoapClient::__doRequest with proper PHP 8.1+ signature.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param bool   $one_way
     *
     * @return string|null
     */
    public function __doRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        bool $one_way = false
    ): ?string {
        $headers = [
            'Method: POST',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'User-Agent: Mozilla/1.0N (Windows)',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
        ];

        $this->__last_request_headers = $headers;

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, USERPWD);

        $response = curl_exec($ch);

        // curl_exec returns string|false; parent expects ?string (string|null)
        if ($response === false) {
            // Optional: log curl_error($ch) here if you have a logger
            // error_log('NTLMSoapClient curl error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $response;
    }

    /**
     * Override SoapClient::__getLastRequestHeaders with proper PHP 8.1+ signature.
     *
     * @return string|null
     */
    public function __getLastRequestHeaders(): ?string
    {
        if (empty($this->__last_request_headers)) {
            return null;
        }

        return implode("\n", $this->__last_request_headers) . "\n";
    }
}
