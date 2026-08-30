# NAV/Business Central Email Integration

This app can send all its outgoing email (certificate issuance, quotation
copies, claim notifications, password resets, everything) through a
Microsoft NAV/Business Central codeunit web service instead of SMTP. The
Laravel side is complete and safe to deploy as-is — it currently falls back
to SMTP for every send. **The only remaining work is implementing the
codeunit function below on the NAV side**, then flipping `NAV_MAIL_ENABLED=true`.

## How it's wired

- `App\Services\Nav\NavMailService` is the SOAP+NTLM client (`NTLMSoapClient`
  / `NTLMStream` handle NTLM auth, since PHP's native SOAP transport doesn't
  support it). Its `sendMail(array $params)` calls the single configured
  codeunit function (`NAV_MAIL_FUNCTION`, default `SendMail`).
- `App\Services\Nav\NavTransport` is a custom Symfony Mailer transport
  registered as `Mail::extend('nav', ...)` in `AppServiceProvider::boot()`,
  and set as the `nav` mailer in `config/mail.php`. Once `MAIL_MAILER=nav`
  (the app's current default, see `.env`), **every** `Mail::send()` /
  Mailable / Notification "mail" channel call anywhere in the app is routed
  through it automatically — no controller needs to know NAV exists.
- `NavTransport::doSend()` is the single decision point: if
  `NavMailService::isEnabled()` is false (i.e. `NAV_MAIL_ENABLED` is off, or
  the WSDL isn't configured), or the SOAP call throws for any reason
  (unreachable, auth failure, function not found, timeout), it falls back to
  sending through the app's `smtp` mailer's own Symfony transport instead —
  fetched directly so the fallback can never loop back through `nav` itself.
  **Nothing about the app's current behavior changes until `NAV_MAIL_ENABLED`
  is explicitly turned on**, and even then a NAV outage degrades to SMTP
  rather than failing the request.

## Configuration (`.env`)

```
MAIL_MAILER=nav                 # routes all app mail through NavTransport
NAV_MAIL_ENABLED=false          # flip to true once the function below exists on NAV
NAV_MAIL_WSDL=                  # codeunit WSDL URL
NAV_MAIL_FUNCTION=SendMail
NAV_NTLM_DOMAIN=
NAV_NTLM_USERNAME=
NAV_NTLM_PASSWORD=

# SMTP fallback — used whenever NAV is disabled or unreachable
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
```

## Contract: what this app sends to the function

The configured function receives a single parameter object:

```
{
  "to":        [{ "address": string, "name": string|null }],
  "cc":        [{ "address": string, "name": string|null }],
  "bcc":       [{ "address": string, "name": string|null }],
  "from":      [{ "address": string, "name": string|null }],
  "reply_to":  [{ "address": string, "name": string|null }],
  "subject":   string,
  "body":      string,                  // HTML (falls back to plain text if no HTML part)
  "attachments": [                      // possibly empty array
    {
      "filename":       string,
      "mime_type":      string,
      "content_base64": string          // base64-encoded file bytes
    }
  ]
}
```

This is the same shape for every email the app sends — certificate
issuance, quotation copies, claim notifications, password resets, and
anything else — since it's built generically from the underlying Symfony
`Email` object, not per-flow. NAV doesn't need to know anything about the
content beyond sending `body` as-is and `attachments` as MIME attachments.

## Expected return value

Any shape is accepted — a bare string, or an object. If it's a JSON string,
it's decoded; the recommended shape is:

```json
{ "success": true, "message": "optional info" }
```

A SOAP fault or thrown exception on the NAV side is treated as a failure and
triggers the SMTP fallback automatically — NAV does not need special
"failure" logic beyond raising a normal SOAP fault or returning
`success: false`.

## Testing before NAV is ready

- `HealthCheckController`/Platform Health's "Email Server" card calls
  `NavMailService::testConnection()`, which parses the WSDL and performs the
  NTLM handshake **without invoking any function**, so it's safe to point at
  a real WSDL and check connectivity before the codeunit function exists.
- `ext-soap` must be enabled on whatever server runs this in production —
  it's declared in `composer.json` (`ext-soap`) but many default PHP
  installs ship without it. `NavMailService` raises a clear, actionable
  error (not a silent failure) if it's missing, and `NavTransport` catches
  that and falls back to SMTP rather than letting it fail the request.
