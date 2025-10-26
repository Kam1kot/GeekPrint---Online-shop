<?php

// Создание категорий
if (isset($_POST['add_category'])) {
    if (empty($_POST['category_name'])) {
        $errors[] = 'Введите название категории';
    }
    
    $checkResСat = checkPhotoBeforeUploadCat();
    if (is_array($checkResСat)) {
        $errors = $checkResСat;
    } elseif ($checkResСat === true) {
        // Откуда
        $sourcePath = $_FILES['cover']['tmp_name'];

        // Куда
        $extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        if ($extension === 'jpeg') $extension = 'jpg';

        $file_name = uniqid() . '.' . $extension;
        $result_path = ROOT . "src/data/category_covers/" . $file_name;
        $result_path_alt = ROOT . "src\data\product_covers/" . $file_name;

        // Кроп
        if (resizeImageByWidth($sourcePath, $result_path, $result_path_alt, 225)) {
            if(resizeAndCrop($sourcePath, $result_path, $result_path_alt, 225, 325)) {
                $coverName = $file_name;
            }
        }

        $category = R::dispense('categories');
        $category->name = htmlspecialchars(trim($_POST['category_name']));
        $category->cover_name = $coverName;
        $id = R::store($category);

        header("Location: " . $_SERVER['PHP_SELF'] . "?success_category=1&category_id=$id");
        exit;
        
    }
}
// Удаление категории
if (isset($_POST['category-delete'])) {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $category = R::load('categories', $_POST['id']);
        if ($category['id'] == 0) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if (isset($_POST['category-delete'])) {
        $coverPath = ROOT . "src/data/category_covers/" . $_POST['cover'];
        if (is_file($coverPath)) {
            unlink($coverPath);
        }
        R::trash($category);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
// Редактирование категории
if (isset($_POST['edit_category'])) {
    $category = R::load('categories', (int)($_POST['id'] ?? 0));
    if ($category->id === 0) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Берём имя из поля формы. Используй одно имя везде — "category_name". + Очистка от мусора
    $name = trim($_POST['category_name'] ?? '');
    if ($name === '') {
        $errors[] = "Должно быть название!";
    }

    if (empty($errors)) {
        // Текущее имя файла (если есть)
        $coverName = $category->cover_name;

        // Если прислали новый файл
        if (!empty($_FILES['cover']['tmp_name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

            // Проверяем именно "категорийную" валидацию
            $checkRes = checkPhotoBeforeUploadCat();
            if (is_array($checkRes)) {
                $errors = $checkRes;
            } elseif ($checkRes === true) {
                $sourcePath = $_FILES['cover']['tmp_name'];

                $extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
                if ($extension === 'jpeg') $extension = 'jpg';

                $file_name = uniqid('', true) . '.' . $extension;

                // Сохраняем в ту же папку, что и при создании
                $result_path = ROOT . "src/data/category_covers/" . $file_name;
                // Если функции требуют альтернативный путь — даём такой же (чтобы не было undefined)
                $result_path_alt = $result_path;

                // Ресайз/кроп (возьми те же размеры, что при создании категории)
                if (resizeImageByWidth($sourcePath, $result_path, $result_path_alt, 225)
                    && resizeAndCrop($sourcePath, $result_path, $result_path_alt, 225, 325)) {

                    // Удаляем старый файл, если был
                    if (!empty($coverName)) {
                        @unlink(ROOT . "src/data/category_covers/" . $coverName);
                    }
                    $coverName = $file_name;
                } else {
                    $errors[] = 'Не удалось обработать изображение.';
                }
            }
        }

        if (empty($errors)) {
            $category->name = htmlspecialchars($name);
            $category->cover_name = $coverName;
            R::store($category);

            header("Location: " . $_SERVER['PHP_SELF'] . "?success_category=1&category_id=" . $category->id);
            exit();
        }
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'delete_cover') {
    $response = ['success' => false, 'message' => ''];
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $category = R::load('categories', $_POST['id']);
        if ($category->id !== 0) {
            $coverName = $category->cover_name;
            if (!empty($coverName)) {
                $filePath = ROOT . "src/data/category_covers/" . $coverName;
                if (file_exists($filePath) && unlink($filePath)) {
                    $category->cover_name = null;
                    R::store($category);
                    $response['success'] = true;
                    $response['message'] = 'Изображение успешно удалено.';
                } else {
                    $response['message'] = 'Файл изображения не найден или не может быть удален.';
                }
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