<?php

if (!function_exists('normalizeMonth')) {
  function normalizeMonth(string $value): ?string
  {
    if (preg_match('/^(\d{4}-\d{2})(-\d{2})?$/', trim($value), $match) !== 1) {
      return null;
    }

    $ts = strtotime($match[1] . ($match[2] ?? '-01'));
    return $ts === false ? null : date('Y-m-01', $ts);
  }
}

if (!function_exists('getBudgetProgress')) {
  function getBudgetProgress(float $amount, float $spent): array
  {
    $progress = $amount > 0 ? ($spent / $amount) * 100 : 0;
    $progressClamped = max(0, min(100, $progress));
    $barClass = 'bg-green-500';

    if ($progress >= 80 && $progress <= 100) {
      $barClass = 'bg-yellow-500';
    } elseif ($progress > 100) {
      $barClass = 'bg-red-500';
    }

    return [
      'progress' => $progress,
      'progressClamped' => $progressClamped,
      'barClass' => $barClass,
    ];
  }
}
