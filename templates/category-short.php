<article class="all-card col-sm-6 col-md-4 col-lg-3 d-flex flex-column gap-2">
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