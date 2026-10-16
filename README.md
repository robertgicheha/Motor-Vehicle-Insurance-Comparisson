# LOLC Motor Vehicle Insurance Portal

A Laravel 13 application that lets members of the public price and compare motor
(comprehensive / third-party) insurance quotes across multiple underwriters, and
lets authenticated staff and customers request insurance **stickers** end-to-end:
from quote → cover details → KYC documents → dispatch to the underwriter →
underwriter response → completion.

The system is built for the Kenyan market and is deliberately designed around the
**Data Protection Act (Kenya)**:

- Accounts are identified by **username**, never by email address.
- Personal data is **not** stored on the (offshore) application database where it
  can be avoided.
- KYC / logbook documents are handled with a **zero-storage lifecycle** — they are
  scanned in volatile memory, attached to the underwriter email, and never
  persisted to disk by the application.
- Sensitive signup fields (`branch`, `mobile`) are stored **AES-256-CBC encrypted**.

---

## Table of contents

1. [Technology stack](#technology-stack)
2. [High-level architecture](#high-level-architecture)
3. [Roles & access control](#roles--access-control)
4. [Accounts, authentication & password lifecycle](#accounts-authentication--password-lifecycle)
5. [Quotation lifecycle](#quotation-lifecycle)
6. [Sticker (certificate) lifecycle](#sticker-certificate-lifecycle)
7. [KYC documents & the zero-storage lifecycle](#kyc-documents--the-zero-storage-lifecycle)
8. [DPA consent (Clause 7.4)](#dpa-consent-clause-74)
9. [Claims lifecycle](#claims-lifecycle)
10. [Email dispatch](#email-dispatch)
11. [Data protection & compliance](#data-protection--compliance)
12. [Audit, activity & notifications](#audit-activity--notifications)
13. [Admin & reporting](#admin--reporting)
14. [Where files live (disks)](#where-files-live-disks)
15. [Configuration reference (`.env`)](#configuration-reference-env)
16. [Local setup](#local-setup)
17. [Scheduled tasks & queues](#scheduled-tasks--queues)
18. [Production deployment](#production-deployment)
19. [Testing](#testing)

---

## Technology stack

| Layer | Choice |
|---|---|
| Framework | Laravel 13 (`laravel/framework ^13.8`; PHP `^8.3`, 8.4 in the container) |
| Frontend | Blade templates + vanilla JS + Alpine.js, Tailwind CSS (Vite build) |
| Database | MySQL locally; **SQLite** on the Render deployment (file on a persistent disk) |
| PDF | `barryvdh/laravel-dompdf` (quotation & comparison PDFs) |
| Excel / CSV | `openspout` (users/stickers/report exports) |
| Permissions | `spatie/laravel-permission` |
| Malware scanning | **ESET Server Security for Linux** (`odscan` CLI) |
| Outbound email | Laravel Mail, with a custom **NAV / Business Central SOAP+NTLM** transport and Mailtrap/Postmark/SMTP fallbacks |
| reCAPTCHA | Google reCAPTCHA v3 (optional, disabled when keys are blank) |

Composer requires `ext-soap`, `ext-curl`, `ext-gd`, `ext-zip`, `ext-intl`,
`ext-bcmath`, `ext-exif`.

---

## High-level architecture

```
routes/
  web.php        → loads the others (shared → public → admin → auth)
  shared.php     → authenticated app surface (dashboard, quotations, stickers, claims, profile)
  public.php     → guest surface (home, quote wizard, lookups, public PDFs, public sticker pages)
  admin.php      → /admin/* (role:super-admin|admin)
  auth.php       → login + username/previous-password reset only
  console.php    → artisan closure commands + scheduler

app/Http/Controllers/
  PublicController.php       quote pages, public lookup, guest PDF/email
  QuotationController.php    authenticated quotation CRUD, PDF, compare-all, regenerate
  StickerController.php      full sticker lifecycle (search → cover → KYC → issue → response)
  ClaimsController.php       claim submission, document upload, underwriter email
  DashboardController.php    admin + user dashboards, weather, activity feed
  Admin/*                    users, companies, rates, email settings, media, reports, audit…
  Auth/*                     login, password reset

app/Services/
  PremiumCalculationService  authoritative premium engine (server-side only)
  FileSecurityService        extension / MIME / magic-byte intake validation
  EsetScanService            ESET `odscan` wrapper
  AuditService               audit_logs + activity_logs writer
  Nav/*                      NAV/Business Central SOAP mail transport
  WeatherService             dashboards weather widget
```

**Route load order matters** and is commented in `routes/web.php`: `shared.php`
loads before `public.php` so static `/stickers/*` paths win over the public
`/stickers/{sticker}` wildcard; similarly `/admin/users/trash` and
`/admin/claims/reports` are registered before their resources.

**A premium is never trusted from the client.** Every flow that creates a
quotation recomputes the premium server-side with
`App\Services\PremiumCalculationService`; client-submitted premium/breakdown
values are ignored (`QuotationController::store`, `PublicController::storeQuotationFromSession`).

---

## Roles & access control

Three Spatie roles (`bootstrap/app.php`, `routes/admin.php`):

| Role | Capabilities |
|---|---|
| `super-admin` | Everything, including managing admin/super-admin accounts, trash/restore users. |
| `admin` | Manage plain `user` accounts only; companies/rates, email settings, reports, audit, media, claims/stickers management. |
| `user` | Own quotations, stickers and claims; own profile/help. |

Admins (non-super) can never view, edit, deactivate or delete an
`admin`/`super-admin` account — enforced by `UserController::assertCanManage()`.
Sticker/claim visibility is enforced by owner-or-staff checks in the
controllers.

---

## Accounts, authentication & password lifecycle

- **No public registration, no email verification, no social login.** Accounts
  are provisioned by an admin (`Admin\UserController::store`) or bulk-imported
  from `.xlsx`/`.csv` (`Admin\UserController::import` → `App\Imports\UsersImport`).
- **Usernames are derived from role + branch**, e.g. `superadmin`, `admin`,
  `user1_westlands`, `user4_cbd` (`User::generateUsername()`), so the DB never
  needs an email address to identify a person. Renaming a user's branch or role
  regenerates the username.
- **Email columns were dropped** from `users` (`2026_10_14_000003_...`). Login is
  `username` + `password` only.

### Login (`Auth\AuthenticatedSessionController`, `Auth\LoginRequest`)

1. `authenticate()` looks the user up by `username`.
2. Distinct messages for unknown username vs. wrong password, `is_active` check.
3. **Password expiry is enforced before `Auth::login`** — an account whose
   password is older than `User::PASSWORD_LIFETIME_DAYS` (30) is forced to reset
   instead of logging in.
4. Failures are rate-limited (5 attempts per `username|ip`); a lockout fires a
   `FailedLoginLockout` **notification to every admin** and writes an audit entry.
5. On success the session is regenerated; admin/super-admin logins also run
   `runHealthChecks()` (DB, self-HTTP, disk, NAV WSDL).

### Password expiry

- `User::passwordExpiresAt()` = `password_changed_at + 30 days`.
- `App\Http\Middleware\EnsurePasswordIsFresh` (appended to the `web` group) locks
  every route except `profile.edit/update/password` and `logout` once expired,
  redirecting to the profile page with a notice. Warning shows in the last 7 days.
- Any password change/`reset` calls `User::markPasswordChanged()` which **restarts
  the 30-day clock**.

### Self-service reset (`Auth\PasswordResetController`)

Because there is no email on file, the reset page proves identity with
**username + current password** (used when the password has expired but is still
known). The new password must be **≥ 12 chars with mixed case, a number and a
symbol, and differ from the current one**. On success the username and the new
plaintext password are flashed to a one-time success page with a **copy button**
so the user can save it. Rate-limited (5 attempts per `username|ip`).

A user who has genuinely *forgotten* their password cannot self-serve — an admin
runs `Admin\UserController::resetPassword()`, which generates a strong password,
restarts the clock, and displays it once.

---

## Quotation lifecycle

### Public quote wizard (guests)

Entry points: `home`, `generate.quote`, `motor.quote`, `motor.private`,
`motor.psv`, `quotation.pdf.*`.

1. The visitor chooses a company/mode (single, all, compare) and enters vehicle +
   valuation + passengers. Client-side JS renders premiums from company rates.
2. **`QuotationController::calculate` / `compareAllCalculate`** (and the public
   equivalent) recompute authoritatively via `PremiumCalculationService`.
3. A guest can **download** (`quotation.pdf.download`) or **email**
   (`quotation.compare-all.pdf.email`) the unsaved quotation; PDFs are DomPDF
   renders of `pdf.quotation` / `pdf.compare-all-quotations`.
4. To save a quotation the guest must log in; `PublicController::storeQuotationFromSession`
   persists it to the authenticated user.

### Authenticated quotations (`QuotationController`)

- `create` issues an encrypted, time-boxed `_signed` token and a `QuoteSession`;
  `store` **decrypts and validates that token** (user + expiry) before accepting
  the payload, then recomputes the premium inside a `DB::transaction`.
- `QuotationMail` is sent to the customer only if an address is present on the
  quotation.
- Views: `quotations.index` (role-scoped list + analytics + trend),
  `quotations.show` (recomputed breakdown + rate categories),
  `quotations.mine`, `quotations.recent`, `quotations.tracker`.
- `compare-all` builds **unsaved** quotation models (one per matching insurer)
  for preview/PDF/email — nothing is persisted.
- `regenerate` clones the latest quotation for a registration number (guests are
  bounced to login with the registration stashed in the session).

### Quote numbering

`Quotation::generateNumber()` → company-initials prefix + date segment + random
letters + sequence. Sticker numbers use `Sticker::generateNumber()` →
`STK-<letters><seq>/<company initials>`.

---

## Sticker (certificate) lifecycle

A "sticker" here is the request the portal sends to an insurer/underwriter for an
actual physical/temporary certificate. **The portal does not generate the
certificate PDF itself** — it emails the request (quotation PDF + KYC documents)
to the underwriter, who replies with the real sticker/policy. This is documented
in `CertificateIssuedMail::attachments()`.

Flow (`StickerController`, view `resources/views/stickers/issue.blade.php`):

1. **Find the quotation** — `searchQuotation` searches by quotation number,
   registration, or user name (owner-scoped for non-staff).
2. **Cover details** — client type (`individual` / `corporate`), cover start date
   and duration. `calculateCoverEndDate` computes the inclusive end date
   server-side (`week` = +7·n, `month`/`year` via `Carbon` `NoOverflow`). Saving
   creates/updates the **pending** `Sticker` (`saveCoverDetails`).
3. **Upload KYC documents** — see the next section.
4. **Request the sticker** (`issue`) — validates the required documents and the
   consent checkbox, writes the sticker/consent state in one transaction,
   emails the underwriter, logs the email, and (on success) clears any transient
   file data.
5. **Underwriter response** — an admin records the response
   (`Admin\StickerSettingsController::logResponse`): status, notes, optional
   response document (stored on the `public` disk under `sticker-responses/`).
   Marking it `completed` closes the sticker.
6. **Customer completes** (`myUpdateSticker`) — the customer records the real
   underwriter sticker number they received; this sets `status = completed`.

Required KYC documents by client type:

| Client type | Required |
|---|---|
| Individual | `id_copy`, `kra_pin`, and `logbook` — **or** at least one "other" document when the logbook is marked N/A |
| Corporate | `kra_pin`, `certificate_of_incorporation`, `logbook_import` |

The freeform "Others" panel is always allowed (optional extra material).

Underwriter turnaround is tracked against `STICKER_TURNAROUND_MINUTES` (default
30) via `Sticker::getTurnaroundStatus()` / `isTurnaroundBreached()`.

The printable certificate view (`stickers.print`) is an A4 browser-print HTML
page, which increments `download_count`.

---

## KYC documents & the zero-storage lifecycle

KYC documents (ID copy, KRA PIN, logbook, certificate of incorporation, etc.)
are **Sensitive Personal Data** under the DPA. To keep them off the database and
off the server's persistent disk, the sticker request uses a **zero-storage
lifecycle**:

```
[ Browser RAM ] --(HTTPS stream)--> [ Server RAM ] --(scan)--> [ ESET ]
                                          |
                                   (if safe: keep in RAM)
                                          |
                                   (attach to email)
                                          v
                                 [ Underwriter email ]
                                          |
                                    (server RAM wiped)
```

1. **Browser** holds the selected files only as in-memory `File` objects. Nothing
   is written to `localStorage`/`sessionStorage`. After a successful submit the
   JS clears the file references so the browser can garbage-collect them.
2. **Server** receives the files in the sticker-request request, streams them
   into volatile memory (PHP's temp buffer / volatile `/tmp`), and scans each one
   with ESET (`EsetScanService`, `odscan`). A malicious file aborts the request
   and no email is sent. Nothing is written to the application's storage disks.
3. **Attach & send** — the file bytes are attached to the underwriter email
   directly from memory (`Attachment::fromData`, i.e. Laravel's `attachData`)
   rather than from a stored path.
4. **Wipe** — Laravel is stateless: once the request returns, PHP frees the
   buffer and the OS releases the memory. The upload is gone.

Validation & scanning pipeline (both sticker and claim uploads):

- `FileSecurityService::validate()` — extension allow-list (PDF/JPG/JPEG),
  `finfo` MIME detection, magic-byte checks, PDF signature check, and an explicit
  block on executable/script MIME types, run **before** anything is stored.
- `EsetScanService::scan()` — runs `odscan --scan-path=… --clean-mode=none`
  (exit `0` = clean, `1` = infected, missing binary/timeout = "unavailable").
  Infected files are rejected (HTTP 422) and audited as `kyc_document_rejected`.

The `certificate_documents` table still keeps an **audit row per document**
(type, original filename, MIME, size, scan status/result, uploader, `purged_at`)
— but no file bytes and no usable path, so it is not personal data. Documents are
never reachable via a direct URL.

Claim documents are a separate case: claim attachments are stored on the
`public` disk (`claim-documents/`) because they are part of the insured's claim
record — see [Claims lifecycle](#claims-lifecycle).

---

## DPA consent (Clause 7.4)

Submitting a sticker request requires explicit consent to process logbook
details (Sensitive Personal Data) and share them with the underwriter.

- Wording, clause reference (`DPA Clause 7.4`), privacy-policy URL and a
  **version string** live in `config/consent.php` (`CONSENT_VERSION`, default
  `7.4-v1`).
- The form shows an unchecked-by-default checkbox above the submit button; the
  button stays disabled until it is ticked (UX only). Label text comes from
  config so wording changes are versioned.
- Server side, `issue` validates `'consent' => ['accepted']`; without it the
  request fails validation and **no data is processed and no email is sent**.
- On success a `ConsentLog` row is written **inside the same DB transaction as
  the sticker state change** — if it fails, the transaction rolls back and the
  underwriter email is never dispatched. It stores `sticker_id`, `consent_given`,
  `consent_version`, the consent text snapshot, `clause_reference`, `ip_address`,
  `user_agent` and `consented_at`.

---

## Claims lifecycle

`ClaimsController` + views under `resources/views/claims/`:

1. `create` issues a UUID `upload_token` and stages documents against it.
2. `uploadDocument` validates + ESET-scans and stores the file on the **`public`**
   disk under `claim-documents/` (a `police_abstract` single slot, plus open-ended
   incident photos). Staged rows are linked to the claim on submit.
3. `store` **derives the customer identity from the matched quotation**
   (registration lookup) rather than trusting the client; creates the `Claim`
   (`CLM-…`), links staged documents, notifies admins, and emails the underwriter
   at `company.claims_email ?: company.underwriter_email` via `ClaimLoggedMail`
   (with attachments) — logging to `claim_email_logs`.
4. Status changes notify the owner, the on-behalf user and admins
   (`ClaimStatusChanged`). `publicTracker` lets a guest track by registration.

---

## Email dispatch

All app email goes through Laravel's Mailer, which can be routed through the
NAV / Business Central codeunit (see `app/Services/Nav/README.md`):

- `Mail::extend('nav', …)` registers `NavTransport`; `NavMailService` performs the
  SOAP+NTLM call. If NAV is disabled or throws, `NavTransport` **falls back to
  SMTP** automatically, so a NAV outage never fails a request.
- Today the live mailer is configured to Mailtrap/Postmark/SMTP while the NAV
  codeunit functions are not yet deployed (`NAV_MAIL_ENABLED=false`).

Email templates are **admin-editable** in `email_settings` (subject + body +
letterhead), rendered through `App\Mail\Concerns\UsesEmailTemplate` and
`emails.dynamic`; when no template exists the built-in Blade view is used. Each
sticker send is recorded in `sticker_email_logs` (outbound + inbound), claim
sends in `claim_email_logs`.

Sender/CC resolution:

- Sticker emails: from/name from `EmailSetting` keys
  `sticker_email_from_address` / `sticker_email_from_name`; CC priority
  `company.sticker_notification_emails` → `EmailSetting` CC → `MIC_CC_EMAIL`.
- Claim emails: CC priority `company.cc_claims_email` → shared CC → default.

---

## Data protection & compliance

- **Username-only identity** — no email or `google_id` on `users`; the
  `password_reset_tokens` table was dropped.
- **Encrypted fields** — `users.branch` and `users.mobile` are cast `encrypted`
  (AES-256-CBC); the `2026_10_14` migration re-encrypted existing plaintext.
- **No PII in lists/reports/audit/search** — searches and displays are keyed on
  vehicle identifiers (registration, chassis, engine), quotation/sticker numbers
  and usernames; owner personal details are not shown in logs, audit trails or
  reports.
- **Zero-storage KYC** — see above; no KYC bytes persist on the application disk.
- **HTTPS enforced** — `ForceHttps` middleware + `SESSION_SECURE_COOKIE`; the
  `session`/`web` stack is encrypted (`SESSION_ENCRYPT=true`).
- **Consent audit** — `consent_logs` (versioned, clause-referenced, IP + UA).
- **Audit & activity logs** — `audit_logs` (who/what/old→new) and
  `activity_logs` (human-readable feed), both with an `ip_address`, `user_agent`
  and a `user_name` snapshot so records survive account deletion.
- **Retention** — `logs:prune --days=30` runs daily at 02:30 and trims
  activity/audit logs to a one-month window.

---

## Audit, activity & notifications

- `App\Services\AuditService::log()` writes structured `audit_logs`;
  `::activity()` writes the human-readable `activity_logs`. Both capture actor
  (`user_id` + `user_name`), IP and user-agent.
- Admin audit screens: `admin.audit.index`, `admin.audit.activity` (feed) and
  `admin.audit.story` (per-record timeline).
- Notifications (database channel) cover: new enquiries, failed-login lockouts,
  quotation/sticker/claim status changes, sticker completion reminders, user
  deletion, and change-request events. Users manage them under
  `notifications.*` (read / trash / restore).

---

## Admin & reporting

`/admin/*` (role-gated) exposes: users (CRUD, trash/restore, import/export,
password reset, online status), roles, companies + rates (with bulk import),
compare-all settings, sticker tracker + settings + email settings, claims +
reports, reports (CSV/Excel/PDF), audit (index/activity/story), email settings &
analytics, platform health checks, media library, change requests, quote
sessions (abandoned quotes) and dashboard analytics.

The admin dashboard also surfaces abandoned `quote_sessions` (started > 30 min
ago), health checks, and per-company market share.

---

## Where files live (disks)

`config/filesystems.php`:

| Disk | Root | Used for |
|---|---|---|
| `local` (private) | `storage/app/private` | user imports and other non-public app files |
| `public` | `storage/app/public` | company logos, media library, **claim documents**, underwriter response documents |
| default (`FILESYSTEM_DISK`, `public` on Render) | — | user imports, etc. |

With the zero-storage lifecycle, client KYC uploads are **not written to any
disk**: they live only in the browser and the request's volatile buffer, and
only their audit rows remain in the database.

---

## Configuration reference (`.env`)

Key variables (see `.env.example` for the full annotated list):

```
APP_NAME="LOLC Motor Vehicle Insurance"
APP_ENV=local | production
APP_URL=https://...
APP_KEY=base64:...
FORCE_HTTPS=true
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
DB_CONNECTION=mysql | sqlite
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=postmark | mailtrap | smtp | nav
MAIL_FROM_ADDRESS=...
MIC_CC_EMAIL=...

# Malware scanning
ESET_SCAN_ENABLED=true
ESET_SCANNER_BINARY=/opt/eset/efs/bin/odscan
ESET_SCAN_TIMEOUT=60

# DPA consent (sticker form, Clause 7.4)
CONSENT_VERSION=7.4-v1
PRIVACY_POLICY_URL=/privacy-policy

# Underwriter turnaround SLA (minutes)
STICKER_TURNAROUND_MINUTES=30

# NAV / Business Central mail codeunit (off until deployed)
NAV_MAIL_ENABLED=false
NAV_MAIL_WSDL=...
NAV_NTLM_DOMAIN=... NAV_NTLM_USERNAME=... NAV_NTLM_PASSWORD=...

# Optional reCAPTCHA v3 (blank disables it, rate limiting still applies)
RECAPTCHA_SITE_KEY= RECAPTCHA_SECRET_KEY=
OPENWEATHER_API_KEY=...
```

---

## Local setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# MySQL (default) or SQLite
php artisan migrate --seed

npm run build          # or: npm run dev
php artisan serve
```

Seeders provision a super-admin, admin and sample users
(`database/seeders/AdminUserSeeder.php`) plus companies, rates, email settings
and platform settings.

> ESET scanning gracefully degrades: if `odscan` is not installed (e.g. a local
> dev box), `EsetScanService` returns `unavailable` and the upload passes the
> intake checks instead of being blocked.

---

## Scheduled tasks & queues

`routes/console.php`:

- `health:check` — every 15 minutes.
- `logs:prune --days=30` — daily at 02:30 (activity/audit retention).

Queues use the database driver (`QUEUE_CONNECTION=database`); `ScanDocumentJob`
and mailables are queued-capable. Run `php artisan queue:work` (or a worker) in
production if you enable queued scanning/mail.

---

## Production deployment

The app ships a multi-stage `Dockerfile` (Node asset build → Composer install →
PHP 8.4 CLI runtime with SOAP/PDO drivers) and a `render.yaml` blueprint:

- Persistent disk **mounted at `/var/www/html/storage`** holding uploads **and**
  the SQLite database (`/var/www/html/storage/database.sqlite`).
- `docker/entrypoint.sh` fixes disk ownership, seeds the SQLite DB from the image
  on first boot, generates `APP_KEY` if missing, runs `storage:link` + `migrate`,
  caches config/routes/views, and serves on `$PORT`.
- Health check path: `/up`.
- Fill in the `sync: false` secrets (APP_URL, mail, NAV, reCAPTCHA) on the first
  deploy.

---

## Testing

```bash
php artisan test          # or: vendor/bin/phpunit
```

Feature tests live under `tests/Feature` (auth, password update, profile) and
unit tests under `tests/Unit`. The auth suite mirrors the username-only model
(no registration / email-verification / reset-token flows).
