<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Потоковый CSV для Excel: UTF-8 с BOM, разделитель «;».
 */
class Csv
{
    /**
     * @param  array<int, string>  $header
     * @param  callable(mixed): array<int, mixed>  $row
     */
    public static function download(string $filename, array $header, Builder $query, callable $row): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $query, $row) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $header, ';');
            $query->chunkById(500, function ($items) use ($out, $row) {
                foreach ($items as $item) {
                    fputcsv($out, $row($item), ';');
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public static function date(?\DateTimeInterface $date): string
    {
        return $date ? $date->copy()->timezone(config('app.display_timezone'))->format('d.m.Y H:i') : '';
    }
}
