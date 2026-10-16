<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Consent (DPA Clause 7.4)
    |--------------------------------------------------------------------------
    |
    | Sensitive Personal Data (logbook details) collected on the sticker
    | request form requires an explicit consent confirmation before the
    | request is processed and shared with the underwriter. The wording is
    | kept here, in one place, with a version string so changes are
    | auditable. Update `version` whenever the wording changes.
    |
    */

    'version' => env('CONSENT_VERSION', '7.4-v1'),

    'clause' => 'DPA Clause 7.4',

    'privacy_policy_url' => env('PRIVACY_POLICY_URL', '/privacy-policy'),

    'sticker_text' => 'I consent to MIC Global Risks collecting and processing my logbook details, '
        . 'which constitute Sensitive Personal Data, for the purpose of processing this sticker '
        . 'request and sharing them with the underwriter, in accordance with the Data Protection Policy.',

];
