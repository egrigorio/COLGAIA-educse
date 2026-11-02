<?php

namespace Educse\Services;

final class WorkloadService
{
    public static function workingDays(\DateTimeImmutable $start, \DateTimeImmutable $end, array $settings): int
    {
        if ($end < $start) { return 0; }
        $days = (int) $start->diff($end)->days + 1;
        $week = [];
        for ($i = 0; $i < 7; $i++) { $week[$i] = empty($settings['dia_' . $i]) ? 0 : 1; }
        $count = intdiv($days, 7) * array_sum($week);
        $offset = (int) $start->format('N') - 1;
        for ($i = 0; $i < $days % 7; $i++) { $count += $week[($offset + $i) % 7]; }
        return $count;
    }

    public static function dailyAverage(array $activity, array $settings): float
    {
        $days = self::workingDays(new \DateTimeImmutable(substr($activity['comeco'], 0, 10)), new \DateTimeImmutable(substr($activity['fim'], 0, 10)), $settings);
        return $days > 0 ? (float) $activity['tempo_sugerido'] / $days : 0.0;
    }
}
