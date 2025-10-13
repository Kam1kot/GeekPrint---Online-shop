<?php
require("config.php");
require("db.php");
require("src/functions/all.php");

$title = "GeekPrint | Каталог";
$errors = [];
$successMessage = '';

$products = R::findAll('products');
$reviews = R::findAll('avito_reviews');

// Создание поста
if (isset($_POST['submit_product'])) {
    if (empty($_POST['title'])) {
        $errors[] = "Введите название!";
    }

    if (!is_numeric($_POST['price'])) {
        $errors[] = "Цена должны быть числом!";
    }

    if (empty($errors)) {
        // Изображние
        $coverName = null;
        if (!empty($_FILES['cover']['tmp_name'])) {

            // Проверка загруженной фотографии
            $checkRes = checkPhotoBeforeUpload();

            if (is_array($checkRes)) {
                $errors = $checkRes;
            } elseif ($checkRes === true) {

                // Откуда
                $sourcePath = $_FILES['cover']['tmp_name'];

                // Куда
                $extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                if ($extension === 'jpeg') $extension = 'jpg';

                $file_name = uniqid() . '.' . $extension;
                $result_path = ROOT . "src/data/covers/" . $file_name;
                $result_path_alt = ROOT . "src/data/product_covers/" . $file_name;

                // Кроп
                if (resizeImageByWidth($sourcePath, $result_path, $result_path_alt, 200)) {
                    if(resizeAndCrop($sourcePath, $result_path, $result_path_alt, 200, 200)) {
                        $coverName = $file_name;
                    }
                }

                // Создание поста
                $product = R::dispense('products');
                $product->created_at = date('Y-m-d H:i:s');
                $product->category_id = intval($_POST['category_id']);
                $product->title = htmlspecialchars(trim($_POST['title']));
                $product->desc = htmlspecialchars(trim($_POST['desc']));
                $product->price = floatval($_POST['price']);
                $product->cover_name = $coverName;
                $id = R::store($product);

                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&id=$id");
                exit();;
            }
        }
    }
}
// Удаление продукта
if (isset($_POST['product-delete'])) {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $product = R::load('products', $_POST['id']);
        if ($product['id'] == 0) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if (isset($_POST['product-delete'])) {
        unlink(ROOT . "src/data/covers/" . $_POST['cover']);
        unlink(ROOT . "src/data/product_covers/" . $_POST['cover']);
        R::trash($product);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Редактирование продукта
if (isset($_POST['edit_product'])) {
    $product = R::load('products', $_POST['id']);

    if (empty($_POST['title'])) {
        $errors[] = "Должно быть название!";
    }
    if (empty($_POST['price'])) {
        $errors[] = "Должна быть цена!";
    }
    if (empty($errors)) {
        // Изображние
        $coverName = $product['cover_name'];
        if (!empty($_FILES['cover']['tmp_name'])) {

            // Проверка загруженной фотографии
            $checkRes = checkPhotoBeforeUpload();

            if (is_array($checkRes)) {
                $errors = $checkRes;
            } elseif ($checkRes === true) {

                // Откуда
                $sourcePath = $_FILES['cover']['tmp_name'];

                // Куда
                $extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                if ($extension === 'jpeg') $extension = 'jpg';

                $file_name = uniqid() . '.' . $extension;
                $result_path = ROOT . "src/data/covers/" . $file_name;
                $result_path_alt = ROOT . "src\data\product_covers/" . $file_name;

                // Кроп
                if (resizeImageByWidth($sourcePath, $result_path, $result_path_alt, 200)) {
                    if(resizeAndCrop($sourcePath, $result_path, $result_path_alt, 200, 200)) {
                        unlink(ROOT . "src/data/covers/" . $coverName);
                        $coverName = $file_name;
                    }
                }
            }
        }
        // Перезапись поста
        $product->category_id = intval($_POST['category_id']);
        $product->title = htmlspecialchars(trim($_POST['title']));
        $product->desc = htmlspecialchars(trim($_POST['desc']));
        $product->price = floatval($_POST['price']);
        $product->cover_name = $coverName;
        R::store($product);

        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&id=$id");
        exit();
    } else {
        $errors ['Произошла ошибка'];
    }
}
// --- 4. AJAX УДАЛЕНИЕ ОБЛОЖКИ ---
if (isset($_POST['action']) && $_POST['action'] === 'delete_cover') {
    $response = ['success' => false, 'message' => ''];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id > 0) {
        $product = R::load('products', $id);
        if ($product->id !== 0) {
            $coverName = $product->cover_name;
            if (!empty($coverName)) {
                // Используем @unlink, чтобы избежать фатальных ошибок, если файл не существует
                @unlink(ROOT . "src/data/covers/" . $coverName);
                @unlink(ROOT . "src/data/product_covers/" . $coverName); 
                
                $product->cover_name = null;
                R::store($product);
                $response['success'] = true;
                $response['message'] = 'Изображение успешно удалено.';
            } else {
                $response['message'] = 'Изображение для удаления не найдено.';
            }
        } else {
            $response['message'] = 'Товар не найден.';
        }
    } else {
        $response['message'] = 'Неверный ID товара.';
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// PHP ЛОГИКА: ВЫБОРКА ДАННЫХ ДЛЯ КАТАЛОГА
$categories = R::findAll('categories', 'ORDER BY id DESC');
$new_products = R::find('products', 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY created_at DESC');

$selectedCategoryId = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? intval($_GET['category_id']) : null;

$whereClause = '';
$params = [];

if ($selectedCategoryId) {
    $whereClause .= ' WHERE category_id = ?';
    $params[] = $selectedCategoryId;
}

// Выборка всех товаров (с фильтром категории если есть)
$products = R::findAll('products', $whereClause . ' ORDER BY created_at DESC', $params);

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
            
            <?php if (!empty($errors)): ?>
                <div class="row justify-content-center mb-4">
                    <div class="col-md-8">
                        <?php foreach($errors as $error): ?>
                            <div class="alert alert-danger mx-3" role="alert"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3 shadow-lg">
                            <div class="modal-header bg-light border-bottom-0">
                                <h5 class="modal-title fw-bold" id="addProductModalLabel">Добавление нового товара</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Название <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="title" placeholder="Название товара..." required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Категория</label>
                                    <div class="d-flex gap-2">
                                        <select name="category_id" class="form-select">
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Добавить новую категорию">
                                            ➕
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Описание</label>
                                    <textarea class="form-control" name="desc" rows="3" placeholder="Подробное описание товара..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Цена <span class="text-danger">*</span></label>
                                    <input class="form-control" type="number" step="0.01" name="price" placeholder="Цена товара (например, 199.50)" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Изображение товара <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" name="cover" id="coverInput" required>
                                </div>
                                <div class="cover-preview-wrapper mt-3" style="display: none;">
                                    <p class="mb-1 text-muted small">Превью:</p>
                                    <img src="" alt="Превью" id="coverPreview" class="img-fluid rounded shadow" style="max-width: 150px;">
                                </div>
                            </div>

                            <div class="modal-footer border-top-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                <button type="submit" name="submit_product" class="btn btn-primary">Добавить товар</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
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


<script>
document.getElementById('categoryFilter').addEventListener('change', function() {
    document.getElementById('categoryFilterForm').submit();
});
</script>
<script>
    // 1. Автоматическая отправка формы при смене категории
    document.getElementById('categoryFilter').addEventListener('change', function() {
        document.getElementById('categoryFilterForm').submit();
    });

    // 2. Логика превью для модального окна добавления товара
    document.getElementById('coverInput').addEventListener('change', function() {
        const input = this;
        const preview = document.getElementById('coverPreview');
        const wrapper = document.querySelector('.cover-preview-wrapper');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                wrapper.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            wrapper.style.display = 'none';
        }
    });
</script>
<?require_once(ROOT . "templates/footer.php");?>