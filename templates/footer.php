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
</footer>
<?require('modals.php')?>
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
    <script src="<?= HOST ?>src/js/swiper-otherProducts.js"></script>
    <script src="<?= HOST ?>src/js/categoryPreview.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
    
</body>
</html>