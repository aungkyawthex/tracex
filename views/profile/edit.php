<form action="profile.php?edit=1" method="POST" class="max-w-2xl">
    <?= csrfField() ?>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                    <i class="fas fa-user-circle"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Profile details</p>
                    <p class="text-xs text-gray-500">Update your personal information</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 space-y-5">
            <?php if (!empty($updateErrors['update'])): ?>
                <p class="rounded-lg border border-red-400 bg-red-100 px-4 py-3 text-sm text-red-700"><?= htmlspecialchars($updateErrors['update']) ?></p>
            <?php endif; ?>
            <div>
                <label class="block text-xs font-semibold tracking-wide text-gray-500 uppercase" for="profile-name">Full name</label>
                <div class="mt-2">
                    <input type="text"
                        id="profile-name"
                        name="name"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                    />
                </div>
                <?php if (!empty($updateErrors['name'])): ?>
                    <p class="text-red-500 text-xs italic mt-2"><?= htmlspecialchars($updateErrors['name']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-xs font-semibold tracking-wide text-gray-500 uppercase" for="profile-email">Email address</label>
                <div class="mt-2">
                    <input type="email"
                        id="profile-email"
                        name="email"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                    />
                </div>
                <?php if (!empty($updateErrors['email'])): ?>
                    <p class="text-red-500 text-xs italic mt-2"><?= htmlspecialchars($updateErrors['email']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 rounded-b-2xl">
            <div class="flex flex-wrap gap-3">
                <a href="profile.php" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-white cursor-pointer">Cancel</a>
                <button type="submit" name="btnUpdateProfile" class="px-5 py-2 rounded-lg bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700 cursor-pointer">Save changes</button>
            </div>
        </div>
    </div>
</form>
