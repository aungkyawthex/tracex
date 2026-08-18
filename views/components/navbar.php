<?php
    require_once __DIR__ . '/../../src/helpers/csrf.php';

    $userName = trim((string) ($_SESSION['user_name'] ?? 'Guest'));
    $nameParts = preg_split('/\s+/', $userName) ?: [];
    $initials = '';
    foreach ($nameParts as $part) {
        if ($part === '') {
            continue;
        }
        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) {
            break;
        }
    }
    if ($initials === '') {
        $initials = 'G';
    }
?>
<header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center">
        <button id="mobile-sidebar-toggle" type="button" class="text-gray-500 focus:outline-none md:hidden cursor-pointer" aria-label="Open sidebar" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="flex items-center">
        <!-- dropdown -->
        <div class="relative ml-3" id="user-dropdown">
          <div>
            <button type="button" id="user-menu-button" class="flex items-center focus:outline-none cursor-pointer">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-500 text-white text-xs font-semibold"><?= htmlspecialchars($initials) ?></span>
              <span class="ml-2 text-sm font-medium text-gray-700"><?= htmlspecialchars($userName) ?></span>
              <i class="fas fa-chevron-down ml-1 text-gray-500 text-xs"></i>
            </button>
          </div>
            
            <!-- dropdown menu -->
            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 focus:outline-none" role="menu">
                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                    <i class="fas fa-user mr-2"></i> Account
                </a>
                <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a> -->
                <div class="border-t border-gray-100"></div>
                <button onclick="openLogoutModal()" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" role="menuitem">
                    <i class="fas fa-sign-out-alt mr-2"></i> Signout
                </button>
            </div>
        </div>
    </div>
</header>

<!-- logout modal -->
<div id="logoutModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
  <div class="flex min-h-screen items-end justify-center px-3 pt-4 pb-4 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <!-- Modal -->
    <div class="inline-block w-full max-w-md align-bottom bg-white rounded-t-xl sm:rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10 max-h-screen">
      <!-- Content -->
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
          <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
            <i class="fas fa-sign-out-alt text-red-500"></i>
          </div>
          <!-- Text -->
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Sign Out
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500">
                Are you sure you want to sign out? You will need to sign in again to access your account.
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- Actions -->
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <form action="<?= url('src/helpers/logout.php') ?>" method="POST" class="w-full sm:w-auto sm:ml-3">
          <?= csrfField() ?>
          <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2
            bg-red-600 text-base font-medium text-white hover:bg-red-700
            focus:outline-none sm:w-auto sm:text-sm cursor-pointer">
            Signout
          </button>
        </form>

        <button
          onclick="closeLogoutModal()"
          type="button"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2
            bg-white text-base font-medium text-gray-700 hover:bg-gray-50
            focus:outline-none
            sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<script>
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileSidebarPanel = document.getElementById('mobile-sidebar-panel');
    const mobileSidebarBackdrop = document.getElementById('mobile-sidebar-backdrop');
    const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
    const mobileSidebarLinks = document.querySelectorAll('.mobile-sidebar-link');

    function openMobileSidebar() {
        if (!mobileSidebar || !mobileSidebarPanel || !mobileSidebarBackdrop) return;
        mobileSidebar.classList.remove('pointer-events-none');
        mobileSidebarBackdrop.classList.remove('opacity-0');
        mobileSidebarBackdrop.classList.add('opacity-100');
        mobileSidebarPanel.classList.remove('-translate-x-full');
        mobileSidebarPanel.classList.add('translate-x-0');
        if (mobileSidebarToggle) {
            mobileSidebarToggle.setAttribute('aria-expanded', 'true');
        }
    }

    function closeMobileSidebar() {
        if (!mobileSidebar || !mobileSidebarPanel || !mobileSidebarBackdrop) return;
        mobileSidebarBackdrop.classList.remove('opacity-100');
        mobileSidebarBackdrop.classList.add('opacity-0');
        mobileSidebarPanel.classList.remove('translate-x-0');
        mobileSidebarPanel.classList.add('-translate-x-full');
        window.setTimeout(() => {
            mobileSidebar.classList.add('pointer-events-none');
        }, 300);
        if (mobileSidebarToggle) {
            mobileSidebarToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            if (mobileSidebarToggle.getAttribute('aria-expanded') === 'true') {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });
    }

    if (mobileSidebarBackdrop) {
        mobileSidebarBackdrop.addEventListener('click', closeMobileSidebar);
    }

    if (mobileSidebarClose) {
        mobileSidebarClose.addEventListener('click', closeMobileSidebar);
    }

    mobileSidebarLinks.forEach((link) => {
        link.addEventListener('click', closeMobileSidebar);
    });

    document.getElementById('user-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    });
    // close when click outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('user-dropdown');
        const button = document.getElementById('user-menu-button');
        const menu = document.getElementById('user-menu');
        
        if (!dropdown.contains(event.target) && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });
    function openLogoutModal() {
        document.getElementById('logoutModal').classList.remove('hidden');
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    }
    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.add('hidden');
    }
</script>
