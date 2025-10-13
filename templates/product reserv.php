<?PHP
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/db.php');
require_once(ROOT . 'src/functions/all.php');




if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Загружаем товар по ID с помощью RedBean
    $product = R::load('products', $id);
    // Проверяем, существует ли товар
    if ($product->id) {
        
    } else {
        // Товар не найден, можно вывести сообщение об ошибке 404
        echo "Товар не найден!";
    }
} else {
    // ID не передан, выводим ошибку
    echo "ID товара отсутствует.";
}

// Название + игрушка
$title = "GeekPrint | " . $product['title'];

// Загружаем случайные товары (кроме текущего)
$otherProducts = R::findAll(
    'products',
    ' id != ? ORDER BY RAND() LIMIT 6 ',
    [$product->id]
);
$catPrd = $product['category_id'];
$currentCat = R::findOne('categories', ' id = ? ',[$product->category_id]);
$categories = R::findAll('categories', 'ORDER BY id DESC');

echo $catPrd, $currentCat;

require_once(ROOT . "templates/head.php");
require_once(ROOT . "templates/header.php");

?>
    <main class="container">
        <section style="margin-top:10rem" class="d-flex flex-column align-items-center justify-content-center">
            <div class="mb-4 position-relative col-1 prd-wrap shadow-lg border border-secondary-subtle">
                <div class="decor-sq-1 position-absolute border border-secondary-subtle"></div>
                <div class="decor-sq-2 position-absolute border border-secondary-subtle"></div>
                <img src="<?= HOST ?>src/data/product_covers/<?= htmlspecialchars($product['cover_name']) ?>" alt="Товар" class="prd-img img-fluid mb-2 position-absolute top-50 start-50 translate-middle">
            </div>
            <div class="mb-5 mt-5 col-1 fs-3 fw-bold">
                <div class="prd-info d-flex flex-column align-items-center">
                    <span><?= $product['title'] ?></span>
                    <h5 class="my-1 fs-6 text-muted"><?= $currentCat['name']?></h5>
                    <span class="fs-4 text-secondary"><?= $product['price'] ?> ₽</span>
                    <span><?= $product['desc'] ?></span>
                    <button class="btn btn-sm btn-outline-primary add-to-cart"
                            data-id="<?= (int)$product['id'] ?>"
                            data-cover="<?= htmlspecialchars($product['cover_name'], ENT_QUOTES) ?>"
                            data-title="<?= htmlspecialchars($product['title'], ENT_QUOTES) ?>"
                            data-price="<?= number_format((float)$product['price'], 2, '.', '') ?>">
                        В корзину
                    </button>
                </div>
            </div>
            <div class="fs-5 fw-normal w-100 other mt-5">
                Также можете посмотреть:
                <div id="otherProductsCarousel" class="carousel slide mt-3" data-bs-ride="carousel">
  <div class="carousel-inner">

    <?php foreach (array_chunk($otherProducts, 3) as $index => $productChunk): ?>
      <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
        <div class="row justify-content-center">
            <?
            $categoriesById = [];
            foreach ($categories as $category) {
                $categoriesById[$category->id] = $category->name; // Store category name by ID
            }
            ?>
            <?php foreach ($productChunk as $prd): ?>
                <?$currentCategoryName = $categoriesById[$product->category_id] ?? 'Без категории';?>
            <div class="col-md-3 mb-5">
                <div class="card shadow-sm border-0 h-100">
                <a href="product.php?id=<?= $prd['id'] ?>" class="text-decoration-none">
                    <img src="<?= HOST ?>src/data/product_covers/<?= htmlspecialchars($prd['cover_name']) ?>" 
                        class="small-img-carousel card-img-top img-fluid" 
                        alt="<?= htmlspecialchars($prd['title']) ?>">
                    <div class="card-body text-center">
                    <h5 class="card-title fs-4"><?= htmlspecialchars($prd['title']) ?></h5>
                    <h5 class="text-muted"><?= htmlspecialchars($currentCategoryName) ?></h5>
                    <p class="card-text text-secondary"><?= $prd['price'] ?> ₽</p>
                    </div>
                </a>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
      </div>
    <?php endforeach; ?>

  </div>

  <!-- Навигация -->
  <button class="carousel-control-prev" type="button" data-bs-target="#otherProductsCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#otherProductsCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>
            </div>
        </section>
    </main>
<?require_once(ROOT . "templates/footer.php");?>