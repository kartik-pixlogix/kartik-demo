<?php

/*
|-------------------------------------------------------------------------------
| The six sectors
|-------------------------------------------------------------------------------
|
| Content for the industry pages. Code-managed for the same reason as
| config/practices.php — BUILD.md scopes the admin to Leads, Insights, Case
| studies and Settings, and these pages are structural.
|
| `lead` and `number` are the prototype's own words and ordering, carried over
| from the homepage industries list verbatim. Everything else describes what the
| sector is inspected against; nothing asserts an unconfirmed fact about UB
| Shield. `practices` cross-links to the service pages that matter most here.
|
*/

return [

    'banking-finance' => [
        'number' => '01',
        'nav' => 'Banking & finance',
        'title' => 'Banking and finance',
        'art' => 'industry-1',
        'lead' => 'RBI and SEBI frameworks, payment data and vendor risk.',
        'intro' => 'Financial services is the sector where security failures become regulatory events fastest. Inspection dates are fixed, the control expectations are written down, and the questions come with a deadline attached — so the work is as much about evidence as it is about defence.',

        'pressures' => [
            ['Fixed inspection dates', 'The RBI cyber security framework and SEBI CSCRF are inspected against, on a schedule you do not set.'],
            ['Payment data in scope', 'Card data pulls PCI DSS in alongside everything else, usually across more systems than anyone expects.'],
            ['Third parties and outsourcing', 'Your regulator holds you responsible for your vendors, which means assessing them rather than trusting them.'],
            ['Incident reporting windows', 'Reportable incidents have to be filed quickly and completely, which is a process problem long before it is a technical one.'],
        ],

        'regulations' => ['RBI Cyber Security Framework', 'SEBI CSCRF', 'CERT-In', 'PCI DSS', 'ISO/IEC 27001', 'DPDPA 2023'],
        'practices' => ['governance-risk-compliance', 'offensive-security', 'managed-security-services'],
        'case' => 'bfsi-rbi-cyber-framework',
    ],

    'healthcare' => [
        'number' => '02',
        'nav' => 'Healthcare',
        'title' => 'Healthcare',
        'art' => 'industry-2',
        'lead' => 'Patient data under DPDPA, and HIPAA for US work.',
        'intro' => 'Health data is the most sensitive category most organisations will ever hold, and it moves constantly — between clinicians, labs, insurers and systems nobody has reviewed in years. The work starts with knowing where it actually goes.',

        'pressures' => [
            ['Sensitive data, everywhere', 'Patient records spread across clinical systems, imaging, billing and email far beyond where anyone assumes they live.'],
            ['Legacy and medical devices', 'Equipment that cannot be patched on your schedule, and sometimes cannot be patched at all, has to be isolated instead.'],
            ['Consent and retention', 'DPDPA expects a lawful basis, a notice and a retention limit for data that clinicians want to keep indefinitely.'],
            ['Cross-border work', 'Serving US providers or insurers brings HIPAA obligations alongside Indian law, not instead of it.'],
        ],

        'regulations' => ['DPDPA 2023', 'HIPAA', 'ISO/IEC 27001', 'CERT-In'],
        'practices' => ['data-privacy', 'infrastructure-cloud', 'managed-security-services'],
        'case' => 'saas-soc-2-readiness',
    ],

    'saas-it' => [
        'number' => '03',
        'nav' => 'SaaS & IT',
        'title' => 'SaaS and IT',
        'art' => 'industry-3',
        'lead' => 'SOC 2 and ISO 27001 so security reviews stop stalling deals.',
        'intro' => 'For a software business, security is a sales problem before it is a risk problem. The questionnaire arrives, procurement blocks, and the deal sits still until someone can answer with evidence. Certification turns that from a scramble into a document you already have.',

        'pressures' => [
            ['Security questionnaires', 'Enterprise buyers ask the same hundred questions, and answering them from memory does not survive the follow-up.'],
            ['Multi-tenant isolation', 'The boundary between one customer and the next is the finding that matters most, and the hardest to test from outside.'],
            ['Ship speed versus control', 'Controls that fight your release process get routed around, so they have to fit the pipeline you already run.'],
            ['Sub-processors', 'Your customers inherit your vendors, and increasingly ask you to prove you have assessed them.'],
        ],

        'regulations' => ['SOC 2', 'ISO/IEC 27001', 'GDPR', 'DPDPA 2023'],
        'practices' => ['governance-risk-compliance', 'offensive-security', 'infrastructure-cloud'],
        'case' => 'saas-soc-2-readiness',
    ],

    'manufacturing' => [
        'number' => '04',
        'nav' => 'Manufacturing',
        'title' => 'Manufacturing',
        'art' => 'industry-4',
        'lead' => 'Plant and office networks kept apart, supplier access controlled.',
        'intro' => 'On a plant network, availability outranks everything — which is exactly why the usual advice does not transfer. The work is segmentation, access and monitoring that can be done without stopping a line.',

        'pressures' => [
            ['IT and OT convergence', 'Plant systems reachable from the office network, usually through a connection nobody documented.'],
            ['Uptime over patching', 'Production equipment that cannot be taken down for a maintenance window needs compensating controls instead.'],
            ['Supplier and contractor access', 'Remote access granted to vendors for support, and rarely reviewed after the support ended.'],
            ['Ransomware economics', 'A stopped line costs money by the hour, which is precisely why the sector is targeted.'],
        ],

        'regulations' => ['IEC 62443', 'ISO/IEC 27001', 'NIST CSF', 'CERT-In'],
        'practices' => ['infrastructure-cloud', 'managed-security-services', 'offensive-security'],
        'case' => 'bfsi-rbi-cyber-framework',
    ],

    'education' => [
        'number' => '05',
        'nav' => 'Education',
        'title' => 'Education',
        'art' => 'industry-5',
        'lead' => 'Student and parent data, consent records and campus networks.',
        'intro' => 'Education holds children’s data on open networks with a user base that changes every year. DPDPA treats data about children with particular care, which makes consent and retention the first problem rather than an afterthought.',

        'pressures' => [
            ['Children’s data', 'DPDPA sets stricter expectations for processing data about children, including verifiable parental consent.'],
            ['Open campus networks', 'Networks designed for access, used by thousands of unmanaged personal devices.'],
            ['A roll that turns over', 'Accounts created and abandoned every year, and the stale access that accumulates behind them.'],
            ['Third-party platforms', 'Learning and assessment tools that hold student data under contracts nobody has read recently.'],
        ],

        'regulations' => ['DPDPA 2023', 'ISO/IEC 27001', 'CERT-In'],
        'practices' => ['data-privacy', 'infrastructure-cloud', 'governance-risk-compliance'],
        'case' => 'saas-soc-2-readiness',
    ],

    'e-commerce' => [
        'number' => '06',
        'nav' => 'E-commerce',
        'title' => 'E-commerce',
        'art' => 'industry-6',
        'lead' => 'PCI DSS, checkout integrity and account-takeover defence.',
        'intro' => 'Retail fraud rarely looks like an intrusion. It looks like logins that succeed, orders that complete and refunds that process — which is why detection matters as much as prevention, and why the checkout deserves testing in its own right.',

        'pressures' => [
            ['Card data and PCI DSS', 'Scope follows the card data, and reducing that scope is usually cheaper than securing it.'],
            ['Account takeover', 'Credential stuffing against a large consumer base, where the successful login is indistinguishable from a real one.'],
            ['Checkout and business logic', 'Price, quantity and discount manipulation that no scanner is going to find for you.'],
            ['Peak-season exposure', 'Sale events concentrate both revenue and attack traffic into the window you can least afford an incident.'],
        ],

        'regulations' => ['PCI DSS', 'DPDPA 2023', 'ISO/IEC 27001', 'CERT-In'],
        'practices' => ['offensive-security', 'managed-security-services', 'data-privacy'],
        'case' => 'saas-soc-2-readiness',
    ],

];
