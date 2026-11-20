<?php

/*
|-------------------------------------------------------------------------------
| Launch content
|-------------------------------------------------------------------------------
|
| Source: "UB Shield — Final Website Content & Developer Handover Brief"
| (launch 23 November). Text is the brief's, verbatim, apart from sentence
| case where the design sets type in capitals anyway. Section numbers refer
| to the brief.
|
| Do not add claims here that the brief does not make: no statistics, no
| certifications, no client names, no response-time promises (§8, §10).
|
*/

return [

    // §2 Hero
    'eyebrow' => 'Cybersecurity • Network Solutions • IT Infrastructure',
    'headline' => ['Secure', 'Today.', 'Stronger', 'Tomorrow.'],
    'hero_lead' => 'UB Shield provides cybersecurity and network infrastructure services to help businesses build secure, reliable and resilient IT environments. From network architecture and security assessments to firewall configuration and infrastructure deployment, we help organizations identify risks and strengthen their digital infrastructure.',

    // §1 Recommended company description
    'description' => 'UB Shield is a growing cybersecurity and network infrastructure services company focused on helping businesses build secure, reliable and resilient IT environments.',

    // §2 About UB Shield
    'about' => [
        'UB Shield is a growing cybersecurity and network solutions company focused on helping businesses improve their IT security and infrastructure. We provide services in network design, vulnerability assessment, network security, firewall configuration, security hardening and infrastructure deployment.',
        "Our approach begins with understanding each organization's requirements. We then identify relevant gaps, recommend suitable solutions and support the agreed implementation. We aim to build long-term relationships through transparent communication, responsible security practices and reliable technical support.",
    ],

    // §1 Delivery model
    'delivery' => 'We are a small core team, supported by technical partners when a project requires additional expertise or resources.',

    // §2 Why work with UB Shield?
    'why' => [
        "Practical, scope-based recommendations aligned with the client's environment.",
        'Clear communication about project scope, deliverables and timelines.',
        'Security work performed only with appropriate authorization.',
        'Implementation support and handover documentation where included in the agreed scope.',
        'Additional technical expertise through project partners when required.',
    ],

    // §5 Our approach
    'approach' => [
        ['Understand & Assess', 'Understand client requirements, existing IT environment and security objectives. Confirm scope and authorization.'],
        ['Identify & Recommend', 'Identify relevant gaps and prepare practical recommendations with priorities, deliverables and next steps.'],
        ['Implement & Configure', 'Carry out agreed network, security or infrastructure work using the core team and approved technical partners as required.'],
        ['Validate & Handover', 'Test the agreed implementation, document the work and provide handover information and recommendations.'],
    ],

    // §4 Target industries — sectors UB Shield aims to serve, not client claims.
    // `preview` picks the hover artwork (resources/views/components/ui/
    // industry-preview.blade.php); null shows none.
    'industries' => [
        ['IT & Software Companies', 'Software houses, SaaS businesses, IT service providers and technology firms.', 3],
        ['Small and Medium Businesses', 'Offices, trading businesses and growing organizations relying on business networks and systems.', null],
        ['Manufacturing & Industrial Businesses', 'Factories, industrial offices, warehouses and connected business networks.', 4],
        ['Healthcare & Clinics', 'Clinics, diagnostic centres and healthcare organizations handling sensitive information.', 2],
        ['Education & Training Institutes', 'Schools, colleges, coaching and professional training institutes.', 5],
        ['Retail & E-commerce', 'Retail businesses, online stores and e-commerce operators.', 6],
        ['Finance & Professional Services', 'Accounting firms, consultants and financial service businesses.', 1],
        ['Hospitality', 'Hotels, restaurants, resorts and guest-facing businesses.', null],
        ['Logistics & Warehousing', 'Transport operators, warehouses and distribution businesses.', null],
    ],

    // §1 / §6 Service locations
    'locations' => 'Ahmedabad and Rajkot, Gujarat, India',
    'coverage' => 'Ahmedabad, Rajkot and projects across Gujarat, subject to project scope and availability.',

    // §6 Contact section
    'contact_title' => ["Let's discuss your", 'network & security', 'requirements.'],
    'contact_lead' => 'Tell us about your business, IT environment and security requirements. Our team will review your enquiry and get in touch to discuss the next steps.',

];
