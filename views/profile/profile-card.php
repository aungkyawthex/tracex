<div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
  <?php 
    $profileName = trim((string) ($user['name'] ?? ($_SESSION['user_name'] ?? 'Guest')));
    $profileParts = preg_split('/\s+/', $profileName) ?: [];
    $profileInitials = '';
    foreach ($profileParts as $part) {
      if ($part === '') {
        continue;
      }
      $profileInitials .= strtoupper(substr($part, 0, 1));
      if (strlen($profileInitials) >= 2) {
        break;
      }
    }
    if ($profileInitials === '') {
      $profileInitials = 'G';
    }

    if ($editMode):
      include __DIR__ . '/edit.php';
    else: 
  ?>
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
          <div class="h-14 w-14 rounded-full bg-blue-600 text-white text-xl font-semibold flex items-center justify-center shadow-sm">
            <?= htmlspecialchars($profileInitials) ?>
          </div>
          <div>
            <p class="text-lg font-semibold text-gray-900">
              <?= htmlspecialchars($user['name'] ?? 'Guest') ?>
            </p>
            <p class="text-sm text-gray-500">Account overview</p>
          </div>
        </div>
        <a href="<?= url('public/profile.php?edit') ?>" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
          <i class="fas fa-edit"></i>
          Edit profile
        </a>
      </div>
    </div>

    <div class="px-6 py-6 space-y-5">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Full name</p>
          <p class="mt-2 text-sm font-semibold text-gray-900">
            <?= htmlspecialchars($user['name'] ?? 'Guest') ?>
          </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email address</p>
          <p class="mt-2 text-sm font-semibold text-gray-900">
            <?= htmlspecialchars($user['email'] ?? 'No email provided') ?>
          </p>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Joined</p>
        <p class="mt-2 text-sm font-semibold text-gray-900">
          <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?>
        </p>
      </div>
    </div>
  <?php endif; ?>
</div>
