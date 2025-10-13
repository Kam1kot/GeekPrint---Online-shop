<?php
require(ROOT . "config.php");
require(ROOT . "db.php");
require(ROOT . "src/functions/all.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category = R::load('categories', $_GET['id']);

    if ($category['id'] == 0) {
        header('Location: ' . HOST);
        exit;
    }
} else {
    header('Location: ' . HOST);
    exit;
}

if (isset($_POST['delete-post'])) {
    R::trash($category);
    header('Location: ' . HOST);
    exit;
}