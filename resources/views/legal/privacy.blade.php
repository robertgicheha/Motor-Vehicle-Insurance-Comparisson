@extends('layouts.public-plain')

@section('title', 'Privacy Policy')
@section('description', 'Privacy Policy for the MIC Global Risks motor insurance portal.')

@php
    $heroEyebrow = 'Legal';
    $heroTitle = 'Privacy Policy';
    $heroSubtitle = 'How MIC Global Risks collects, uses, and protects your data on this portal.';
@endphp

@section('content')
<main class="max-w-[900px] mx-auto w-full px-6 sm:px-10 py-12 pb-20 font-montserrat text-[#1a1a2e]">
    <p class="text-xs text-gray-500 mb-10">Last updated: {{ now()->format('d F Y') }}</p>

    <div class="space-y-8 text-[13.5px] leading-relaxed text-gray-700">

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">1. Introduction</h2>
            <p>This motor insurance portal is built and operated by <strong>MIC Global Risks Limited</strong> on behalf of <strong>LOLC Bank</strong>  for use by LOLC's authorised staff and agents to generate motor insurance quotations, compare quotations across underwriters, report claims, and request insurance stickers on behalf of customers. This Privacy Policy explains what personal data we collect through the Portal, why we collect it, how it is protected, and the rights available to individuals whose data we process.</p>
            <p class="mt-2">We process personal data in accordance with the <strong>Kenya Data Protection Act, 2019</strong> and its subsidiary regulations.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">2. Information We Collect</h2>
            <p>Depending on how the Portal is used, we collect:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li><strong>User account data</strong> — name, email address, mobile number, and role, for staff/agents who log in to the Portal.</li>
                <li><strong>Customer &amp; vehicle data</strong> — the insured's name, contact details, vehicle registration, make, model, year, chassis number, engine number, colour, and estimated value, submitted to generate a quotation, log a claim, or request a sticker.</li>
                <li><strong>KYC documents</strong> — identity documents (e.g. national ID, passport), KRA PIN certificates, vehicle logbooks, certificates of incorporation, and any other supporting documents uploaded to request a sticker on a customer's behalf.</li>
                <li><strong>Claim details</strong> — incident date, description, claim type, police abstract reference (where applicable), and any documents submitted in support of a claim.</li>
                <li><strong>Technical data</strong> — IP address, device/browser information, and activity logs, collected automatically for security and audit purposes.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">3. How We Use This Information</h2>
            <p>We use the information collected to: generate and compare insurance quotations; transmit sticker requests and supporting KYC documents to the relevant underwriter for their review and approval; transmit claim details to the relevant underwriter for assessment; maintain an audit trail of quotations, sticker requests, and claims for accountability and dispute-resolution purposes; and secure and administer the Portal itself (e.g. detecting fraud, misuse, or security incidents).</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">4. Legal Basis for Processing</h2>
            <p>We process personal data on the basis of: performance of a contract (providing quotation, claims, and sticker-issuance services on LOLC's instruction); legal obligation (e.g. KYC and anti-fraud requirements applicable to insurance intermediation); and legitimate interest (maintaining the security, integrity, and audit trail of the Portal). Where required, consent is obtained from the customer by the LOLC staff member or agent submitting their information on their behalf.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">5. Who We Share Data With</h2>
            <p>Personal data and KYC documents submitted on the Portal are shared only with:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>The <strong>participating insurance underwriter</strong> relevant to a specific quotation, sticker request, or claim — this is necessary for them to price, approve, and issue the requested cover, sticker, or claim decision.</li>
                <li><strong>LOLC</strong>, as the Portal is operated on their behalf and for their staff's use.</li>
                <li>Service providers who support the underlying infrastructure of the Portal (e.g. secure hosting and email delivery), strictly to the extent necessary to operate the service, and under obligations of confidentiality.</li>
            </ul>
            <p class="mt-2">We do not sell personal data, and we do not share it with any third party for marketing purposes.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">6. How We Protect KYC Documents</h2>
            <p>KYC documents are sensitive personal data and are handled with particular care:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Documents are transmitted between your device and our servers, and from our servers to the underwriter, over encrypted (HTTPS/TLS) connections.</li>
                <li>Every uploaded document is automatically scanned for malware before it is accepted.</li>
                <li>Uploaded documents are held in restricted, non-public storage — not in our application database — and access is limited to the systems and staff who need it to process a specific sticker request.</li>
                <li>Documents are held only for as long as it takes to assemble and send your sticker request to the underwriter; once that email has been sent successfully, the underlying files are deleted immediately. We do not retain KYC documents on the system.</li>
                <li>We do not store KYC document files in our database; the database only records limited metadata (such as the document type and verification status) needed to track a request.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">7. Data Retention</h2>
            <p>We retain quotation, claim, and sticker-request records for as long as reasonably necessary to fulfil the purposes described in this Policy, to meet regulatory and record-keeping obligations applicable to insurance intermediation, and to resolve any disputes. KYC documents are not retained on the system.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">8. Your Rights</h2>
            <p>Under the Kenya Data Protection Act, 2019, a data subject has the right to: be informed of the use of their personal data; access their personal data; request correction of inaccurate data; request deletion of data no longer necessary for the purpose collected; object to or restrict certain processing; and lodge a complaint with the Office of the Data Protection Commissioner, Kenya. Requests relating to a customer's data should be directed through the LOLC branch or agent who assisted with the quotation, claim, or sticker request, or directly to us using the contact details below.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">9. Security Measures</h2>
            <p>We apply technical and organisational measures appropriate to the sensitivity of the data we handle, including encrypted data transmission, malware scanning of uploaded files, restricted and audited access to stored documents, and role-based access controls limiting Portal features to authorised users. No system can guarantee absolute security, but we continually review and improve these measures.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">10. Security Incident Notification</h2>
            <p>If we become aware of a security incident data breach affecting data processed through the Portal, we will: investigate the matter as a priority using the Portal's audit and activity logs; notify LOLC's designated contact in writing without undue delay, and in any event within 72 hours of becoming aware of it, describing the nature of the incident and the data or systems affected; and support LOLC in meeting any notification obligations it may have to the Office of the Data Protection Commissioner, Kenya, or to affected data subjects, under the Kenya Data Protection Act, 2019.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">11. Cookies</h2>
            <p>The Portal uses strictly necessary cookies/session storage to keep you signed in and to remember your in-progress quotation while you complete a form. We do not use these cookies for advertising or cross-site tracking.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">12. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time to reflect changes in our practices or in applicable law. The "Last updated" date above indicates when this Policy was last revised.</p>
        </section>

        <section>
            <h2 class="text-base font-bold text-[#1a1a2e] mb-2">13. Contact Us</h2>
            <p>For questions about this Privacy Policy, or to exercise your data protection rights, please contact MIC Global Risks Limited, 6th Floor, 9 West, Ring Road Parklands, Nairobi, Kenya, or via <a href="https://www.micglobalrisks.com" class="text-[#0066B3] font-semibold hover:underline" target="_blank" rel="noopener">www.micglobalrisks.com</a>.</p>
        </section>

    </div>
</main>
@endsection
