<?php

namespace Database\Seeders;

use App\Models\Insight;
use Illuminate\Database\Seeder;

/**
 * The three field notes exactly as the prototype prints them.
 *
 * `date_label` holds the literal "[DD MMM 2026]" the design shows; `published_at`
 * is the sortable timestamp behind it. The client replaces the labels with real
 * dates — at which point date_label can be derived from published_at instead.
 *
 * ON THE BODIES. The prototype gives these three articles a title and nothing
 * else. The bodies below are DRAFT EDITORIAL COPY written to exercise the
 * long-form typography — headings, lists, a pull quote, links — and to give the
 * client something to edit rather than an empty page. They describe general
 * practice and avoid asserting anything about UB Shield that has not been
 * confirmed, but they still need an editorial pass before launch.
 * Logged in docs/placeholders.md.
 */
class InsightSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::rows() as $row) {
            Insight::query()->firstOrCreate(
                ['slug' => $row['slug']],
                $row + ['published_at' => now()]
            );
        }
    }

    public static function rows(): array
    {
        return [
            [
                'slug' => 'dpdpa-2023-consent-flows',
                'title' => 'DPDPA 2023: what changes in your consent flows once the Rules apply',
                'category' => 'Advisory',
                'date_label' => '[DD MMM 2026]',
                'cover_art_key' => 'note-consent',
                'art_inverted' => false,
                'position' => 1,
                'excerpt' => 'Most consent banners collect agreement. The Act expects you to be able to prove what was agreed, when, and to what — which is a different engineering problem.',
                'body' => <<<'HTML'
                    <p>Almost every consent implementation we review does the same thing: it records that a user clicked the button. That was enough under a notice-and-choice regime. It is not enough under the Digital Personal Data Protection Act, which expects consent to be free, specific, informed, unconditional and unambiguous — and expects you to be able to demonstrate it after the fact.</p>

                    <p>The practical difference is that consent stops being a flag on a user record and becomes an append-only log.</p>

                    <h2>What a compliant record looks like</h2>

                    <p>If a Data Principal withdraws consent and asks what you were relying on, a boolean column cannot answer. The record needs to carry enough to reconstruct the moment:</p>

                    <ul>
                        <li>the exact notice text that was shown, versioned, not a reference to whatever the current copy happens to be;</li>
                        <li>the specific purposes agreed to, separately, rather than bundled into one acceptance;</li>
                        <li>when it was given, and through which interface;</li>
                        <li>whether it has since been withdrawn, and when.</li>
                    </ul>

                    <blockquote><p>Consent you cannot evidence is consent you do not have.</p></blockquote>

                    <h2>Three things that usually need rebuilding</h2>

                    <h3>Bundled consent</h3>
                    <p>A single checkbox covering account creation, marketing and analytics fails the specificity test. Each purpose needs its own decision, and refusing one cannot block a service that does not depend on it.</p>

                    <h3>Pre-ticked boxes and implied agreement</h3>
                    <p>Anything that treats inaction as agreement does not survive the "unambiguous" requirement. This includes the pattern where continuing to scroll is taken as acceptance.</p>

                    <h3>Withdrawal that is harder than giving</h3>
                    <p>The Act expects withdrawal to be about as easy as consent was to give. In practice this is where most implementations fail: two clicks to accept, an email to support to withdraw.</p>

                    <h2>Children&rsquo;s data</h2>

                    <p>Processing data about children attracts stricter expectations, including verifiable parental consent and a prohibition on behavioural advertising directed at them. If your user base might include children — and for most consumer services it does — age assurance becomes part of the consent flow rather than a separate project.</p>

                    <hr>

                    <h2>Where to start</h2>

                    <ol>
                        <li>Inventory every point where you collect personal data and what you currently record about the agreement.</li>
                        <li>Separate bundled purposes and version the notice text.</li>
                        <li>Build withdrawal as a first-class path, not a support ticket.</li>
                        <li>Decide retention per purpose, because consent that has been withdrawn does not retroactively justify keeping the data.</li>
                    </ol>

                    <p>If you are already running a GDPR programme, much of the mapping carries over — but the consent and notice mechanics differ enough that reusing the implementation wholesale is a mistake. We cover this in <a href="/services/data-privacy">our data privacy practice</a>.</p>
                    HTML,
            ],

            [
                'slug' => 'sebi-cscrf-control-by-control',
                'title' => 'SEBI CSCRF, control by control: a checklist for regulated entities',
                'category' => 'Guide',
                'date_label' => '[DD MMM 2026]',
                'cover_art_key' => 'note-controls',
                'art_inverted' => true,
                'position' => 2,
                'excerpt' => 'The Cybersecurity and Cyber Resilience Framework reads as a long list. Grouped by what it is actually asking for, it is shorter than it looks.',
                'body' => <<<'HTML'
                    <p>SEBI&rsquo;s Cybersecurity and Cyber Resilience Framework consolidates guidance that regulated entities previously had to assemble from several circulars. Read front to back it is daunting. Grouped by intent, the same requirements collapse into a handful of questions you either can or cannot answer.</p>

                    <h2>Govern: who owns this?</h2>

                    <p>The framework expects named accountability rather than a committee. In practice an assessor will ask who signed the policy, when it was last reviewed, and what changed at that review. A policy approved three years ago and untouched since is evidence of absence.</p>

                    <ul>
                        <li>An approved cyber security and cyber resilience policy, reviewed on a stated cycle.</li>
                        <li>A designated officer with the authority the role implies.</li>
                        <li>Board-level reporting that someone can produce on request.</li>
                    </ul>

                    <h2>Identify: what are you protecting?</h2>

                    <p>Asset inventory is the control that quietly determines whether everything downstream works. Classification matters more than completeness here — an inventory that lists everything but ranks nothing does not help anyone decide what to patch first.</p>

                    <h2>Protect: access, and then everything else</h2>

                    <h3>Access control</h3>
                    <p>Least privilege, periodic recertification, and removal on exit. The recertification evidence is what auditors ask for and what organisations most often cannot produce.</p>

                    <h3>Network and data security</h3>
                    <p>Segmentation, encryption in transit and at rest, and controls on data leaving the organisation. Expect to be asked where the boundary is drawn and why.</p>

                    <h2>Detect and respond</h2>

                    <p>Monitoring coverage, and a response plan that has been exercised rather than written. The framework is explicit that incidents must be reported within defined timelines, which makes the reporting path itself a control worth testing.</p>

                    <blockquote><p>An untested response plan is a document, not a capability.</p></blockquote>

                    <h2>Recover</h2>

                    <p>Recovery objectives that have been measured against a real restore, not assumed. A recovery time objective nobody has ever hit is a target, not a control.</p>

                    <hr>

                    <h2>The practical shortcut</h2>

                    <p>Most of these controls overlap heavily with ISO/IEC 27001. If you are certified, the gap is usually narrower than it first appears — the work is mapping existing evidence onto SEBI&rsquo;s structure rather than building new controls. We do that mapping as part of <a href="/services/governance-risk-compliance">our GRC practice</a>, and it is normally where a readiness project starts.</p>
                    HTML,
            ],

            [
                'slug' => 'five-cloud-misconfigurations-aws',
                'title' => 'Five cloud misconfigurations we find in almost every AWS review',
                'category' => 'Field note',
                'date_label' => '[DD MMM 2026]',
                'cover_art_key' => 'note-cloud',
                'art_inverted' => false,
                'position' => 3,
                'excerpt' => 'None of these are clever. They are the same five findings, in roughly the same order, on estates run by people who know what they are doing.',
                'body' => <<<'HTML'
                    <p>Cloud breaches are rarely the result of a novel technique. They are the result of a default nobody changed, a permission granted for an afternoon and never revoked, or a resource created outside the process that would have caught it. Here are the five we find most often.</p>

                    <h2>01. Over-permissive IAM roles</h2>

                    <p>A role created with a wildcard action "just to get it working", then kept. The fix is unglamorous: read the access analyser output, scope to the actions actually used, and re-check after a month of real traffic rather than guessing up front.</p>

                    <h2>02. Long-lived access keys</h2>

                    <p>Static credentials with no rotation and no expiry, frequently in a CI configuration or — still — a repository. Roles and short-lived credentials remove the class of problem entirely, which is a better outcome than rotating keys on a calendar.</p>

                    <h2>03. Storage that is more public than anyone believes</h2>

                    <p>Not usually a bucket open to the world any more; the defaults improved. More often it is a bucket readable by every authenticated principal in the account, or a policy that grants access far more broadly than the person writing it intended.</p>

                    <h2>04. Logging that stops where it matters</h2>

                    <p>Trails enabled in one region, or capturing management events but not data events, so the one question an incident responder needs to answer is the one the logs cannot. Verify by asking what you would be able to reconstruct, not whether logging is on.</p>

                    <h2>05. Security groups with an inherited 0.0.0.0/0</h2>

                    <p>Almost always on a port opened for a migration, a vendor or a debugging session that ended months ago. Rule bases need reading in full, periodically, because nothing else removes them.</p>

                    <blockquote><p>Every one of these is found by a tool. The work is deciding which of the four hundred findings actually matters.</p></blockquote>

                    <hr>

                    <h2>Why the tooling is not enough</h2>

                    <p>Provider tooling is good at flagging known misconfigurations and has no opinion about your architecture. It cannot tell you that the over-permissive role is attached to an instance in a subnet that can reach your database, which is the difference between a medium finding and an incident. That judgement is what <a href="/services/infrastructure-cloud">an infrastructure and cloud review</a> adds on top.</p>
                    HTML,
            ],
        ];
    }
}
