<?php
/**
 * TraceX brand lockup.
 * Set $variant: 'dark' (default) for light backgrounds, 'light' for dark backgrounds.
 * Set $size: 'sm' | 'md' (default) | 'lg'.
 */
$variant = $variant ?? 'dark';
$size = $size ?? 'md';

$tile = ['sm' => 'h-7 w-7 rounded-md', 'md' => 'h-8 w-8 rounded-lg', 'lg' => 'h-9 w-9 rounded-lg'][$size];
$icon = ['sm' => 'text-xs', 'md' => 'text-sm', 'lg' => 'text-base'][$size];
$text = ['sm' => 'text-base', 'md' => 'text-lg', 'lg' => 'text-xl'][$size];

$markColor = $variant === 'light' ? 'text-white' : 'text-gray-900';
$accentColor = $variant === 'light' ? 'text-blue-300' : 'text-blue-600';
?>
<span class="inline-flex items-center gap-2 select-none">
    <span class="flex <?= $tile ?> shrink-0 items-center justify-center bg-blue-600 text-white shadow-sm">
        <i class="fa-solid fa-wallet <?= $icon ?>"></i>
    </span>
    <span class="<?= $text ?> font-extrabold tracking-tight <?= $markColor ?>">Trace<span class="<?= $accentColor ?>">X</span></span>
</span>
