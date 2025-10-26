<?php

require("config.php");
require("db.php");
require("src/functions/all.php");
require(ROOT . "src/functions/productsHandle.php");

$title = "GeekPrint | Каталог";
$errors = [];
$successMessage = '';

$products = R::findAll('products');
$reviews = R::findAll('avito_reviews');

// PHP ЛОГИКА: ВЫБОРКА ДАННЫХ ДЛЯ КАТАЛОГА
$categories = R::findAll('categories', 'ORDER BY id DESC');
$new_products = R::find('products', 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY created_at DESC');

$selectedCategoryId = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? intval($_GET['category_id']) : null;

$whereCAT = '';
$params = [];

if ($selectedCategoryId) {
    $whereCAT .= ' WHERE category_id = ?';
    $params[] = $selectedCategoryId;
}

// Выборка всех товаров (с фильтром категории если есть)
$products = R::findAll('products', $whereCAT . ' ORDER BY created_at DESC', $params);

// Выборка новинок за 30 дней с фильтром категории если есть
$newProductsWhere = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
if ($selectedCategoryId) {
    $newProductsWhere .= ' AND category_id = ?';
}
$new_products = R::find('products', $newProductsWhere . ' ORDER BY created_at DESC', $params);

// Массив для быстрого поиска имени категории по ID (для шаблонов)
$categoriesById = [];
foreach ($categories as $category) {
    $categoriesById[$category->id] = $category->name;
}

require_once(ROOT . "templates/head.php");
require_once(ROOT . "templates/header.php");
?>
    <main>
        <section class="container pt-5 pb-5 mt-5">
            <h1 class="text-center mb-5 fw-bold">Наш Каталог</h1>
            <?php if (is_admin()):?>
                <div class="py-4 d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
                    <button type="button" class="btn btn-lg btn-success main-btn shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        ✨ Добавить товар
                    </button>
                    
                    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                        <?php 
                            $id = htmlspecialchars($_GET['id'] ?? 'N/A');
                            $msg = isset($_POST['edit_product']) ? "Изменения сохранены для товара ID: $id" : "Товар успешно добавлен с ID: $id";
                            echo "<div class='alert alert-success alert-dismissible fade show mb-0' role='alert'>
                                    <strong>Успешно!</strong> $msg
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>";
                        ?>
                    <?php endif; ?>
                </div>
            <?php endif;?>
            <?php if (!empty($errors)): ?>
                <div class="row justify-content-center mb-4">
                    <div class="col-md-8">
                        <?php foreach($errors as $error): ?>
                            <div class="alert alert-danger mx-3" role="alert"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="get" id="categoryFilterForm" class="d-flex align-items-center justify-content-center gap-3 mb-5 p-3 bg-light rounded-3 shadow-sm">
                <label for="categoryFilter" class="mb-0 fw-semibold text-dark">Фильтр по категории:</label>
                <select name="category_id" id="categoryFilter" class="form-select" style="max-width: 250px;">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat->id ?>" <?= ($selectedCategoryId == $cat->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                </form>
            
            
            <?php if (!empty($new_products)): ?>
                <h2 id="new" class="text-center mb-1 mt-5 fw-bold">
                    🔥 <span class="text-warning">Новинки</span> Каталога!
                </h2>
                <h5 class="text-center mb-4 text-secondary">Не упустите момент.</h5>
                <div class="row justify-content-center g-4">
                    <?php foreach($new_products as $product) {
                        $currentCategoryName = $categoriesById[$product->category_id] ?? 'Без категории';
                        include(ROOT . "templates/product-short.php"); // Используем product-short.php
                    } ?>
                </div>
                <hr class="my-5">
            <?php endif; ?>

            <h2 class="text-center mb-4 fw-bold">Все товары</h2>
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center" role="alert">
                    Товаров в этой категории не найдено.
                </div>
            <?php else: ?>
                <div class="row justify-content-center g-4">
                    <?php foreach($products as $product) {
                        $currentCategoryName = $categoriesById[$product->category_id] ?? 'Без категории';
                        include(ROOT . "templates/product-short.php"); // Используем product-short.php
                    } ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
<?require_once(ROOT . "templates/footer.php");?>