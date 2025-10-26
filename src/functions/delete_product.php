<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require(ROOT . "db.php");
require(ROOT . "src/functions/all.php");


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product = R::load('products', $_GET['id']);

    if ($product['id'] == 0) {
        header('Location: ' . HOST);
        exit;
    }
} else {
    header('Location: ' . HOST);
    exit;
}

if (isset($_POST['delete-post'])) {
    R::trash($product);
    header('Location: ' . HOST);
    exit;
}