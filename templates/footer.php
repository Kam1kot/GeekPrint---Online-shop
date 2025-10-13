<footer>
        <div class="footer d-flex flex-column align-items-center bg-white shadow-sm pt-4 pb-3">
            <div class="footer-row-info d-flex gap-4 mb-3">
                <a href="<?=HOST?>#">Главная</a>
                <a href="<?=HOST?>index.php#about-us">О нас</a>
                <a href="<?=HOST?>catalog.php">Каталог</a>
                <a href="<?=HOST?>index.php#contacts">Контакты</a>
            </div>
            <p class="text-muted small mb-0">© <?= date('Y') ?> GeekPrint. Все права защищены.</p>
        </div>

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
                                            class="btn btn-danger btn-sm delete-cover-btn position-absolute top-0 start-100 translate-middle rounded-circle p-1" 
                                            title="Удалить обложку"
                                            data-product-id="<?= (int)$product['id'] ?>"
                                            style="z-index: 10;">
                                            &times;
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
    </footer>
    
    <script>
        // --- Логика JS для превью изображения в модальном окне ---
        const setupEditModalPreview = (productId) => {
            const input = document.getElementById(`editCoverInput-${productId}`);
            const preview = document.getElementById(`editCoverPreview-${productId}`);
            const wrapper = document.querySelector(`.cover-preview-wrapper-${productId}`);
            const currentCoverContainer = document.getElementById(`cover-container-${productId}`);

            if (!input || !preview || !wrapper) return; 

            input.addEventListener("change", function () {
                const file = this.files[0];

                if (file && file.type.startsWith("image/")) {
                    const reader = new FileReader();

                    reader.onload = ({ target }) => {
                        preview.src = target.result;
                        wrapper.style.display = "block"; // Показываем контейнер с новым превью
                        
                        // Скрываем текущее изображение
                        if (currentCoverContainer) {
                            currentCoverContainer.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Если файл не выбран, скрываем превью и показываем текущее изображение (если было)
                    wrapper.style.display = "none";
                    if (currentCoverContainer) {
                        currentCoverContainer.style.display = 'block';
                    }
                }
            });
        };

        // Инициализация превью для всех модальных окон
        document.querySelectorAll('.modal').forEach(modal => {
            const modalId = modal.id;
            const productIdMatch = modalId.match(/editProductModal(\d+)/);
            
            if (productIdMatch && productIdMatch[1]) {
                const productId = productIdMatch[1];
                setupEditModalPreview(productId);
            }
        });

        // --- Логика JS для AJAX-удаления обложки ---
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-cover-btn')) {
                const button = event.target;
                const productId = button.getAttribute('data-product-id');

                const formData = new FormData();
                formData.append('action', 'delete_cover');
                formData.append('id', productId);

                fetch('<?=HOST?>src/actions/delete_cover_action.php', { // Рекомендуется использовать отдельный обработчик
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Успех: скрываем блок с изображением
                        const container = document.getElementById(`cover-container-${productId}`);
                        if (container) {
                            container.style.display = 'none';
                        }
                        // Опционально: сбросить input file
                        document.getElementById(`editCoverInput-${productId}`).value = '';
                    } else {
                        alert('Ошибка при удалении обложки: ' + (data.message || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Ошибка AJAX:', error);
                    alert('Произошла ошибка сети или сервера.');
                });
            }
        });

        // --- Логика JS для отправки формы фильтрации (если она есть на странице) ---
        // (Оставлено без изменений, так как не имеет прямого отношения к футеру/модалке)
        const categoryFilterElement = document.getElementById('categoryFilter');
        if (categoryFilterElement) {
            categoryFilterElement.addEventListener('change', function() {
                const categoryFilterFormElement = document.getElementById('categoryFilterForm');
                if (categoryFilterFormElement) {
                    categoryFilterFormElement.submit();
                }
            });
        }
    </script>
    
    <script src="<?= HOST ?>src/js/bootstrap.bundle.min.js"></script>
    <script src="<?= HOST ?>src/js/cart.js"></script>
    <script src="<?= HOST ?>src/js/search-logic.js"></script>
    <script src="<?= HOST ?>src/js/delete_product.js"></script>
    <script src="<?= HOST ?>src/js/delete_category.js"></script>
    <script src="<?= HOST ?>src/js/swiper-bundle.min.js"></script>
    <script src="<?= HOST ?>src/js/swiper.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

</body>
</html>