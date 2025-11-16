<?php
$sidebar_items = require __DIR__ . '/../../config/sidebar_items.php';
$user_type = $_SESSION['user_type'] ?? 'admin';
$departement = $_SESSION['nom_departement'] ?? null;

if ($user_type === 'admin') {
    $menu_key = $departement ?? 'admin';
} else {
    $menu_key = $departement ?? 'employe';
}

$current_path = $_SERVER['REQUEST_URI'];
$items = $sidebar_items[$menu_key] ?? [];
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2 class="sidebar-logo">
            <i data-feather="hexagon"></i>
            <span><?= $user_type === 'admin' ? 'RH Admin' : 'Mon Espace' ?></span>
        </h2>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <?php foreach ($items as $item): ?>
                <?php 
                    $is_active = strpos($current_path, $item['link']) === 0;
                    $has_sub_items = !empty($item['sub_items']);
                ?>
                <li class="menu-item <?= $is_active ? 'active' : '' ?> <?= $has_sub_items ? 'has-submenu' : '' ?>">
                    <a href="<?= $item['link'] ?>" class="menu-link" <?= $item['link'] === '#chatbotModal' ? 'data-bs-toggle="modal"' : '' ?>>
                        <i data-feather="<?= $item['icon'] ?>"></i>
                        <span><?= $item['label'] ?></span>
                        <?php if ($has_sub_items): ?>
                            <i data-feather="chevron-down" class="submenu-arrow"></i>
                        <?php endif; ?>
                    </a>
                    
                    <?php if ($has_sub_items): ?>
                        <ul class="submenu <?= $is_active ? 'show' : '' ?>">
                            <?php foreach ($item['sub_items'] as $sub_item): ?>
                                <?php $sub_active = $current_path === $sub_item['link']; ?>
                                <li class="submenu-item <?= $sub_active ? 'active' : '' ?>">
                                    <a href="<?= $sub_item['link'] ?>"><?= $sub_item['label'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="/logout" class="logout-btn">
            <i data-feather="log-out"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>
