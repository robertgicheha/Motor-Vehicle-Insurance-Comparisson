<?php

namespace App\Services\Nav;

use Illuminate\Support\Facades\Log;
use SoapClient;

/**
 * SoapClient that authenticates each request with NTLM via cURL, since
 * PHP's native SOAP transport has no NTLM support.
 */
class NTLMSoapClient extends SoapClient
{
    protected string $userpwd;

    /**
     * @param string $wsdl    Codeunit WSDL URL.
     * @param array  $options Standard SoapClient options, plus 'ntlm_userpwd'
     *                        in the form "DOMAIN\username:password".
     */
    public function __construct(string $wsdl, array $options = [])
    {
        $this->userpwd = $options['ntlm_userpwd'] ?? '';
        unset($options['ntlm_userpwd']);

        parent::__construct($wsdl, $options);
    }

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
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
        ];

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);

        $response = curl_exec($ch);

        if ($response === false) {
            Log::error('NAV NTLMSoapClient cURL error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $response;
    }
}
