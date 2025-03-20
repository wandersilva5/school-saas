<?php
use App\Services\MenuService;
use App\Helpers\AuthRoleHelper;

// Get current user roles
$userRoles = $_SESSION['user']['roles'] ?? [];

// Initialize menu service
$menuService = new MenuService();

// Get menus for the user's roles
$userMenus = $menuService->getUserMenu($userRoles);

// Determine current page from URL
$currentRoute = $currentRoute ?? '';
if (empty($currentRoute)) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $currentRoute = trim($requestUri, '/');
    // Handle homepage
    if (empty($currentRoute)) {
        $currentRoute = 'dashboard';
    }
}

// Group menus by header
$menusByHeader = [];
foreach ($userMenus as $menu) {
    if (!isset($menu['active']) || $menu['active'] == 1) { // Only include active menus
        $menusByHeader[$menu['header']][] = $menu;
    }
}

// Sort each header group by order_index
foreach ($menusByHeader as &$headerMenus) {
    usort($headerMenus, function($a, $b) {
        return $a['order_index'] - $b['order_index'];
    });
}

// Define the header order
$headerOrder = [
    'Principal',
    'Acadêmico',
    'Administração'
];

// Create a new ordered array based on the defined order
$orderedMenus = [];
// First add headers in the specified order
foreach ($headerOrder as $header) {
    if (isset($menusByHeader[$header])) {
        $orderedMenus[$header] = $menusByHeader[$header];
    }
}
// Then add any remaining headers
foreach ($menusByHeader as $header => $menus) {
    if (!in_array($header, $headerOrder)) {
        $orderedMenus[$header] = $menus;
    }
}

// Replace the original array with the ordered one
$menusByHeader = $orderedMenus;
?>

<div class="d-flex flex-column flex-shrink-0 text-white">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-content">
            <!-- Institution logo/branding -->
            <div class="sidebar-brand">
                <div class="brand-content">
                    <?php
                    // Institution data
                    $institutionId = $_SESSION['user']['institution_id'] ?? null;
                    $db = \App\Config\Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT name, logo_url FROM institutions WHERE id = ?");
                    $stmt->execute([$institutionId]);
                    $institution = $stmt->fetch(\PDO::FETCH_ASSOC);
                    ?>

                    <div class="institution-logo">
                        <?php if (isset($institution['logo_url']) && $institution['logo_url']): ?>
                            <img src="<?= htmlspecialchars($institution['logo_url']) ?>" alt="Logo" class="img-fluid">
                        <?php else: ?>
                            <i class="bi bi-building"></i>
                        <?php endif; ?>
                    </div>
                    <div class="institution-name">
                        <?= htmlspecialchars($institution['name'] ?? 'School SaaS') ?>
                    </div>
                </div>
            </div>

            <ul class="sidebar-nav">
                <?php foreach ($menusByHeader as $header => $menus): ?>
                    <li class="sidebar-header">
                        <?= htmlspecialchars($header) ?>
                    </li>
                    
                    <?php foreach ($menus as $menu): ?>
                        <?php 
                        // Check if current page matches this menu's route
                        $isActive = (strpos($currentRoute, $menu['route']) === 0) ? 'active' : '';
                        ?>
                        <li class="sidebar-item <?= $isActive ?>">
                            <a class="sidebar-link" href="<?= htmlspecialchars($menu['url']) ?>">
                                <i class="bi <?= htmlspecialchars($menu['icon']) ?>"></i>
                                <span><?= htmlspecialchars($menu['name']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</div>