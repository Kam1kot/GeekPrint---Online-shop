<article class="all-card col-sm-6 col-md-4 col-lg-3 d-flex flex-column gap-2">
    <?php if(is_admin()): ?>
        <div class="product-actions d-flex gap-2 mt-2">
            <!-- Форма удаления категории -->
            <button 
                class="btn btn-warning position-relative" 
                style="z-index: 2;" 
                data-bs-toggle="modal" 
                data-bs-target="#editCategoryModal<?= (int)$category['id'] ?>">
                ⚙️
            </button>
            <form method="post" action="index.php" class="d-inline product-delete-form">
                <input type="hidden" name="id" value="<?= $category["id"] ?>">
                <input type="hidden" name="cover" value="<?= htmlspecialchars($category['cover_name']) ?>">
                <input type="hidden" name="category-delete" value="1">
                <button class="btn btn-danger confirm-delete-btn-cat position-relative" style="z-index: 2;">❌</button>
            </form>
        </div>
    <?php endif; ?>
    <div class="card category-card text-decoration-none h-100 border-0 w-100">
        <?php
        $categoryCover = $category->cover_name ?? '';
        $coverPath = ROOT . "src/data/category_covers/" . $categoryCover;
        ?>

        <a href="catalog.php?category_id=<?= htmlspecialchars($category->id) ?>" class="card-body border border-secondary d-flex align-items-center justify-content-center shadow">
            <h5 class="card-title text-center mb-0 text-dark fs-5 fw-bold">
                <?= htmlspecialchars($category->name) ?>
            </h5>
        </a>
        <?php if (!empty($categoryCover) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . str_replace(ROOT, '', $coverPath))): ?>
            <a href="catalog.php?category_id=<?= htmlspecialchars($category->id) ?>" class="img-wrapper">
                <img src="<?= HOST ?>src/data/category_covers/<?= $category['cover_name'] ?>" class="card-img-top category-card-img border border-secondary border-top-0" alt="<?= htmlspecialchars($category->name) ?>">
            </a>
        <?php endif; ?>
    </div>
</article>
<!-- Модальное окно редактирования категории -->  
<div class="modal fade" 
    id="editCategoryModal<?= (int)$category['id'] ?>" 
    tabindex="-1" 
    aria-labelledby="editCatModalLabel<?= (int)$category['id'] ?>" 
    aria-hidden="true">
    <form method="post" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Заголовок -->
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editCatModalLabel<?= (int)$category['id'] ?>">
                        Редактирование категории
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Тело модалки -->
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название</label>
                        <input class="form-control"
                                type="text"
                                name="category_name"
                                value="<?= htmlspecialchars($category['name']) ?>"
                                required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Изображение</label>
                        <input class="form-control"
                                type="file"
                                name="cover"
                                id="editCoverInput-<?= (int)$category['id'] ?>">
                    </div>
                    <div class="mb-3 cover-preview-wrapper-<?= (int)$category['id'] ?>" style="display: none;">
                        <p class="mb-1">Превью нового изображения:</p>
                        <img src="" 
                            alt="" 
                            id="editCoverPreview-<?= (int)$category['id'] ?>" 
                            class="img-fluid rounded shadow" 
                            style="max-width: 150px; display: block;">
                    </div>

                    <?php if(!empty($category['cover_name'])): ?>
                        <div class="mb-3">
                            <div id="cover-container-<?= (int)$category['id'] ?>">
                                <p class="mb-1">Текущее изображение:</p>
                                <div class="position-relative img-div">
                                <img src="src/data/category_covers/<?= htmlspecialchars($category->cover_name) ?>" 
                                    alt="" 
                                    class="img-fluid" 
                                    style="max-width: 150px; display: block; margin-bottom: 10px;">
                                <button type="button" 
                                        class="d-flex align-items-center justify-content-center svg-sym p-3 btn-sm delete-cover-btn position-absolute top-50 start-50 translate-middle" 
                                        data-category-id="<?= (int)$category['id'] ?>">
                                        X
                                </button>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                </div>

                <!-- Кнопки -->
                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" name="edit_product" class="btn btn-primary">
                        Сохранить изменения
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>