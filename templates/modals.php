<?php if (!empty($errors) && isset($_POST['submit_product'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
        modal.show();
    });
</script>
<?php endif; ?>

<?php if (!empty($errors) && isset($_POST['edit_product'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
    });
</script>
<?php endif; ?>

<?php if (!empty($errors) && isset($_POST['add_category'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        modal.show();
    });
</script>
<?php endif; ?>
<!-- Добавление продукта -->
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

<!-- Модальное окно добавления категории -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <form method="post" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryLabel">Добавить категорию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <?php if (!empty($errors) && is_array($errors) && isset($_POST['add_category'])): ?>
                    <?php foreach($errors as $error): ?>
                        <div class='alert alert-danger mx-3 mt-2'><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><span class="text-warning"><strong>* </strong></span>Название</label>
                        <input type="text" name="category_name" class="form-control" placeholder="Название категории" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Изображение категории</label>
                        <input type="file" name="cover" class="form-control" id="categoryCoverInput">
                        <img id="categoryCoverPreview" src="" 
                            style="display:none; max-width:150px; margin-top:10px;" 
                            class="img-fluid rounded shadow">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_category" class="btn btn-primary">Добавить</button>
                    <input type="hidden" name="add_category" value="1">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Модальное окно корзины -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-cart modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Ваша корзина</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="modal-body">
                <div id="cartEmpty" class="text-center text-muted my-4 d-none">
                    Корзина пуста
                    <div class="mt-3">
                    <a href="/catalog.php" class="btn btn-outline-primary">Перейти в каталог</a>
                </div>
            </div>


            <div id="cartList" class="list-group mb-3"></div>


            <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="fs-5">Итого: <span id="cartTotal" class="fw-bold">0,00 ₽</span></div>
                <button id="clearCartBtn" type="button" class="btn btn-outline-danger">Очистить корзину</button>
            </div>


            <hr class="my-3"/>


            <form id="orderForm" class="row g-3">
                <div class="col-md-6">
                <label for="orderName" class="form-label">Имя <strong>*</strong></label>
                    <input type="text" class="form-control" id="orderName" name="name" placeholder="Ваше имя" required>
                </div>
                <div class="col-md-6">
                    <label for="orderPhone" class="form-label">Номер телефона <strong>*</strong></label>
                    <input id="orderPhone" name="phone" type="tel" class="form-control" pattern="^\+?[0-9\s\-()]{6,}$" placeholder="+7 999 123-45-67" required>
                </div>
                <div class="col-12">
                    <label for="orderComment" class="form-label">Комментарий</label>
                    <textarea class="form-control" id="orderComment" name="comment" rows="3" placeholder="Пожелания по заказу"></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="pickupCheck" name="pickup">
                        <label class="form-check-label" for="pickupCheck">
                        Самовывоз
                        </label>
                    </div>
                </div>
                <div id="deliveryBlock" class="col-12">
                    <label for="orderAddress" class="form-label">Адрес доставки <strong>*</strong></label>
                    <input type="text" class="form-control mb-3" id="orderAddress" name="address" placeholder="Введите адрес или выберите точку на карте">

                    <div id="deliveryMap" style="width: 100%; height: 300px; border-radius: 10px;"></div>
                </div>
                <div class="alert alert-info" role="alert">
                    <h5 class="alert-heading mb-1">Как происходит оплата товаров:</h5>
                    <ol class="mb-0">
                        <li>Вы оформляете заказ.</li>
                        <li>Продавцу приходит уведомление с вашими контактами и корзиной.</li>
                        <li>Продавец связывается с вами для подтверждения заказа и оплаты.</li>
                        <li>После успешной оплаты продавец отправляет вам товары по прикрепленному адресу.</li>
                        <li>Товар будет у вас в течение x-n дней!</li>
                    </ol>
                    <div class="form-check ms-2 mt-2 mb-1">
                        <input class="form-check-input" type="checkbox" id="checkedCheck" name="checked" required>
                        <label class="form-check-label" for="pickupCheck">
                        С условиями оплаты заказов <span class="text-decoration-underline">ознакомлен</span>.
                        </label>
                    </div>
                </div>
                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Заказать</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

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

    (() => {
        'use strict';
        // Page is loaded
        const objects = document.getElementsByClassName('asyncImage');
        Array.from(objects).map((item) => {
            // Start loading image
            const img = new Image();
            img.src = item.dataset.src;
            // Once image is loaded replace the src of the HTML element
            img.onload = () => {
            item.classList.remove('asyncImage');
            return item.nodeName === 'IMG' ? 
                item.src = item.dataset.src :        
                item.style.backgroundImage = `url(${item.dataset.src})`;
            };
        });
    })();
</script>