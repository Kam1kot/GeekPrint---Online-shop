<?php
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
            header('Location: ' . HOST . '/catalog.php');
            exit;
        }
    } else {
        header('Location: ' . HOST . '/catalog.php');
        exit;
    }

    if (isset($_POST['product-delete'])) {
        unlink(ROOT . "src/data/covers/" . $_POST['cover']);
        unlink(ROOT . "src/data/product_covers/" . $_POST['cover']);
        R::trash($product);
        header('Location: ' . HOST . '/catalog.php');
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