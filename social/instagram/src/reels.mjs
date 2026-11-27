/*
 * Reel scripts: one per post, five beats each (hook, three scenes, close).
 *
 *   hook   the big stacked words that open the reel (the last line is cyan)
 *   vo     what the voice says in each beat. Also the on-screen caption.
 *
 * The scene titles and visuals come from the carousel content (content.mjs);
 * this file only adds the spoken script. Every line restates the client's
 * document: no new claims, numbers, offers or promises.
 *
 * `say` (see below) is the same text spelled for the voice model.
 */
export const reels = {
  1: {
    hook: ['Who is', 'UB Shield?'],
    vo: [
      'Who is UB Shield, and how can we help secure your business?',
      'We are a growing cybersecurity and network solutions company, based in Ahmedabad and Rajkot.',
      'Six services: network design, VAPT, network security assessment, firewall review, security hardening, and security infrastructure deployment.',
      'Recommendations based on your environment. Clear scope, deliverables and timelines. Security work only with proper authorization.',
      "Let's secure your business. Send us your requirement at ubshield.in/contact",
    ],
  },
  2: {
    hook: ['Small business', 'does not mean', 'no risk.'],
    vo: [
      "Does your business need cybersecurity, even if it's small? Small business does not mean no risk.",
      'If you use email, Wi-Fi, online payments, or store customer data, you have something to protect.',
      'Common weak points: weak or reused passwords, software that is not updated, shared Wi-Fi, and backups that are never tested.',
      'Start with these. Turn on multi-factor authentication, install updates regularly, separate guest Wi-Fi, and test your backups.',
      'Know your gaps. UB Shield can help you review your IT security. Enquire at ubshield.in/contact',
    ],
  },
  3: {
    hook: ['From', 'finding gaps', 'to fixing them.'],
    vo: [
      'How does UB Shield work, from finding gaps to fixing them? Our four-step approach.',
      'Step one. We learn your requirements, IT environment and security goals, and confirm scope and authorization.',
      'Step two. We identify gaps and give prioritized recommendations and next steps.',
      'Steps three and four. We carry out the agreed work, then test, document and hand over.',
      "Have a requirement? Let's discuss it at ubshield.in/contact",
    ],
  },
  4: {
    hook: ['Is your', 'network', 'ready to grow?'],
    vo: [
      'Is your office network ready for your business to grow? More users and devices need a better plan.',
      'Warning signs: a slow or unstable connection, no network diagram, and one failure that stops the whole office.',
      'Good design covers network topology, IP planning, routing and switching, redundancy and VPN connectivity.',
      'We design secure, scalable networks for offices, data centers and multi-branch organizations, with network documentation.',
      'Plan before you expand. Enquire at ubshield.in/contact',
    ],
  },
  5: {
    hook: ['One office.', 'Separate', 'network zones.'],
    vo: [
      "What is VLAN segmentation, and why shouldn't every device share one network?",
      'On one flat network, a single infected device may be able to reach many other systems.',
      'A VLAN divides a physical network into separate logical networks. Access rules control traffic between them.',
      'Example zones: staff, guest Wi-Fi, servers, and CCTV and IoT devices.',
      'Is your network segmented? UB Shield can help plan it at ubshield.in/contact',
    ],
  },
  6: {
    hook: ['Find weaknesses', 'before', 'attackers do.'],
    vo: [
      'What is VAPT, and why does your business need it? Find weaknesses before attackers do.',
      'VAPT means Vulnerability Assessment and Penetration Testing: finding security weaknesses and validating them.',
      'We can test networks, servers, web applications, APIs, and other authorized systems.',
      'You get risk ratings and evidence, remediation advice, and retesting to verify fixes. Testing needs written authorization and an agreed scope.',
      'Test your security. Enquire at ubshield.in/contact',
    ],
  },
  7: {
    hook: ['Related.', 'But not', 'the same.'],
    vo: [
      "Vulnerability assessment versus penetration testing. What's the difference? Related, but not the same.",
      'A vulnerability assessment looks for known and possible weaknesses across the agreed systems, and rates their risk.',
      'Penetration testing safely attempts to exploit weaknesses, with permission, to confirm what an attacker could actually do.',
      'A simple example. Assessment: this door lock looks weak. Penetration test: with your permission, we checked whether it can be opened.',
      'Which do you need? It depends on your systems and goals. Ask us at ubshield.in/contact',
    ],
  },
  8: {
    hook: ['Check the', 'entry points.'],
    vo: [
      'How could an attacker get into your business network? Check the entry points.',
      'Exposed services. Services open to the internet that do not need to be can increase your risk.',
      'Remote access. Weak VPN or remote desktop settings and poor passwords can open a way in.',
      'Inside the network. Poor segmentation and weak access controls can let an attacker move from one system to another.',
      'Review your attack paths. Enquire at ubshield.in/contact',
    ],
  },
  9: {
    hook: ['A health check', 'for your', 'network.'],
    vo: [
      'What happens during a network security assessment? A health check for your network.',
      'Before we start, we agree on the scope. Assessments are done only with written authorization.',
      'We evaluate internal and external networks, exposed services and segmentation, firewall boundaries, remote access, and potential attack paths.',
      'You get a prioritized risk report, so you know what to address first.',
      'Book an assessment. Send your requirement at ubshield.in/contact',
    ],
  },
  10: {
    hook: ['You have', 'a firewall.', 'Is it configured correctly?'],
    vo: [
      'You have a firewall. But is it configured correctly? Buying a firewall is only the first step.',
      'Rules change over time. Old or broad rules can allow access that is no longer needed.',
      'We review policies and NAT rules, VPN configuration, admin access, firmware and logging, and rule conflicts.',
      'We identify unnecessary access and recommend improvements, with support for platforms such as Fortinet and Sophos.',
      'Get your firewall reviewed. Enquire at ubshield.in/contact',
    ],
  },
  11: {
    hook: ['Common', 'firewall', 'mistakes.'],
    vo: [
      'What are common firewall mistakes businesses make? Check if any of these apply to you.',
      'Mistake one: broad rules. Allow any style rules can give far more access than needed.',
      'Mistake two: weak admin access. Default or shared logins, or a management page open to the internet.',
      'Mistake three: no maintenance. Old firmware, logging turned off or never checked, and unused rules or VPN users left active.',
      'Find and fix the gaps. Request a firewall review at ubshield.in/contact',
    ],
  },
  12: {
    hook: ['Reduce', 'the ways in.'],
    vo: [
      "What is security hardening, and why aren't default settings enough?",
      'Default settings may not match your security needs. Some can leave unnecessary features or access enabled.',
      'Hardening means improving system settings to reduce the attack surface: the number of ways a system can be attacked.',
      'We review servers and endpoints, network devices and operating systems, cloud platforms, and security products.',
      'Check your configuration. Enquire at ubshield.in/contact',
    ],
  },
  13: {
    hook: ['Harder', 'to attack.', '4 practical steps.'],
    vo: [
      'How can you make your servers and computers harder to attack? Four practical steps.',
      'One. Apply secure baselines: a standard set of secure settings for your systems.',
      "Two. Remove what you don't need. Disable unused services, software and accounts, and test changes before applying them widely.",
      'Three and four. Use strong authentication, including multi-factor authentication, and install security updates regularly.',
      'Need a hardening review? Talk to UB Shield at ubshield.in/contact',
    ],
  },
  14: {
    hook: ['The right mix', 'depends on', 'your business.'],
    vo: [
      'Which security tools does a growing business need? The right mix depends on your business.',
      'For network protection: firewalls, secure switches and wireless access points, and VPN.',
      'For devices, email and data: endpoint protection, email security, and backups.',
      'For visibility, SIEM collects and analyses security logs. It needs someone to monitor and respond. Not every business needs it on day one.',
      'Choose what fits. We install and configure these solutions. Enquire at ubshield.in/contact',
    ],
  },
  15: {
    hook: ['Installed', 'does not always', 'mean ready.'],
    vo: [
      'What should you check before calling a security installation complete? Installed does not always mean ready.',
      'Check one: testing. Has each solution been tested to confirm it works as planned?',
      'Check two: documentation. Do you have handover documents showing what was installed and configured?',
      'Check three: training. Does your admin know how to manage it? Have default passwords been changed?',
      'Deploy with a proper handover: initial testing, handover documentation and admin training. ubshield.in/contact',
    ],
  },
};

/** The script as sent to the voice model: beats separated by a pause, awkward tokens spelled out. */
export const say = (id) =>
  reels[id].vo
    .map((line) => line
      .replace(/(at )?ubshield\.in\/contact/, (m, at) => (at ? 'at ' : 'Visit ') + 'UB Shield dot in, slash contact.')
      .replace(/\bVLAN\b/g, 'V-LAN')
      .replace(/\bSIEM\b/g, 'S.I.E.M.')
      .replace(/\bIoT\b/g, 'I.o.T.'))
    .join(' [pause] ');
