<?PHP

use RedBeanPHP\Util\Dump;

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require(ROOT . "db.php");
require_once(ROOT . 'src/functions/all.php');
require(ROOT . "src/functions/productsHandle.php");

$product = null;
$currentCat = null;
$title = "GeekPrint | Товар"; // Значение по умолчанию
$otherProducts = [];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Загружаем товар по ID с помощью RedBean
    $product = R::load('products', $id);

    // Проверяем, существует ли товар
    if (!$product->id) {
        die("<h1>404</h1><p>Товар не найден!</p>");
    }

    $title = "GeekPrint | " . $product['title'];

    // Загружаем категорию
    if ($product->category_id) {
        $currentCat = R::findOne('categories', ' id = ? ', [$product->category_id]);
    }

    $allCategories = R::findAll('categories', 'ORDER BY id DESC');
    $categoriesById = [];
    foreach ($allCategories as $category) {
        $categoriesById[$category->id] = $category->name;
    }
    // Загружаем случайные товары (кроме текущего)
    $otherProducts = R::findAll(
        'products',
        ' id != ? ORDER BY RAND() LIMIT 6 ',
        [$product->id]
    );
} else {
    // ID не передан, выводим ошибку
    die("<h1>Ошибка</h1><p>ID товара отсутствует.</p>");
}

require_once(ROOT . "templates/head.php");
require_once(ROOT . "templates/header.php");

$productID = (int)$product->id;
$productTitle = htmlspecialchars($product->title, ENT_QUOTES);
$productCover = htmlspecialchars($product->cover_name);
$productPrice = number_format((float)$product->price, 2, '.', '');
$productDesc = htmlspecialchars($product->desc);
$categoryName = $currentCat ? htmlspecialchars($currentCat->name) : 'Без категории';

?>
    <main class="container py-5">
        <section class="mt-5">
            <div class="row product-container justify-content-center">
                
                <div class="col-12 col-md-6 col-lg-5 mb-4 mb-md-0 product-image">
                    <img 
                        src="<?=HOST?>/src/data/product_covers/<?=$productCover?>" 
                        alt="<?=$productTitle?>" 
                        class="img-fluid p-img rounded-4 shadow-lg" 
                        style="max-height: 500px; object-fit: contain;"
                    >
                </div>
                
                <div class="col-12 col-md-6 col-lg-5 product-info ps-md-5">
                    
                    <h1 class="display-5 fw-bold mb-2"><?=$productTitle?></h1>
                    
                    <div class="text-muted fs-6 mb-3">
                        <p class="mb-0">Категория: <span class="fw-semibold text-primary"><?=$categoryName?></span></p>
                    </div>
                    
                    <hr class="my-3">

                    <div class="product-price mb-4">
                        <span class="fs-2 fw-bolder text-dark"><?=$productPrice?> ₽</span>
                    </div>
                    
                    <div class="product-description mb-4">
                        <?php if ($productDesc != null): ?>
                            <h2 class="fs-5 fw-semibold mb-2" style="color:gray">Описание товара</h2>
                            <p class="text-secondary"><?=$productDesc?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-actions d-flex align-items-center gap-3 mt-4">
                        
                        <button 
                            class="btn btn-primary btn-lg add-to-cart flex-grow-1"
                            data-id="<?= $productID ?>"
                            data-cover="<?= $productCover ?>"
                            data-title="<?= $productTitle ?>"
                            data-price="<?= $productPrice ?>">
                            В корзину
                        </button>
                        <?php if(is_admin()):?>
                            <button 
                                class="btn btn-outline-warning p-3" 
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
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </section>
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
        <hr class="my-5">

        <section class="other">
            <h2 class="fs-4 fw-bold mb-4">Другие товары</h2>
            <div class="swiperOP">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <?php foreach ($otherProducts as $product) {
                        $currentCategoryName = $categoriesById[$product->category_id] ?? 'Без категории';
                        include(ROOT . "templates/product-short-other.php"); // Используем product-short.php
                    } ?>
                </div>
                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
    </main>    
<?require_once(ROOT . "templates/footer.php");?>