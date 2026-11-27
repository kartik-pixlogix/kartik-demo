/*
 * UB Shield Instagram carousels: 15 posts x 5 pages.
 *
 * Source: "UB Shield: Instagram Carousel Content" (client PDF, November 2026).
 * Text is the document's, verbatim. Each page is { h, body?, bullets?, pairs?, note? }:
 *   h        the bold line (page heading)
 *   body     the remaining text
 *   bullets  list items
 *   pairs    labelled lines ("Assessment: ...") shown as a comparison
 *   note     a closing line under a list
 * `service` is the document's own section title for posts 4-15, shown as a tag.
 * `art` picks the illustration (see art.mjs) from the document's "visual idea".
 */

const U = 'ubshield.in/contact';

export const posts = [
  {
    id: 1, slug: 'who-is-ub-shield', service: null, art: 'emblem',
    pages: [
      { h: 'Who is UB Shield, and how can we help secure your business?', body: 'Secure Today. Stronger Tomorrow.' },
      { h: 'Who We Are', body: 'A growing cybersecurity and network solutions company based in Ahmedabad and Rajkot.' },
      { h: 'Our 6 Services', bullets: ['Network Design & Architecture', 'VAPT', 'Network Security Assessment', 'Firewall Configuration & Review', 'Security Hardening Review', 'Security Infrastructure Deployment'] },
      { h: 'Why UB Shield?', bullets: ['Recommendations based on your environment and scope', 'Clear scope, deliverables and timelines', 'Security work only with proper authorization'] },
      { h: "Let's Secure Your Business", body: `Send us your requirement: ${U}` },
    ],
    caption: 'Meet UB Shield. We help businesses build more secure and reliable IT environments. Tell us what you need at ubshield.in/contact',
    hashtags: '#UBShield #CyberSecurity #NetworkSecurity #Ahmedabad #Rajkot',
  },
  {
    id: 2, slug: 'small-business-security', service: null, art: 'checklist',
    pages: [
      { h: "Does your business need cybersecurity, even if it's small?", body: 'Small business does not mean no risk.' },
      { h: 'Why It Matters', body: 'If you use email, Wi-Fi, online payments or store customer data, you have something to protect.' },
      { h: 'Common Weak Points', bullets: ['Weak or reused passwords', 'Software that is not updated', 'One Wi-Fi shared by staff and guests', 'Backups that are never tested'] },
      { h: 'Start With These', bullets: ['Turn on multi-factor authentication', 'Install updates regularly', 'Separate guest Wi-Fi', 'Test your backups'] },
      { h: 'Know Your Gaps', body: `UB Shield can help you review your IT security. Enquire at ${U}` },
    ],
    caption: 'Attackers do not only target large companies. Start with the basics and know where your gaps are.',
    hashtags: '#UBShield #SmallBusiness #CyberSecurity #MSME #Gujarat',
  },
  {
    id: 3, slug: 'our-4-step-approach', service: null, art: 'steps',
    pages: [
      { h: 'How does UB Shield work, from finding gaps to fixing them?', body: 'Our 4-step approach.' },
      { h: 'Step 1: Understand & Assess', body: 'We learn your requirements, IT environment and security goals, and confirm scope and authorization.' },
      { h: 'Step 2: Identify & Recommend', body: 'We identify gaps and give prioritized recommendations and next steps.' },
      { h: 'Steps 3 & 4: Implement, Validate & Handover', body: 'We carry out the agreed work, then test, document and hand over. Technical partners join when extra expertise is needed.' },
      { h: 'Have a Requirement?', body: `Let's discuss it: ${U}` },
    ],
    caption: 'A clear process helps you know what happens at each stage. Here is how we work.',
    hashtags: '#UBShield #CyberSecurity #ITSecurity #ITInfrastructure #Gujarat',
  },
  {
    id: 4, slug: 'network-ready-to-grow', service: 'Network Design & Architecture', art: 'network',
    pages: [
      { h: 'Is your office network ready for your business to grow?', body: 'More users and devices need a better plan.' },
      { h: 'Warning Signs', bullets: ['Slow or unstable connection', 'No network diagram', 'Adding new devices is difficult', 'One failure stops the whole office'] },
      { h: 'What Good Design Covers', bullets: ['Network topology', 'IP planning', 'Routing and switching', 'Redundancy and VPN connectivity'] },
      { h: 'How UB Shield Helps', body: 'We design secure, scalable networks for offices, data centers and multi-branch organizations, with network documentation.' },
      { h: 'Plan Before You Expand', body: `Enquire at ${U}` },
    ],
    caption: 'Growing your team or opening a new branch? Plan your network first.',
    hashtags: '#UBShield #NetworkDesign #Networking #ITInfrastructure #BusinessGrowth',
  },
  {
    id: 5, slug: 'vlan-segmentation', service: 'Network Design & Architecture', art: 'zones',
    pages: [
      { h: "What is VLAN segmentation, and why shouldn't every device share one network?", body: 'One office. Separate network zones.' },
      { h: 'The Problem', body: 'On one flat network, a single infected device may be able to reach many other systems.' },
      { h: 'What Is a VLAN?', body: 'A VLAN divides a physical network into separate logical networks. Access rules control traffic between them.' },
      { h: 'Example Zones', bullets: ['Staff', 'Guest Wi-Fi', 'Servers', 'CCTV and IoT devices'] },
      { h: 'Is Your Network Segmented?', body: `UB Shield can help plan it: ${U}` },
    ],
    caption: "Should a guest's phone be on the same network as your office server? Segmentation, with the right access rules, helps keep them apart.",
    hashtags: '#UBShield #VLAN #NetworkSecurity #Networking #CyberSecurity',
  },
  {
    id: 6, slug: 'what-is-vapt', service: 'VAPT', art: 'vapt',
    pages: [
      { h: 'What is VAPT, and why does your business need it?', body: 'Find weaknesses before attackers do.' },
      { h: 'VAPT Means', body: 'Vulnerability Assessment and Penetration Testing: finding security weaknesses and validating them.' },
      { h: 'What Can Be Tested?', bullets: ['Networks', 'Servers', 'Web applications', 'APIs', 'Other authorized systems'] },
      { h: 'What You Get', bullets: ['Risk ratings and evidence', 'Remediation advice', 'Retesting to verify fixes'], note: 'Testing needs written authorization and an agreed scope.' },
      { h: 'Test Your Security', body: `Enquire about VAPT: ${U}` },
    ],
    caption: 'You cannot fix what you do not know about. VAPT helps you find and prioritize security weaknesses.',
    hashtags: '#UBShield #VAPT #PenetrationTesting #CyberSecurity #WebSecurity',
  },
  {
    id: 7, slug: 'va-vs-pt', service: 'VAPT', art: 'lock',
    pages: [
      { h: "Vulnerability Assessment vs. Penetration Testing: what's the difference?", body: 'Related, but not the same.' },
      { h: 'Vulnerability Assessment', body: 'Looks for known and possible weaknesses across the agreed systems and rates their risk.' },
      { h: 'Penetration Testing', body: 'Safely attempts to exploit weaknesses, with permission, to confirm what an attacker could actually do.' },
      { h: 'Simple Example', pairs: [['Assessment', '"This door lock looks weak."'], ['Penetration test', '"With your permission, we checked whether it can be opened."']] },
      { h: 'Which Do You Need?', body: `It depends on your systems and goals. Ask us: ${U}` },
    ],
    caption: 'One finds possible weaknesses. The other checks how they could be exploited. Together they give a clearer picture.',
    hashtags: '#UBShield #VAPT #EthicalHacking #InfoSec #CyberSecurity',
  },
  {
    id: 8, slug: 'entry-points', service: 'Network Security Assessment', art: 'radar',
    pages: [
      { h: 'How could an attacker get into your business network?', body: 'Check the entry points.' },
      { h: 'Exposed Services', body: 'Services open to the internet that do not need to be can increase your risk.' },
      { h: 'Remote Access', body: 'Weak VPN or remote desktop settings and poor passwords can open a way in.' },
      { h: 'Inside the Network', body: 'Poor segmentation and weak access controls can let an attacker move from one system to another.' },
      { h: 'Review Your Attack Paths', body: `Enquire at ${U}` },
    ],
    caption: 'Do you know which parts of your network are exposed? A security assessment helps you find out.',
    hashtags: '#UBShield #NetworkSecurity #CyberThreats #RiskAssessment #CyberSecurity',
  },
  {
    id: 9, slug: 'network-security-assessment', service: 'Network Security Assessment', art: 'health',
    pages: [
      { h: 'What happens during a Network Security Assessment?', body: 'A health check for your network.' },
      { h: 'Before We Start', body: 'We agree on the scope. Assessments are done only with written authorization.' },
      { h: 'What We Evaluate', bullets: ['Internal and external networks', 'Exposed services and segmentation', 'Firewall boundaries', 'Remote access and access controls', 'Potential attack paths'] },
      { h: 'What You Get', body: 'A prioritized risk report, so you know what to address first.' },
      { h: 'Book an Assessment', body: `Send your requirement: ${U}` },
    ],
    caption: 'From exposed services to remote access, here is what a network security assessment looks at.',
    hashtags: '#UBShield #SecurityAssessment #NetworkSecurity #ITAudit #CyberSecurity',
  },
  {
    id: 10, slug: 'firewall-configured-correctly', service: 'Firewall Configuration & Review', art: 'firewall',
    pages: [
      { h: 'You have a firewall, but is it configured correctly?', body: 'Buying a firewall is only the first step.' },
      { h: 'Why Check It?', body: 'Rules change over time. Old or broad rules can allow access that is no longer needed.' },
      { h: 'What We Review', bullets: ['Policies and NAT rules', 'VPN configuration', 'Admin access', 'Firmware and logging', 'Rule conflicts'] },
      { h: 'How UB Shield Helps', body: 'We identify unnecessary access and recommend improvements, with support for platforms such as Fortinet and Sophos.' },
      { h: 'Get Your Firewall Reviewed', body: `Enquire at ${U}` },
    ],
    caption: 'When was your firewall last reviewed?',
    hashtags: '#UBShield #Firewall #FirewallSecurity #NetworkSecurity #CyberSecurity',
  },
  {
    id: 11, slug: 'firewall-mistakes', service: 'Firewall Configuration & Review', art: 'warn',
    pages: [
      { h: 'What are common firewall mistakes businesses make?', body: 'Check if any of these apply to you.' },
      { h: 'Mistake 1: Broad Rules', body: '"Allow any" style rules can give far more access than needed.' },
      { h: 'Mistake 2: Weak Admin Access', body: 'Default or shared logins, or a management page open to the internet.' },
      { h: 'Mistake 3: No Maintenance', body: 'Old firmware, logging turned off or never checked, and unused rules or VPN users left active.' },
      { h: 'Find and Fix the Gaps', body: `Request a firewall review: ${U}` },
    ],
    caption: 'Small configuration mistakes can create big security gaps. How many of these have you checked?',
    hashtags: '#UBShield #Firewall #CyberSecurityTips #ITSecurity #NetworkAdmin',
  },
  {
    id: 12, slug: 'what-is-hardening', service: 'Security Hardening Review', art: 'toggles',
    pages: [
      { h: "What is security hardening, and why aren't default settings enough?", body: 'Reduce the ways in.' },
      { h: 'The Problem', body: 'Default settings may not match your security needs. Some can leave unnecessary features or access enabled.' },
      { h: 'What Is Hardening?', body: 'Improving system settings to reduce the attack surface: the number of ways a system can be attacked.' },
      { h: 'What We Review', bullets: ['Servers and endpoints', 'Network devices and operating systems', 'Cloud platforms', 'Security products'] },
      { h: 'Check Your Configuration', body: `Enquire at ${U}` },
    ],
    caption: 'A system that works is not always a system that is securely configured.',
    hashtags: '#UBShield #SecurityHardening #ServerSecurity #CloudSecurity #CyberSecurity',
  },
  {
    id: 13, slug: 'hardening-steps', service: 'Security Hardening Review', art: 'checklist',
    pages: [
      { h: 'How can you make your servers and computers harder to attack?', body: '4 practical steps.' },
      { h: '1. Apply Secure Baselines', body: 'Use a standard set of secure settings for your systems.' },
      { h: "2. Remove What You Don't Need", body: 'Disable unused services, software and accounts. Test changes before applying them widely.' },
      { h: '3 & 4. Protect Logins and Update', body: 'Use strong authentication, including multi-factor authentication. Install security updates regularly.' },
      { h: 'Need a Hardening Review?', body: `Talk to UB Shield: ${U}` },
    ],
    caption: 'Save this post and use it as a starting checklist for your IT team.',
    hashtags: '#UBShield #Hardening #EndpointSecurity #CyberSecurityTips #ITSecurity',
  },
  {
    id: 14, slug: 'security-tools', service: 'Security Infrastructure Deployment', art: 'layers',
    pages: [
      { h: 'Which security tools does a growing business need?', body: 'The right mix depends on your business.' },
      { h: 'Network Protection', bullets: ['Firewalls', 'Secure switches and wireless access points', 'VPN'] },
      { h: 'Device, Email & Data Protection', bullets: ['Endpoint protection', 'Email security', 'Backups'] },
      { h: 'Visibility', body: 'SIEM collects and analyses security logs to help spot suspicious activity. It needs someone to monitor and respond. Not every business needs it on day one.' },
      { h: 'Choose What Fits', body: `We install and configure these solutions. Enquire: ${U}` },
    ],
    caption: 'More tools do not automatically mean more security. Choose what fits your risks and set it up properly.',
    hashtags: '#UBShield #SecurityInfrastructure #EndpointProtection #SIEM #CyberSecurity',
  },
  {
    id: 15, slug: 'installation-checks', service: 'Security Infrastructure Deployment', art: 'handover',
    pages: [
      { h: 'What should you check before calling a security installation complete?', body: 'Installed does not always mean ready.' },
      { h: 'Check 1: Testing', body: 'Has each solution been tested to confirm it works as planned?' },
      { h: 'Check 2: Documentation', body: 'Do you have handover documents showing what was installed and configured?' },
      { h: 'Check 3: Training', body: 'Does your admin know how to manage it? Have default passwords been changed?' },
      { h: 'Deploy With a Proper Handover', body: `UB Shield provides installation with initial testing, handover documentation and admin training. ${U}` },
    ],
    caption: 'Before you close an installation project, check these points.',
    hashtags: '#UBShield #ITDeployment #SecuritySolutions #ITInfrastructure #CyberSecurity',
  },
];
