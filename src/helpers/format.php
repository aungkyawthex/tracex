<?php

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
