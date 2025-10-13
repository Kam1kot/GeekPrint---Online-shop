<?php
// PHP-логика для определения количества звезд (использовалась в index.php, но дублируется здесь для самодостаточности)
$reviewStars = (float)($review['stars'] ?? 0);
$fullStars_rate = floor($reviewStars);
// Логика для отображения половины звезды (25% до 75%)
$hasHalfStar_rate = fmod($reviewStars, 1) >= 0.25 && fmod($reviewStars, 1) < 0.75; 
$emptyStars_rate = 5 - $fullStars_rate - ($hasHalfStar_rate ? 1 : 0);

// Использование функции для генерации SVG звезды (предполагается, что она доступна)
if (!function_exists('getStarSvg')) {
    // В случае, если функция не была определена в index.php, определяем ее здесь, 
    // чтобы избежать ошибок и использовать единый стиль звезд.
    function getStarSvg(string $type, string $size = "23"): string {
        $icon = '';
        switch ($type) {
            case 'full':
                $icon = '<path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>';
                break;
            case 'half':
                $icon = '<path fill="currentColor" d="M5.354 5.119L7.538.792A.52.52 0 0 1 8 .5c.183 0 .366.097.465.292l2.184 4.327l4.898.696A.54.54 0 0 1 16 6.32a.55.55 0 0 1-.17.445l-3.523 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256a.5.5 0 0 1-.146.05c-.342.06-.668-.254-.6-.642l.83-4.73L.173 6.765a.55.55 0 0 1-.172-.403a.6.6 0 0 1 .085-.302a.51.51 0 0 1 .37-.245zM8 12.027a.5.5 0 0 1 .232.056l3.686 1.894l-.694-3.957a.56.56 0 0 1 .162-.505l2.907-2.77l-4.052-.576a.53.53 0 0 1-.393-.288L8.001 2.223L8 2.226z"/>';
                break;
            case 'empty':
                $icon = '<path fill="currentColor" d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256l4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73l3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356l-.83 4.73zm4.905-2.767l-3.686 1.894l.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575l-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957l-3.686-1.894a.5.5 0 0 0-.461 0z"/>';
                break;
            default: return '';
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 16 16">' . $icon . '</svg>';
    }
}
?>

<div class="card h-100 shadow-sm border rounded-3 p-4 bg-white">
    <div class="d-flex align-items-center mb-3">
        <div class="me-3 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24">
                <path fill="currentColor" d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4"/>
            </svg>
        </div>
        
        <div>
            <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($review['name'] ?? 'Аноним') ?></h6>
            <?php if (!empty($review['review_date'])): ?>
                <small class="text-muted">
                    <?= date("d.m.Y", strtotime($review['review_date'])) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
    
    <p class="mb-3 text-dark flex-grow-1">
        <?= nl2br(htmlspecialchars($review['review_text'] ?? 'Отзыв отсутствует.')) ?>
    </p>
    
    <div class="text-warning mt-auto d-flex align-items-center gap-1 fs-5">
        <?php for ($i = 0; $i < $fullStars_rate; $i++): ?>
            <?= getStarSvg('full', '20') ?>
        <?php endfor; ?>

        <?php if ($hasHalfStar_rate): ?>
            <?= getStarSvg('half', '20') ?>
        <?php endif; ?>

        <?php for ($i = 0; $i < $emptyStars_rate; $i++): ?>
            <?= getStarSvg('empty', '20') ?>
        <?php endfor; ?>
        
        <span class="ms-2 small text-muted fst-italic">
            (<?= number_format($reviewStars, 1) ?> / 5.0)
        </span>
    </div>
</div>