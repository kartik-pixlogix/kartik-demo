<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Requests per week, split by the service the visitor picked.
 *
 * Which practice the enquiries are landing on is the one question the lead
 * table cannot answer at a glance, and it is the question that decides where
 * the next page of the site gets written.
 */
class LeadsByService extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Requests by service';

    protected ?string $description = 'The last twelve weeks, by the service chosen on the form.';

    protected ?string $maxHeight = '260px';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Bright Blue and Cyan are the only two series colours the brand allows,
     * so a stack of five services cannot be told apart by hue. They are
     * separated by lightness along the blue axis instead — the same device
     * the site uses for its own layered artwork.
     *
     * @return array<int, string>
     */
    private function series(): array
    {
        return ['#27B8ED', '#1479D4', '#0F5FA8', '#0C2944', '#C7D0D8'];
    }

    /** @return array<string, mixed> */
    protected function getData(): array
    {
        $weeks = collect(range(11, 0))->map(fn (int $ago) => Carbon::now()->startOfWeek()->subWeeks($ago));

        $rows = Lead::query()
            ->where('created_at', '>=', $weeks->first())
            ->get(['service', 'created_at'])
            ->groupBy(fn (Lead $lead) => $lead->service ?? 'Not stated');

        $palette = $this->series();

        $datasets = $rows->values()->map(fn ($leads, int $i) => [
            'label' => $rows->keys()[$i],
            'data' => $weeks->map(
                fn (Carbon $start) => $leads->filter(
                    fn (Lead $lead) => $lead->created_at->betweenIncluded($start, $start->copy()->endOfWeek())
                )->count()
            )->all(),
            'backgroundColor' => $palette[$i % count($palette)],
            'borderWidth' => 0,
        ])->all();

        return [
            'datasets' => $datasets,
            'labels' => $weeks->map(fn (Carbon $start) => $start->format('j M'))->all(),
        ];
    }

    /**
     * Square bars, no grid on the value axis, hairlines in the brand's own
     * line colour. Chart.js defaults would put rounded bars and mid-grey
     * gridlines on a navy panel.
     */
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { color: '#C7D0D8', font: { size: 11 } },
                    border: { color: 'rgba(199,208,216,.22)' },
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { color: '#C7D0D8', precision: 0, font: { size: 11 } },
                    grid: { color: 'rgba(199,208,216,.12)' },
                    border: { display: false },
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#C7D0D8',
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: false,
                        font: { size: 11 },
                    },
                },
            },
        }
        JS);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
