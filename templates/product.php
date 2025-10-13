<?PHP

use RedBeanPHP\Util\Dump;

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once(ROOT . 'src/functions/all.php');

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

echo $currentCat;

require_once(ROOT . "templates/head.php");
require_once(ROOT . "templates/header.php");

// --- Переменные для удобства ---
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
                    <h2 class="fs-5 fw-semibold mb-2">Описание товара</h2>
                    <p class="text-secondary"><?=$productDesc?></p>
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
                    
                    <button 
                        class="btn btn-outline-warning p-3" 
                        title="Редактировать товар"
                        data-bs-toggle="modal" 
                        data-bs-target="#editProductModal<?= $productID ?>">
                        ⚙️
                    </button>
                </div>
            </div>
        </div>
    </section>

    <hr class="my-5">

    <section class="other">
        <h2 class="fs-4 fw-bold mb-4">Похожие и другие товары</h2>
        
        <div id="otherProductsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach (array_chunk($otherProducts, 3) as $index => $productChunk): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="row justify-content-center g-4">
                            <?php foreach ($productChunk as $prd): ?>
                                <?php
                                $prdCategoryName = $categoriesById[$prd->category_id] ?? 'Без категории';
                                $prdTitle = htmlspecialchars($prd->title);
                                $prdCover = htmlspecialchars($prd->cover_name);
                                ?>
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="card product-card shadow-sm border-0 h-100 rounded-4 p-0 overflow-hidden">
                                        <a href="product.php?id=<?= (int)$prd->id ?>" class="text-decoration-none">
                                            <img 
                                                src="<?= HOST ?>src/data/product_covers/<?= $prdCover ?>" 
                                                class="small-img-carousel card-img-top img-fluid" 
                                                alt="<?= $prdTitle ?>"
                                                style="max-height: 200px; object-fit: cover;"
                                            >
                                            <div class="card-body text-center d-flex flex-column">
                                                <h5 class="card-title titile-name fs-5 fw-semibold mb-1"><?= $prdTitle ?></h5>
                                                <h6 class="text-muted small mb-2 text-primary"><?= $prdCategoryName ?></h6>
                                                <p class="card-text text-dark fs-5 fw-bold mt-auto"><?= number_format((float)$prd->price, 2, '.', '') ?> ₽</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#otherProductsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#otherProductsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    </main>
    
    <?require_once(ROOT . "templates/footer.php");?>