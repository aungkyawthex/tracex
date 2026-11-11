<?php
/** Alert box. $type: 'error' (default) or 'success'. */
$type = $type ?? 'error';
$message = $message ?? '';

$style = $type === 'success'
    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
    : 'border-red-200 bg-red-50 text-red-700';
?>
<div class="mb-6 rounded-lg border <?= $style ?> px-4 py-3 text-sm" role="alert">
    <?= htmlspecialchars($message) ?>
</div>
