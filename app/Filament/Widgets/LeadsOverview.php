<?php

namespace App\Filament\Widgets;

use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\Lead;
use App\Models\Setting;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

/**
 * The top of the dashboard: what has come in, and what is still unfinished.
 *
 * The last stat is the one that earns its place. Site content is seeded with
 * the prototype's `[placeholder]` values verbatim and those must not be
 * invented (CLAUDE.md), so the only way they ever become real is for someone
 * to notice them. This counts them, on the first screen, every time.
 */
class LeadsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    /** @return array<Stat> */
    protected function getStats(): array
    {
        $week = Lead::query()->where('created_at', '>=', now()->subDays(7))->count();
        $previousWeek = Lead::query()
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        return [
            $this->leadsStat($week, $previousWeek),
            $this->unnotifiedStat(),
            $this->contentStat(),
            $this->placeholderStat(),
        ];
    }

    private function leadsStat(int $week, int $previousWeek): Stat
    {
        $stat = Stat::make('Requests, last 7 days', (string) $week)
            ->icon(Heroicon::OutlinedInbox)
            ->chart($this->dailyCounts())
            ->chartColor('info');

        if ($previousWeek === 0) {
            return $stat->description($week === 0 ? 'None yet' : 'First week with requests');
        }

        $change = (int) round((($week - $previousWeek) / $previousWeek) * 100);

        return $stat
            ->description(sprintf('%s%d%% on the week before', $change >= 0 ? '+' : '', $change))
            ->descriptionIcon($change >= 0 ? Heroicon::OutlinedArrowTrendingUp : Heroicon::OutlinedArrowTrendingDown)
            ->descriptionColor($change >= 0 ? 'info' : 'gray');
    }

    /**
     * Leads whose notification never left the queue. Mail is queued, so a
     * stopped worker is silent until someone asks why nobody replied.
     */
    private function unnotifiedStat(): Stat
    {
        $stuck = Lead::query()->whereNull('notified_at')->count();

        return Stat::make('Notifications not sent', (string) $stuck)
            ->icon(Heroicon::OutlinedBellAlert)
            ->description($stuck === 0
                ? 'Every request has been emailed'
                : 'Check that the queue worker is running')
            ->descriptionColor($stuck === 0 ? 'gray' : 'danger');
    }

    private function contentStat(): Stat
    {
        $published = Insight::query()->whereNotNull('published_at')->where('published_at', '<=', now())->count();
        $drafts = Insight::query()->count() - $published;
        $cases = CaseStudy::query()->count();

        return Stat::make('Published field notes', (string) $published)
            ->icon(Heroicon::OutlinedNewspaper)
            ->description($drafts > 0
                ? sprintf('%d draft%s · %d case stud%s', $drafts, $drafts === 1 ? '' : 's', $cases, $cases === 1 ? 'y' : 'ies')
                : sprintf('%d case stud%s', $cases, $cases === 1 ? 'y' : 'ies'));
    }

    /**
     * A setting is a placeholder while it is still the prototype's bracketed
     * text, or empty. Matches the warning marker on the Site settings page.
     */
    private function placeholderStat(): Stat
    {
        $values = Setting::query()->pluck('value');
        $outstanding = $values
            ->filter(fn (?string $v) => blank($v) || str_contains((string) $v, '['))
            ->count();

        return Stat::make('Settings still placeholder', (string) $outstanding)
            ->icon(Heroicon::OutlinedExclamationTriangle)
            ->description($outstanding === 0
                ? 'Every value is real'
                : sprintf('of %d — real copy needed before launch', $values->count()))
            ->descriptionColor($outstanding === 0 ? 'gray' : 'info');
    }

    /**
     * Fourteen days of counts for the sparkline, with the empty days present
     * as zeroes — grouping in SQL alone would silently compress them and draw
     * a flatter, friendlier line than the truth.
     *
     * @return array<int, int>
     */
    private function dailyCounts(): array
    {
        $counts = Lead::query()
            ->where('created_at', '>=', now()->subDays(14)->startOfDay())
            ->selectRaw('date(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        return collect(range(13, 0))
            ->map(fn (int $ago) => (int) ($counts[Carbon::now()->subDays($ago)->toDateString()] ?? 0))
            ->all();
    }
}
