<?php
// Примечание: Переменная $currentCategoryName должна быть определена ранее в основном скрипте.
$productID = (int)$product['id'];
$productTitle = htmlspecialchars($product['title'] ?? 'Название отсутствует');
$productCover = htmlspecialchars($product['cover_name'] ?? '');
$productPrice = htmlspecialchars($product['price'] ?? '0.00');
$formattedPrice = number_format((float)($product['price'] ?? 0), 2, '.', '');
$categoryDisplay = htmlspecialchars($currentCategoryName ?? 'Без категории');
?>

<article class="swiper-slide">
    <div class="product-card-other position-relative border-0 p-3 bg-white rounded-4 shadow-sm h-100 d-flex flex-column">
        <a href="<?=HOST?>templates/product.php?id=<?= $productID ?>" class="text-decoration-none d-block">
            <?php if (!empty($productCover)): ?>
                <img src="<?= HOST ?>src/data/product_covers/<?= $productCover ?>" alt="<?= $productTitle ?>" class="img-fluid small-img mb-2 rounded-3">
            <?php else: ?>
                <div class="small-img bg-light d-flex align-items-center justify-content-center mb-2 rounded-3 text-muted">Нет фото</div>
            <?php endif; ?>
            <hr>
            <h5 class="fs-6 text-muted mb-1 text-center text-truncate"><?= $categoryDisplay ?></h5>
            
            <h5 class="titile-name fs-5 fw-semibold mb-2 text-center text-dark text-truncate" title="<?= $productTitle ?>"><?= $productTitle ?></h5>
            
            <p class="price fs-4 fw-bolder text-center mt-auto mb-2"><?= $productPrice ?> ₽</p>
        </a>

        <div class="product-actions d-flex gap-2 justify-content-center mt-2 pt-2 border-top">
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
        </div>
    </div>
</article>