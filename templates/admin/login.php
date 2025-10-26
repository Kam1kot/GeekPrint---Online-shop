<?php
// Запускаем сессию


require($_SERVER['DOCUMENT_ROOT'] . "/config.php");
require(ROOT . "db.php");

require_once(ROOT . "templates/head.php");

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: /index.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // 1. Ищем пользователя в БД
    $user = R::findOne('users', 'name = ?', [$login]);

    // 2. Проверяем, что юзер найден И что он админ (добавьте поле 'is_admin' или 'role' в таблицу users)
    // 3. Проверяем пароль
    if ($user && $user->is_admin == 1 && password_verify($password, $user->password)) {
        
        // Записываем в сессию, что это админ
        session_regenerate_id(); // Защита от фиксации сессии
        $_SESSION['is_admin'] = true;
        $_SESSION['user'] = $user->name;
        header('Location: /index.php');
        exit;

    } else {
        // --- ПРОВАЛ ---
        $error_message = 'Неверный логин или пароль.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в Панель управления</title>
    <link rel="stylesheet" href="style.css">
    
    <style>
        body {
            display: block; /* Убираем flex, который нужен для админки */
            background-color: var(--color-vanilla);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>Вход в Админку</h1>
        
        <?php if ($error_message): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div>
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div>
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Войти</button>
        </form>
    </div>

</body>
</html>