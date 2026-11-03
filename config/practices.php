<?php

/*
|-------------------------------------------------------------------------------
| The five practices
|-------------------------------------------------------------------------------
|
| Content for the service pages. Code-managed rather than a Filament resource:
| BUILD.md puts Leads, Insights, Case studies and Settings in the admin, and
| these pages are structural — their shape is part of the design. Say the word
| and they can move into the admin.
|
| ON THE COPY. Everything here describes what the practice involves, in the
| voice the prototype already set ("We test. Then we fix."). Nothing asserts a
| fact about UB Shield that has not been confirmed — any specific claim
| (durations, SLAs, team certifications, tooling) is left in square brackets
| for the client, exactly like the prototype's own [XX] figures.
| See docs/placeholders.md.
|
*/

return [

    'offensive-security' => [
        'kicker' => 'Secure',
        'nav' => 'Offensive Security / VAPT',
        'title' => 'Offensive security',
        'art' => 'service-vapt',
        'art_inverted' => false,
        'lead' => 'We attack your apps and networks the way a real adversary would. Then we hand your developers fixes they can ship.',
        'intro' => 'A scanner tells you what is theoretically wrong. A tester tells you what someone can actually do with it, and in what order. Every finding we raise is reproduced by hand, rated by what it would cost you, and retested once you have fixed it.',

        'included' => [
            ['Web, mobile and API testing', 'Authenticated and unauthenticated, against the business logic as well as the usual injection and access-control classes.'],
            ['Network and cloud testing', 'External perimeter, internal segments, and the cloud control plane that increasingly matters more than either.'],
            ['Thick-client testing', 'Desktop and installed applications, including local storage, inter-process communication and the backend they talk to.'],
            ['Source code review', 'Reading the code alongside the testing, so we find the classes of bug a black-box run would miss.'],
            ['Red teaming', 'A goal-driven exercise against people, process and technology together, when you want to test detection rather than inventory.'],
        ],

        'deliverables' => [
            ['Executive summary', 'What matters, in a page, for people who will not read the technical body.'],
            ['Technical findings', 'Each with reproduction steps, evidence and a severity your team can argue with.'],
            ['Remediation guidance', 'Written for the developer who has to fix it, not for the auditor who has to file it.'],
            ['Retest and closure letter', 'We retest what you fix and state plainly what is closed.'],
        ],

        'frameworks' => ['OWASP ASVS', 'OWASP MASVS', 'PTES', 'NIST SP 800-115', 'CERT-In', 'PCI DSS'],

        'process' => [
            ['Assess', 'Scope the estate, agree rules of engagement, and test.'],
            ['Remediate', 'Work alongside your engineers on the fixes that matter first.'],
            ['Monitor', 'Feed what we found into detection, so the next attempt is seen.'],
            ['Certify', 'Hand the auditor evidence that the finding is closed, not just reported.'],
        ],

        'case' => 'bfsi-rbi-cyber-framework',

        'faq' => [
            ['How long does a test take?', 'It depends on scope — the number of applications, whether testing is authenticated, and how much of the estate is in play. We size it from a short scoping call and give you a fixed window before you commit. [Typical durations to confirm.]'],
            ['Will testing take our systems down?', 'Not by default. Destructive and denial-of-service testing is excluded unless you ask for it in writing, and we agree the rules of engagement, the testing window and an escalation contact before anything starts.'],
            ['Do you test production or staging?', 'Either. Staging is safer but only tells you about staging, so where the two environments differ we say so in the report rather than letting the difference go unrecorded.'],
            ['Is a retest included?', 'Yes. We retest every finding we raise once you tell us it is fixed, and we file the result as audit evidence. A report nobody acts on is not security.'],
            ['Can you work to our auditor’s template?', 'Yes. If your auditor, regulator or customer needs the findings in a particular format, tell us at scoping and we will produce it alongside our own report.'],
        ],
    ],

    'data-privacy' => [
        'kicker' => 'Privacy',
        'nav' => 'Data Privacy',
        'title' => 'Data privacy',
        'art' => 'service-privacy',
        'art_inverted' => false,
        'lead' => 'We map where personal data lives and who touches it. Then we build the notices and consent flows the DPDP Act expects.',
        'intro' => 'Most privacy programmes fail on the first question an auditor asks: where is the data? We start there — with the systems, the vendors and the people who touch personal data — and work outward to notices, consent and the rights process that has to run every day afterwards.',

        'included' => [
            ['Data discovery and mapping', 'Where personal data enters, where it rests, who can reach it and when it leaves.'],
            ['DPIA and risk assessment', 'Assessments for the processing that warrants one, written so they survive a regulator reading them.'],
            ['Notices and consent flows', 'Plain-language notices and a consent mechanism that records what was agreed and when.'],
            ['Data principal rights', 'A working process for access, correction and erasure requests, with timelines your team can meet.'],
            ['Vendor and transfer review', 'The processors you share data with, the contracts behind them and where it crosses a border.'],
            ['DPO as a service', 'A named privacy lead on retainer when you need the role filled without the headcount.'],
        ],

        'deliverables' => [
            ['Data inventory', 'A living record of processing, not a one-off spreadsheet.'],
            ['Gap assessment', 'Against DPDPA, and GDPR where you sell into Europe.'],
            ['Notice and consent pack', 'Drafted text and the flows to put it in.'],
            ['Remediation roadmap', 'Sequenced by risk and by what your engineering team can absorb.'],
        ],

        'frameworks' => ['DPDPA 2023', 'GDPR', 'ISO/IEC 27701', 'HIPAA'],

        'process' => [
            ['Assess', 'Map the processing and measure it against the law that applies to you.'],
            ['Remediate', 'Close the gaps, starting with the ones a regulator would ask about first.'],
            ['Monitor', 'Keep the inventory current as systems and vendors change.'],
            ['Certify', 'Produce the evidence pack when a customer or regulator asks.'],
        ],

        'case' => 'saas-soc-2-readiness',

        'faq' => [
            ['Does the DPDP Act apply to us?', 'If you process the personal data of people in India, almost certainly — including when you collect it outside India to offer goods or services here. The scoping call establishes which obligations land on you and which do not.'],
            ['When do the Rules take effect?', 'The Act is passed and the Rules govern the operational detail. Rather than print a date that may move, we track it and tell clients directly. [Current status to confirm with the client.]'],
            ['Do we need a Data Protection Officer?', 'Significant Data Fiduciaries must appoint one based in India. Whether you are classed as one depends on volume, sensitivity and risk — we assess that rather than guess at it.'],
            ['We already did GDPR. Is that enough?', 'It is a strong head start and much of the mapping carries over, but DPDPA differs on consent, notice and the rights process. We reuse what transfers and tell you plainly what does not.'],
            ['Can you work with our existing counsel?', 'Yes, and usually we should. We handle the technical and operational side of privacy; your lawyers own the legal position.'],
        ],
    ],

    'governance-risk-compliance' => [
        'kicker' => 'Compliance',
        'nav' => 'Governance, Risk & Compliance',
        'title' => 'Governance, risk and compliance',
        'art' => 'service-grc',
        'art_inverted' => true,
        'lead' => 'We keep policies, controls and evidence in order all year. Audit day becomes a review, not a scramble.',
        'intro' => 'Certification is not the hard part. Staying certified is — because evidence goes stale, owners change and nobody notices until six weeks before the audit. We map each control once, point several frameworks at the same evidence, and keep it current between audits.',

        'included' => [
            ['Gap assessment', 'Where you stand against the framework before anyone commits to a date.'],
            ['Policy and control design', 'Written for how your organisation actually works, so people follow them.'],
            ['Risk register', 'Maintained, owned and reviewed, rather than written once for the auditor.'],
            ['Evidence management', 'One library, mapped across every framework that asks for the same thing.'],
            ['Internal audit', 'A dry run against the real criteria, early enough to fix what it finds.'],
            ['Audit support', 'We sit in the room with your auditor and answer the technical questions.'],
        ],

        'deliverables' => [
            ['Control matrix', 'Each control mapped once and reused across frameworks.'],
            ['Policy set', 'Approved, versioned and owned by a named person.'],
            ['Evidence library', 'Organised the way an auditor reads it.'],
            ['Readiness report', 'What is in place, what is missing and the order to close it in.'],
        ],

        'frameworks' => ['ISO/IEC 27001', 'SOC 2', 'RBI Cyber Security Framework', 'SEBI CSCRF', 'PCI DSS', 'HIPAA'],

        'process' => [
            ['Assess', 'Measure the estate against the framework and record a baseline.'],
            ['Remediate', 'Close control gaps alongside the teams who own them.'],
            ['Monitor', 'Keep evidence current so it never has to be rebuilt.'],
            ['Certify', 'Hand the auditor a pack mapped control by control.'],
        ],

        'case' => 'bfsi-rbi-cyber-framework',

        'faq' => [
            ['How long does ISO 27001 take?', 'It depends on how much is already in place and how quickly your teams can absorb change. The gap assessment gives you a realistic date before you announce one. [Typical timelines to confirm.]'],
            ['Can you be our auditor as well?', 'No. The organisation that builds your controls cannot independently certify them, and an auditor who offered both would be a reason to worry. We prepare you and support you through someone else’s audit.'],
            ['We need ISO 27001 and SOC 2. Twice the work?', 'No. The controls overlap heavily, so we map each one once and point both frameworks at the same evidence. The second certification costs far less than the first.'],
            ['What happens after we certify?', 'Surveillance audits and the day-to-day of keeping evidence current. That is what the Monitor phase is for — most of the pain in year two comes from treating year one as a project that ended.'],
            ['Who needs to be involved from our side?', 'A sponsor who can make decisions, an owner for each control area, and whatever time engineering needs for the fixes. We keep that demand as small as the framework allows.'],
        ],
    ],

    'infrastructure-cloud' => [
        'kicker' => 'Infrastructure',
        'nav' => 'Infrastructure & Cloud',
        'title' => 'Infrastructure and cloud',
        'art' => 'service-infra',
        'art_inverted' => false,
        'lead' => 'We review architecture and configuration across AWS, Azure and GCP. Then we harden it so your team can keep it that way.',
        'intro' => 'Cloud breaches are rarely clever. They are a public bucket, a key that never expired, a role with far more permission than its job needs. We review the architecture and the configuration together, fix what is wrong, and leave the guardrails that stop it drifting back.',

        'included' => [
            ['Architecture review', 'Segmentation, trust boundaries and blast radius, assessed before the configuration detail.'],
            ['Cloud configuration review', 'AWS, Azure and GCP against the provider benchmarks and against how you actually use them.'],
            ['Identity and access', 'Roles, keys and privilege, with the standing access nobody remembers granting.'],
            ['Hardening', 'Server, container and endpoint baselines your team can apply and keep applying.'],
            ['Network and firewall review', 'Rule bases read in full, including the rules that stopped making sense years ago.'],
            ['Guardrails as code', 'Policy and detection so a fixed misconfiguration stays fixed.'],
        ],

        'deliverables' => [
            ['Architecture findings', 'With diagrams of what is exposed and to whom.'],
            ['Configuration report', 'Per-account, per-service, prioritised by exploitability.'],
            ['Hardening baselines', 'Applied, and documented so they can be reapplied.'],
            ['Drift controls', 'The policy or alerting that catches the next regression.'],
        ],

        'frameworks' => ['CIS Benchmarks', 'ISO/IEC 27001', 'SOC 2', 'NIST CSF', 'PCI DSS'],

        'process' => [
            ['Assess', 'Review the architecture and read the live configuration.'],
            ['Remediate', 'Harden it with your platform team, not around them.'],
            ['Monitor', 'Watch for drift and for the misconfiguration that returns.'],
            ['Certify', 'Evidence the control state for whichever framework asks.'],
        ],

        'case' => 'saas-soc-2-readiness',

        'faq' => [
            ['Do you need production access?', 'Read-only access to the control plane is usually enough for a configuration review, scoped to named accounts and time-limited. Anything beyond that we ask for explicitly and justify.'],
            ['We use more than one cloud. Is that a problem?', 'No, and it is common. We review each provider against its own benchmarks, then look at the seams between them — which is where the interesting findings usually are.'],
            ['Can you fix things, or only report them?', 'Both. We work alongside your platform team on the remediation, and where it makes sense we leave the change as code so it is reviewable and repeatable.'],
            ['How is this different from a cloud provider’s own tool?', 'Those tools are good at flagging known misconfigurations and have no opinion about your architecture. We use them, then do the part they cannot: judging what the finding means for your business.'],
            ['Will hardening break our applications?', 'It can, which is why baselines are applied in stages with your team and tested as they go rather than pushed estate-wide in one pass.'],
        ],
    ],

    'managed-security-services' => [
        'kicker' => 'MSS',
        'nav' => 'Managed Security Services',
        'title' => 'Managed security services',
        'art' => 'service-mss',
        'art_inverted' => true,
        'lead' => 'Our 24/7 SOC watches, triages and responds. A named lead knows your estate.',
        'intro' => 'Alerts are easy to generate and expensive to read. Our analysts work your alerts in shifts from Ahmedabad, every hour of the year — and because the same named lead stays with your account, the tenth alert is judged with the context of the first nine.',

        'included' => [
            ['SIEM monitoring', 'Cloud, endpoint, identity and firewall telemetry in one place, tuned to your estate.'],
            ['Detection engineering', 'Detections written and tuned for how you run, not shipped as defaults and left.'],
            ['Incident response', 'Triage, containment agreed in advance, and a hand on the problem out of hours.'],
            ['Vulnerability management', 'Continuous scanning with the triage that makes the output usable.'],
            ['CERT-In reporting', 'Reportable incidents prepared and filed inside the window. [Six-hour window to confirm.]'],
            ['Virtual CISO', 'Senior security leadership on retainer, for the board papers as much as the architecture.'],
        ],

        'deliverables' => [
            ['Monthly posture report', 'In plain language, for people who do not work in security.'],
            ['Incident records', 'What happened, what we did and what changed afterwards.'],
            ['Detection coverage map', 'What we would see, and candidly what we would not.'],
            ['Named lead and review', 'A person who knows your estate, and a standing review with them.'],
        ],

        'frameworks' => ['CERT-In', 'MITRE ATT&CK', 'ISO/IEC 27001', 'SOC 2', 'RBI Cyber Security Framework', 'SEBI CSCRF'],

        'process' => [
            ['Assess', 'Establish what you have, what it logs and what we can see today.'],
            ['Remediate', 'Close the gaps in coverage before promising to watch them.'],
            ['Monitor', 'Round-the-clock triage and response against agreed SLAs.'],
            ['Certify', 'Evidence the monitoring control for every framework that asks.'],
        ],

        'case' => 'bfsi-rbi-cyber-framework',

        'faq' => [
            ['What are your response times?', 'They are set per severity and written into the service agreement, so they are a commitment rather than an aspiration. [Target times to confirm with the client.]'],
            ['Do you replace our internal team?', 'Usually we extend it. Most clients keep ownership in-house and use the SOC for the hours and the depth of alert triage that are hard to staff.'],
            ['Can you contain an incident yourselves?', 'Only as far as you authorise in advance. Containment actions — isolating a host, disabling an account — are agreed in writing at onboarding, so nobody is improvising at 3am.'],
            ['What do you need from us to start?', 'Log sources and a named contact. Onboarding establishes what you have, connects it and tunes the noise down before monitoring formally begins.'],
            ['Where are your analysts based?', 'Ahmedabad, in shifts, every hour of the year. Your data stays where your contract says it stays.'],
        ],
    ],

];
