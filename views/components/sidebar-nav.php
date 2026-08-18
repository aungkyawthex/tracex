<?php $extraClass = $extraClass ? $extraClass . ' ' : ''; ?>
<?php foreach ($navItems as $navItem): ?>
        <a href="<?= url($navItem['path']) ?>" class="<?= $extraClass ?>flex items-center px-2 py-3 text-sm font-medium <?= isActive($navItem['path']) ? 'text-white bg-blue-700' : 'text-indigo-200 hover:text-white hover:bg-blue-700' ?> rounded-lg">
          <i class="<?= $navItem['icon'] ?> mr-3"></i>
          <?= $navItem['label'] ?>
        </a>
<?php endforeach; ?>
