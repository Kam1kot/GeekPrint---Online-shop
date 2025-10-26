<?php

// --- PHP-ЛОГИКА: ПРОВЕРКА НОВИЗНЫ ---
$newDays = 30;
$isNew = false;

// Проверяем, существует ли дата создания и является ли она допустимой
if (!empty($product['created_at'])) {
    try {
        $created = new DateTime($product['created_at']);
        $now = new DateTime();
        $interval = $now->diff($created);
        
        // Товар считается новинкой, если он создан не более $newDays дней назад
        if ($interval->days < $newDays && $interval->invert === 0) {
             $isNew = true;
        }
    } catch (Exception $e) {
        // Ошибка парсинга даты, игнорируем
    }
}

// Примечание: Переменная $currentCategoryName должна быть определена ранее в основном скрипте.
$productID = (int)$product['id'];
$productTitle = htmlspecialchars($product['title'] ?? 'Название отсутствует');
$productCover = htmlspecialchars($product['cover_name'] ?? '');
$productPrice = htmlspecialchars($product['price'] ?? '0.00');
$formattedPrice = number_format((float)($product['price'] ?? 0), 2, '.', '');
$categoryDisplay = htmlspecialchars($currentCategoryName ?? 'Без категории');
?>

<article class="col-6 col-sm-6 col-md-4 col-lg-3 mb-4 <?= $index === 0 ? 'active' : '' ?>">
    <div class="product-card position-relative border-0 p-3 bg-white rounded-4 shadow-sm h-100 d-flex flex-column">
        <a href="<?=HOST?>templates/product.php?id=<?= $productID ?>" class="text-decoration-none d-flex flex-column flex-grow-1">
            <?php if ($isNew): ?>
                <div class="new-badge" aria-label="Новинка" title="Новинка">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1024 1024"><path fill="currentColor" d="M834.1 469.2A347.49 347.49 0 0 0 751.2 354l-29.1-26.7a8.09 8.09 0 0 0-13 3.3l-13 37.3c-8.1 23.4-23 47.3-44.1 70.8c-1.4 1.5-3 1.9-4.1 2c-1.1.1-2.8-.1-4.3-1.5c-1.4-1.2-2.1-3-2-4.8c3.7-60.2-14.3-128.1-53.7-202C555.3 171 510 123.1 453.4 89.7l-41.3-24.3c-5.4-3.2-12.3 1-12 7.3l2.2 48c1.5 32.8-2.3 61.8-11.3 85.9c-11 29.5-26.8 56.9-47 81.5a295.64 295.64 0 0 1-47.5 46.1a352.6 352.6 0 0 0-100.3 121.5A347.75 347.75 0 0 0 160 610c0 47.2 9.3 92.9 27.7 136a349.4 349.4 0 0 0 75.5 110.9c32.4 32 70 57.2 111.9 74.7C418.5 949.8 464.5 959 512 959s93.5-9.2 136.9-27.3A348.6 348.6 0 0 0 760.8 857c32.4-32 57.8-69.4 75.5-110.9a344.2 344.2 0 0 0 27.7-136c0-48.8-10-96.2-29.9-140.9"/></svg>
                </div>
            <?php endif; ?>
            <?php if (!empty($productCover)): ?>
                <img src="<?= HOST ?>src/data/product_covers/<?= $productCover ?>" alt="<?= $productTitle ?>" class="img-fluid small-img mb-2 rounded-3">
            <?php else: ?>
                <div class="small-img bg-light d-flex align-items-center justify-content-center mb-2 rounded-3 text-muted">Нет фото</div>
            <?php endif; ?>
            <hr>
            <h5 class="fs-6 text-muted mb-1 text-center text-truncate"><?= $categoryDisplay ?></h5>
            
            <h5 class="titile-name fs-5 fw-semibold mb-2 text-center text-dark text-truncate" title="<?= $productTitle ?>"><?= $productTitle ?></h5>
            
            <p class="price fs-4 fw-bolder text-center mt-auto"><?= $productPrice ?> ₽</p>
        </a>
        
        <div class="product-actions d-flex gap-2 justify-content-center mt-auto pt-2 border-top">
            
            <button 
                class="btn btn-outline-success add-to-cart flex-fill"
                data-price="<?= $formattedPrice ?>" 
                data-title="<?= $productTitle ?>" 
                data-cover="<?= $productCover ?>" 
                data-id="<?= $productID ?>">
                <span class="d-none d-sm-inline">В корзину</span> 🛒
            </button>
            <?php if (is_admin()):?>
                <button 
                    class="btn btn-warning" 
                    title="Редактировать товар"
                    data-bs-toggle="modal" 
                    data-bs-target="#editProductModal<?= $productID ?>">
                    ⚙️
                </button>

                <form method="post" class="d-inline product-delete-form">
                    <input type="hidden" name="id" value="<?= $productID ?>">
                    <input type="hidden" name="cover" value="<?= $productCover ?>">
                    <button class="btn btn-danger confirm-delete-btn" title="Удалить товар">❌</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</article>
