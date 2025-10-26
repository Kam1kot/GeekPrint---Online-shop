<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function cart_count(): int {
    // Используем оператор объединения с null (??) для безопасного доступа к $_SESSION['cart']
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        return 0;
    }
    // Используем array_column для получения всех значений 'qty' и array_sum для их суммирования
    return array_sum(array_column($cart, 'qty'));
}
?>

<body>
    <header>
        <nav id="mainNav" class="navbar navbar-expand-lg bg-white shadow-sm px-5 fixed-top"> <!-- fixed-top -->
            <a class="logo navbar-brand text-warning fw-bold" href="/">
                <div class="navbar-brand">
                    <img src="<?= HOST ?>src/imgs/logo.png" alt="GeekPrint" width="max-width" height="35" class="logo d-inline-block align-text-top">
                </div>
                <h1>GeekPrint</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?=HOST?>catalog.php">Каталог</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?=HOST?>catalog.php#new">Новинки</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#about-us">О нас</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#reviews">Отзывы</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#about-us">Контакты</a></li>
                </ul>
                <div class="headbtns d-flex gap-3 align-items-center position-relative z-1">
                    <?php if (is_admin()):?>
                        <a href="<?=HOST?>templates\admin\logout.php">Выход</a>
                    <?php endif;?>
                    <button id="cartBtn" class="btn position-relative z-1" type="button" data-bs-toggle="modal" data-bs-target="#cartModal" aria-label="Открыть корзину">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2"><path d="M5 7h13.79a2 2 0 0 1 1.99 2.199l-.6 6A2 2 0 0 1 18.19 17H8.64a2 2 0 0 1-1.962-1.608z"/><path stroke-linecap="round" d="m5 7l-.81-3.243A1 1 0 0 0 3.22 3H2m6 18h2m6 0h2"/></g></svg>
                        <span id="cartCount" class="position-absolute z-1 top-0 start-75 translate-middle badge rounded-pill bg-danger"><?php echo cart_count(); ?></span>
                    </button>
                    <button id="searchBtn" class="btn btn-outline-secondary me-2">🔍</button>
                </div>
            </div>
        </nav>
        <div class="search-panel" id="searchPanel">
            <div class="search-input-container">
                <input type="text" id="searchInput" name="q" placeholder="Поиск товаров...">
            </div>
            <div class="search-dropdown" id="searchDropdown">
            </div>
        </div>
    </header>
    <script src="<?= HOST ?>src/js/search-logic.js"></script>
    