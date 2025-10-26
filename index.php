<?
require("config.php");
require("db.php");
require(ROOT . "src/functions/all.php");
require(ROOT . "src/functions/categoriesHandle.php");

require_once(ROOT . "templates/head.php");
require_once(ROOT . "templates/header.php");

$products = R::findAll('products');
$categories = R::findAll('categories');
$reviews = R::findAll('avito_reviews');
?>
    <main>
        <!-- Hero-секция -->
        <section class="hero shadow-sm position-relative mt-5 mb-5">
            <h1>Уют в каждой детали</h1>
            <p class="lead">Индивидуальные 3D-изделия, созданные с душой</p>
            <a style="color: black; text-decoration: none;" class="main-btn" href="<?=HOST?>catalog.php">Смотреть каталог</a>
            <img class="shark-image" src="<?ROOT?>src/imgs/shark-header.png" alt="">
        </section>
        <!-- О нас -->
        <h2 id="about-us" class="d-flex justify-content-center pb-3 pt-5" >О нас</h2>
        <section  class="position-relative container pb-1 pt-5 d-flex justify-content-center align-items-center flex-wrap">
            <div class="row w-100 about-us-container">
                <div class="text-center about-us-left col-md-6 col-lg-4 mb-4 position-relative z-2">
                    <div class="position-relative z-2 border border-secondary-subtle p-4 bg-white shadow-sm rounded">
                        <h2 class="fw-bold mb-3">Чем мы занимаемся?</h2>
                        <hr class="m-auto mb-3" style="width: 80%; height: 3px; background-color: #008DFF; border: none;">
                        <p>Мы специализируемся на 3D-печати и 3D-моделировании. Создаём физические объекты по цифровым чертежам, а также помогаем воплотить ваши идеи в реальность.</p>
                        <p>Выполняем печать:</p>
                        <ul>
                            <li>по готовым 3D-моделям;</li>
                            <li>по вашим чертежам и эскизам с нуля;</li>
                            <li>по физическим образцам — воссоздание и точное копирование деталей.</li>
                        </ul>
                        <p>Мы используем высокоточные принтеры и качественные материалы, что позволяет получать надёжные и эстетичные изделия любой сложности.</p>
                        <p>Обращайтесь — вместе мы создадим то, что ещё вчера было лишь идеей!</p>

                        <div class="d-flex">
                            <button class="btn main-btn border mx-auto mt-3 px-4 py-2">Связаться с нами</button>
                        </div>
                    </div>
                    <img src="<?ROOT?>src/imgs/collage.jpg" alt="3D печать и моделирование" class="border border-secondary-subtle collage-img-left img-fluid rounded position-absolute z-1 **d-none d-md-block**">
                </div>
                <div class="text-center about-us-right col-md-6 col-lg-4 mb-4 position-relative z-2">
                    <div class="position-relative z-2 border border-secondary-subtle p-4 bg-white shadow-sm rounded">
                        <h2 class="fw-bold mb-3">Почему именно мы?</h2>
                        <hr class="m-auto mb-3" style="width: 80%; height: 3px; background-color: #008DFF; border: none;">
                        <!-- ВСТАВКА: Причины выбрать нас -->
                        <div class="mt-4 d-flex flex-column">
                            <div class="mb-4"><strong>🎯 Индивидуальный подход</strong> — вникаем в задачу, дорабатываем модель, работаем на результат.</div>
                            <div class="mb-4"><strong>🧩 Печать под ключ</strong> — от идеи и чертежа до готовой детали.</div>
                            <!-- <div class="mb-3"><strong>👀 Визуальный контроль</strong> — при необходимости согласовываем внешний вид модели и финального изделия.</div> -->
                            <div class="mb-4"><strong>⏰ Прозрачные сроки</strong> — всегда честно называем сроки и соблюдаем их.</div>
                            <div class="mb-4"><strong>🛠️ Опыт и креатив</strong> — берёмся за нестандартные решения, любим сложные проекты.</div>
                            <div class="mb-4"><strong>📦 Надёжная упаковка и доставка</strong> — всё приедет в целости и сохранности.</div>
                            <div class=""><strong>❤️ Работаем с душой</strong> — нам важно не просто сделать, а воплотить вашу идею, чтобы вы остались довольны и пришли снова.</div>
                        </div>
                    </div>
                    <img src="<?ROOT?>src/imgs/collage-2.jpg" alt="3D печать и моделирование" class="border border-secondary-subtle collage-img-right img-fluid rounded position-absolute z-1 **d-none d-md-block**">
                </div>
                <div class="clearfix d-md-none"></div>
    
                </div>
            </div>
        </section>

        <!-- Социальные сети -->
        <section  class="container mb-5">
            <div class="d-flex align-items-center justify-content-center social-icons">
                <a href="https://vk.com/3dprinter33" target="_blank"><img src="<?ROOT?>src/imgs/vk.png" alt="GeekPrint VK"></a>
                <a href="https://www.avito.ru/brands/ec8aea67ae5ca40fae709aa9d2e61c68/all?gdlkerfdnwq=101&page_from=from_item_card_icon&iid=7418041963&sellerId=82e5de2636ff05c30924d46394c6060f" target="_blank"><img src="<?ROOT?>src/imgs/avito.png" alt="GeekPrint AVITO"></a>
            </div>
        </section>

        <!-- Категории товаров -->
        <section class="container pt-3 pb-5">
            <h2 class="text-center mb-4">Категории товаров</h2>
            <?php if(is_admin()): ?>
                <div class="d-flex justify-content-center mb-3">
                    <button type="button" class="btn main-btn ms-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        Добавить категорию
                    </button>
                    <?php if (isset($_GET['success_category']) && $_GET['success_category'] == 1 && empty($errors)): ?>
                        <div class='alert alert-success ms-3'>Категория успешно добавлена с ID: <?= htmlspecialchars($_GET['category_id']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="row category-container g-4">
                <?php
                    foreach ($categories as $category) {
                        include(ROOT . "templates/category-short.php");
                    }
                ?>
            </div>
        </section>

        <?php
        $avito = R::findOne('avito_reviews', 'ORDER BY id DESC');
        $fullStars = floor($avito->rating);
        $hasHalfStar = fmod($avito->rating, 1) >= 0.25 && fmod($avito->rating, 1) < 0.75;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        ?>
        
        <!-- Отзывы -->
        <section id="reviews" class="container pt-3 pb-5">
            <h2 class="text-center mb-4">Отзывы наших клиентов</h2>
            <div class="container my-3 d-flex justify-content-center">
                <div class="d-flex flex-column align-items-center shadow border-0 rounded-4 p-3 bg-light">
                    <a target="_blank" href="https://www.avito.ru/brands/ec8aea67ae5ca40fae709aa9d2e61c68/all?gdlkerfdnwq=101&page_from=from_item_card_icon&iid=7418041963&sellerId=82e5de2636ff05c30924d46394c6060f" class="fs-2 rating-a">Avito рейтинг</a>
                    <div class="d-flex gap-2">
                        <div class="fs-2 fw-bold"><?=$avito->rating?></div>
                        <div class="fs-3 text-warning d-flex align-items-start mt-2 gap-1">
                            <!-- Полные звезды -->
                            <?php for ($i = 0; $i < $fullStars; $i++): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 16 16"><path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
                            <?php endfor; ?>

                            <!-- Половинная звезда -->
                            <?php if ($hasHalfStar): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 16 16"><path fill="currentColor" d="M5.354 5.119L7.538.792A.52.52 0 0 1 8 .5c.183 0 .366.097.465.292l2.184 4.327l4.898.696A.54.54 0 0 1 16 6.32a.55.55 0 0 1-.17.445l-3.523 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256a.5.5 0 0 1-.146.05c-.342.06-.668-.254-.6-.642l.83-4.73L.173 6.765a.55.55 0 0 1-.172-.403a.6.6 0 0 1 .085-.302a.51.51 0 0 1 .37-.245zM8 12.027a.5.5 0 0 1 .232.056l3.686 1.894l-.694-3.957a.56.56 0 0 1 .162-.505l2.907-2.77l-4.052-.576a.53.53 0 0 1-.393-.288L8.001 2.223L8 2.226z"/></svg>
                            <?php endif; ?>

                            <!-- Пустые звезды -->
                            <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 16 16"><path fill="currentColor" d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256l4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73l3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356l-.83 4.73zm4.905-2.767l-3.686 1.894l.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575l-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957l-3.686-1.894a.5.5 0 0 0-.461 0z"/></svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="fs-5">Основываясь на <?= $avito->total_reviews ?> отзывах</div>
                </div>
            </div>
            <div class="swiper">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <?php foreach ($reviews as $review): ?>
                    <div class="swiper-slide p-4"><?include(ROOT . "templates/rate-short.php");?></div>
                    <?php endforeach; ?>
                </div>
                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
    </main>
<?require_once(ROOT . "templates/footer.php")?>