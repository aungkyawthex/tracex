<?php
/**
 * Input field. Required: $id, $name, $label, $type.
 * Optional: $placeholder, $icon, $value, $error, $autocomplete,
 * $required (default true), $minlength, $toggle, $labelExtra (raw HTML after label).
 */
$id = $id ?? '';
$name = $name ?? $id;
$label = $label ?? '';
$type = $type ?? 'text';
$placeholder = $placeholder ?? '';
$icon = $icon ?? '';
$value = $value ?? '';
$error = $error ?? null;
$autocomplete = $autocomplete ?? 'off';
$required = $required ?? true;
$minlength = $minlength ?? null;
$toggle = $toggle ?? false;
$labelExtra = $labelExtra ?? '';

$border = $error
    ? 'border-red-400 focus:border-red-400 focus:ring-red-100'
    : 'border-gray-300 focus:border-blue-500 focus:ring-blue-100';
?>
<div>
    <?php if ($labelExtra): ?>
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-gray-700" for="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($label) ?></label>
            <?= $labelExtra ?>
        </div>
    <?php else: ?>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($label) ?></label>
    <?php endif; ?>
    <div class="relative">
        <?php if ($icon): ?>
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="<?= htmlspecialchars($icon) ?>"></i>
            </span>
        <?php endif; ?>
        <input class="block w-full rounded-lg border bg-white py-2.5 <?= $icon ? 'pl-10' : 'pl-3' ?> <?= $toggle ? 'pr-10' : 'pr-3' ?> text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 <?= $border ?>"
            id="<?= htmlspecialchars($id) ?>"
            type="<?= htmlspecialchars($type) ?>"
            name="<?= htmlspecialchars($name) ?>"
            autocomplete="<?= htmlspecialchars($autocomplete) ?>"
            <?= $required ? 'required' : '' ?>
            <?= $minlength !== null ? 'minlength="' . (int) $minlength . '"' : '' ?>
            placeholder="<?= htmlspecialchars($placeholder) ?>"
            value="<?= htmlspecialchars($value) ?>">
        <?php if ($toggle): ?>
            <button type="button" data-password-toggle="<?= htmlspecialchars($id) ?>" aria-label="Show password" aria-pressed="false"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                <i class="fa-solid fa-eye toggle-show"></i>
                <i class="fa-solid fa-eye-slash toggle-hide" style="display:none"></i>
            </button>
        <?php endif; ?>
    </div>
    <?php if ($error): ?>
        <p class="text-red-600 text-xs mt-1"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>
