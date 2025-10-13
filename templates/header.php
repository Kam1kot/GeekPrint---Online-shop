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
        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-cart modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cartModalLabel">Ваша корзина</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>


                    <div class="modal-body">
                        <div id="cartEmpty" class="text-center text-muted my-4 d-none">
                            Корзина пуста
                            <div class="mt-3">
                            <a href="/catalog.php" class="btn btn-outline-primary">Перейти в каталог</a>
                        </div>
                    </div>


                    <div id="cartList" class="list-group mb-3"></div>


                    <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="fs-5">Итого: <span id="cartTotal" class="fw-bold">0,00 ₽</span></div>
                        <button id="clearCartBtn" type="button" class="btn btn-outline-danger">Очистить корзину</button>
                    </div>


                    <hr class="my-3"/>


                    <form id="orderForm" class="row g-3">
                        <div class="col-md-6">
                        <label for="orderName" class="form-label">Имя <strong>*</strong></label>
                            <input type="text" class="form-control" id="orderName" name="name" placeholder="Ваше имя" required>
                        </div>
                        <div class="col-md-6">
                            <label for="orderPhone" class="form-label">Номер телефона <strong>*</strong></label>
                            <input id="orderPhone" name="phone" type="tel" class="form-control" pattern="^\+?[0-9\s\-()]{6,}$" placeholder="+7 999 123-45-67" required>
                        </div>
                        <div class="col-12">
                            <label for="orderComment" class="form-label">Комментарий</label>
                            <textarea class="form-control" id="orderComment" name="comment" rows="3" placeholder="Пожелания по заказу"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="pickupCheck" name="pickup">
                                <label class="form-check-label" for="pickupCheck">
                                Самовывоз
                                </label>
                            </div>
                        </div>
                        <div id="deliveryBlock" class="col-12">
                            <label for="orderAddress" class="form-label">Адрес доставки <strong>*</strong></label>
                            <input type="text" class="form-control mb-3" id="orderAddress" name="address" placeholder="Введите адрес или выберите точку на карте">

                            <div id="deliveryMap" style="width: 100%; height: 300px; border-radius: 10px;"></div>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <h5 class="alert-heading mb-1">Как происходит оплата товаров:</h5>
                            <ol class="mb-0">
                                <li>Вы оформляете заказ.</li>
                                <li>Продавцу приходит уведомление с вашими контактами и корзиной.</li>
                                <li>Продавец связывается с вами для подтверждения заказа и оплаты.</li>
                                <li>После успешной оплаты продавец отправляет вам товары по прикрепленному адресу.</li>
                                <li>Товар будет у вас в течение x-n дней!</li>
                            </ol>
                            <div class="form-check ms-2 mt-2 mb-1">
                                <input class="form-check-input" type="checkbox" id="checkedCheck" name="checked" required>
                                <label class="form-check-label" for="pickupCheck">
                                С условиями оплаты заказов <span class="text-decoration-underline">ознакомлен</span>.
                                </label>
                            </div>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Заказать</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <script src="<?= HOST ?>src/js/search-logic.js"></script>
    