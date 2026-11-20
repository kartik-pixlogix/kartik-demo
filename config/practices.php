<?php

/*
|-------------------------------------------------------------------------------
| The six approved services
|-------------------------------------------------------------------------------
|
| Source: "UB Shield — Final Website Content & Developer Handover Brief"
| (launch 23 November), §3 "Official six-service catalogue". Names, categories
| and descriptions are the brief's, verbatim. The `included` and `deliverables`
| lists only split the brief's own sentences into rows — nothing is added.
| The slugs are the brief's §7 suggested URLs.
|
| Keys:
|   kicker       the brief's "Category"
|   nav          the service name, used in menus, cards, titles
|   lead         the brief's description
|   included     scope items named in the description
|   deliverables outputs named in the description (omitted where none are)
|   note         an authorization/scope line required by §10 of the brief
|
| The old five practices are redirected in routes/web.php ($legacyServices).
|
*/

return [

    'network-design-architecture' => [
        'kicker' => 'Network Solutions',
        'nav' => 'Enterprise Network Design & Architecture',
        'short' => 'Network Design',
        'art' => 'service-infra',
        'art_inverted' => false,
        'lead' => 'Design and implement secure, scalable network infrastructures for offices, data centers, and multi-branch organizations.',
        'included' => [
            'Network topology',
            'IP planning',
            'VLAN segmentation',
            'Routing',
            'Switching',
            'Redundancy',
            'VPN connectivity',
            'Network documentation',
        ],
        'deliverables' => [
            'Network documentation',
        ],
    ],

    'vulnerability-assessment-penetration-testing' => [
        'kicker' => 'Offensive Security',
        'nav' => 'Vulnerability Assessment & Penetration Testing (VAPT)',
        'short' => 'VAPT',
        'art' => 'service-vapt',
        'art_inverted' => false,
        'lead' => 'Identify and validate security weaknesses in networks, servers, web applications, APIs, and other authorized systems. Provide risk ratings, evidence, remediation recommendations, and retesting to verify fixes.',
        'included' => [
            'Networks',
            'Servers',
            'Web applications',
            'APIs',
            'Other authorized systems',
        ],
        'deliverables' => [
            'Risk ratings',
            'Evidence',
            'Remediation recommendations',
            'Retesting to verify fixes',
        ],
        'note' => 'VAPT and penetration testing are performed only with written authorization and a clearly agreed scope. Testing is limited to systems and environments explicitly authorized by the client.',
    ],

    'network-security-assessment' => [
        'kicker' => 'Security Assessment',
        'nav' => 'Network Security Assessment',
        'short' => 'Network Security',
        'art' => 'service-netsec',
        'art_inverted' => true,
        'lead' => "Evaluate the security of an organization's internal and external networks. Review exposed services, network segmentation, firewall boundaries, remote access, access controls, and potential attack paths. Deliver a prioritized risk report.",
        'included' => [
            'Internal and external networks',
            'Exposed services',
            'Network segmentation',
            'Firewall boundaries',
            'Remote access',
            'Access controls',
            'Potential attack paths',
        ],
        'deliverables' => [
            'Prioritized risk report',
        ],
        'note' => 'Assessments are performed only with written authorization and a clearly agreed scope.',
    ],

    'firewall-security-review' => [
        'kicker' => 'Network Defense',
        'nav' => 'Firewall Configuration & Security Review',
        'short' => 'Firewall Review',
        'art' => 'service-firewall',
        'art_inverted' => false,
        'lead' => 'Review firewall policies, NAT rules, VPN configurations, administrator access, firmware, logging, and rule conflicts. Identify unnecessary access and recommend improvements for platforms such as Fortinet and Sophos.',
        'included' => [
            'Firewall policies',
            'NAT rules',
            'VPN configurations',
            'Administrator access',
            'Firmware',
            'Logging',
            'Rule conflicts',
        ],
        'deliverables' => [
            'Unnecessary access identified',
            'Recommended improvements',
        ],
    ],

    'security-configuration-hardening' => [
        'kicker' => 'Security Hardening',
        'nav' => 'Security Configuration & Hardening Review',
        'short' => 'Security Hardening',
        'art' => 'note-controls',
        'art_inverted' => true,
        'lead' => 'Assess and improve the configuration of servers, endpoints, network devices, operating systems, cloud platforms, and security products. Apply secure baselines, remove unnecessary services, strengthen authentication, and reduce attack surfaces.',
        'included' => [
            'Servers',
            'Endpoints',
            'Network devices',
            'Operating systems',
            'Cloud platforms',
            'Security products',
        ],
        'deliverables' => [
            'Secure baselines applied',
            'Unnecessary services removed',
            'Authentication strengthened',
            'Attack surfaces reduced',
        ],
    ],

    'security-infrastructure-deployment' => [
        'kicker' => 'Implementation',
        'nav' => 'Security Infrastructure Installation & Deployment',
        'short' => 'Infrastructure Deployment',
        'art' => 'service-deploy',
        'art_inverted' => false,
        'lead' => 'Install, configure, and deploy firewalls, switches, wireless access points, endpoint protection, SIEM platforms, VPNs, email security, and backup solutions. Includes initial testing, handover documentation, and administrator training.',
        'included' => [
            'Firewalls',
            'Switches',
            'Wireless access points',
            'Endpoint protection',
            'SIEM platforms',
            'VPNs',
            'Email security',
            'Backup solutions',
        ],
        'deliverables' => [
            'Initial testing',
            'Handover documentation',
            'Administrator training',
        ],
    ],

];
