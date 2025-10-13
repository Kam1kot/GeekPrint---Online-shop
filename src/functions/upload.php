<?php
function createDirIfNotExist($path) {
    if (!is_dir($path)) {
        mkdir($path,0777, true);
    }
}

function checkPhotoBeforeUpload() {
    global $allowed_extentions, $allowed_file_types;

    // Проверка на наличие ошибок
    if($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
        return ["Ошибка при загрузке изображения"];
    }

    // Проверка на тип файла
    $file_type = mime_content_type($_FILES['cover']['tmp_name']);
    if (!in_array($file_type, $allowed_file_types)) {
        return ["Недопустимый тип файла"];
    }

    // Проверка на расширение файла
    $extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
    if (!in_array($extension,$allowed_extentions)) {
        return ["Недопустимое расширение файла"];
    }
    // Проверка на размер файла
    if ($_FILES['cover']['size'] > MAX_UPLOAD_FILE_SIZE) {
        return ['Файл слишком большой. Максимальный размер файла 10Мб'];
    }

    if ($extension === 'jpeg') {
        $extension = 'jpg';
    }

    $upload_path = ROOT . "src/data/covers/";

    // Проверка на расположение директории, если нет то создан
    createDirIfNotExist($upload_path);

    return true;
}

// function upload_photo() {
//     global $allowed_extentions, $allowed_file_types;

//     // Проверка на наличие ошибок
//     if($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
//         return ["Ошибка при загрузке изображения"];
//     }

//     // Проверка на тип файла
//     $file_type = mime_content_type($_FILES['cover']['tmp_name']);
//     if (!in_array($file_type, $allowed_file_types)) {
//         return ["Недопустимый тип файла"];
//     }

//     // Проверка на расширение файла
//     $extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
//     if (!in_array($extension,$allowed_extentions)) {
//         return ["Недопустимое расширение файла"];
//     }
//     // Проверка на размер файла
//     if ($_FILES['cover']['size'] > MAX_UPLOAD_FILE_SIZE) {
//         return ['Файл слишком большой. Максимальный размер файла 10Мб'];
//     }

//     if ($extension === 'jpeg') {
//         $extension = 'jpg';
//     }

//     $file_name = uniqid() . '.' . $extension;
//     $upload_path = ROOT . "src/data/covers/" . $file_name;

//     // Проверка на расположение директории
//     createDirIfNotExist(ROOT . "src/data/covers/");

//     // Если все ок, то сохранение изображения
//     if (empty($errors)) {
//         if (!move_uploaded_file($_FILES['cover']['tmp_name'], $upload_path)) {
//             return ['Ошибка сохранения файла'];
//         } else {
//             return $file_name;
//         }
//     }
// }

function checkPhotoBeforeUploadCat() {
    global $allowed_extentions, $allowed_file_types;

    // Проверка на наличие ошибок
    if($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
        return ["Ошибка при загрузке изображения"];
    }

    // Проверка на тип файла
    $file_type = mime_content_type($_FILES['cover']['tmp_name']);
    if (!in_array($file_type, $allowed_file_types)) {
        return ["Недопустимый тип файла"];
    }
    // Проверка на расширение файла
    $extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
    if (!in_array($extension,$allowed_extentions)) {
        return ["Недопустимое расширение"];
    }
    // Проверка на размер файла
    if ($_FILES['cover']['size'] > MAX_UPLOAD_FILE_SIZE) {
        return ['Файл слишком большой. Максимальный размер файла 10Мб'];
    }

    if ($extension === 'jpeg') {
        $extension = 'jpg';
    }

    $upload_path = ROOT . "src/data/category_covers";

    // Проверка на расположение директории, если нет то создан
    createDirIfNotExist($upload_path);

    return true;
}
?>