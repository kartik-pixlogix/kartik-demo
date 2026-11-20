<?php

namespace App\Support;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV export for leads.
 *
 * Streamed rather than built in memory, so exporting the whole table stays
 * flat in memory however large it gets. Written by hand rather than through
 * Filament's export feature, which would add an exports table, a queue worker
 * and a notification pipeline for what is one file.
 */
class LeadCsv
{
    private const COLUMNS = [
        'id' => 'ID',
        'created_at' => 'Received (IST)',
        'name' => 'Name',
        'email' => 'Business email',
        'company' => 'Company / organization',
        'city' => 'City',
        'service' => 'Service interest',
        'requirement' => 'Brief requirement',
        'consented_at' => 'Consent given (IST)',
        'source_page' => 'Source page',
        'utm_source' => 'UTM source',
        'utm_medium' => 'UTM medium',
        'utm_campaign' => 'UTM campaign',
        'utm_term' => 'UTM term',
        'utm_content' => 'UTM content',
        'notified_at' => 'Notification queued (IST)',
    ];

    public static function download(Collection|LazyCollection $records, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens accented names correctly rather than as mojibake.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, array_values(self::COLUMNS));

            foreach ($records as $lead) {
                fputcsv($out, self::row($lead));
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** @return list<string> */
    public static function row(Lead $lead): array
    {
        $values = [];

        foreach (array_keys(self::COLUMNS) as $column) {
            $value = $lead->{$column};

            if ($value instanceof \DateTimeInterface) {
                $value = $value->setTimezone(new \DateTimeZone('Asia/Kolkata'))->format('Y-m-d H:i:s');
            }

            $values[] = self::neutralise((string) ($value ?? ''));
        }

        return $values;
    }

    /**
     * Blunt a CSV injection attempt.
     *
     * A lead's name or company is attacker-controlled text that an admin will
     * very likely open in Excel. A cell beginning =, +, - or @ is treated as a
     * formula there, so prefix it with a tab to force it to be read as text.
     * This is exactly the finding UB Shield would write up in someone else's
     * export, so the site should not ship it.
     */
    private static function neutralise(string $value): string
    {
        return $value !== '' && str_contains("=+-@\t\r", $value[0])
            ? "\t".$value
            : $value;
    }
}