<!-- Редактирование товара -->
<!-- Не убирать, иначе не будет модалки редактирования у каждого товара -->
<div class="modal fade" 
    id="editProductModal<?= (int)$product['id'] ?>" 
    tabindex="-1" 
    aria-labelledby="editModalLabel<?= (int)$product['id'] ?>" 
    aria-hidden="true">
    
    <form method="post" enctype="multipart/form-data">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow-lg">

                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold" id="editModalLabel<?= (int)$product['id'] ?>">
                        Редактирование товара: <span class="text-primary"><?= htmlspecialchars($product['title']) ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label for="title-<?= (int)$product['id'] ?>" class="form-label fw-semibold">Название <span class="text-danger">*</span></label>
                        <input class="form-control" id="title-<?= (int)$product['id'] ?>" type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="category-<?= (int)$product['id'] ?>" class="form-label fw-semibold">Категория <span class="text-danger">*</span></label>
                        <select name="category_id" id="category-<?= (int)$product['id'] ?>" class="form-select">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= $product->category_id == $cat->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="desc-<?= (int)$product['id'] ?>" class="form-label fw-semibold">Описание</label>
                        <textarea class="form-control" id="desc-<?= (int)$product['id'] ?>" name="desc" rows="3"><?= htmlspecialchars($product['desc']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="price-<?= (int)$product['id'] ?>" class="form-label fw-semibold">Цена <span class="text-danger">*</span></label>
                        <input class="form-control" id="price-<?= (int)$product['id'] ?>" type="text" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="editCoverInput-<?= (int)$product['id'] ?>" class="form-label fw-semibold">Изображение</label>
                        <input class="form-control" 
                            type="file" 
                            name="cover" 
                            id="editCoverInput-<?= (int)$product['id'] ?>">
                    </div>
                    
                    <div class="mb-3 cover-preview-wrapper-<?= (int)$product['id'] ?>" style="display: none;">
                        <p class="mb-1 text-muted small">Превью нового изображения:</p>
                        <img src="" 
                            alt="Превью" 
                            id="editCoverPreview-<?= (int)$product['id'] ?>" 
                            class="img-fluid rounded shadow" 
                            style="max-width: 150px; display: block;">
                    </div>

                    <?php if(!empty($product['cover_name'])): ?>
                        <div class="mb-3 p-2 border rounded-3" id="cover-container-<?= (int)$product['id'] ?>">
                            <p class="mb-1 text-muted small">Текущее изображение:</p>
                            <div class="position-relative d-inline-block">
                                <img src="<?=HOST?>src/data/product_covers/<?= htmlspecialchars($product->cover_name) ?>" 
                                    alt="Текущая обложка" 
                                    class="img-fluid rounded" 
                                    style="max-width: 150px; display: block;">
                                
                                <button type="button" 
                                    class="btn btn-danger btn-sm delete-cover-btn p-1" 
                                    title="Удалить обложку"
                                    data-product-id="<?= (int)$product['id'] ?>"
                                    style="z-index: 10;">X
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="modal-footer border-top-0">
                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" name="edit_product" class="btn btn-primary">
                        Сохранить изменения
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>