<?php

require($_SERVER['DOCUMENT_ROOT'] . "/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/db.php");

header('Content-Type: application/json');

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($searchTerm)) {
    echo json_encode([]);
    exit;
}

$searchPattern = '%' . $searchTerm . '%';

$products = R::findAll(
    'products',
    ' `title` LIKE ? OR `desc` LIKE ? LIMIT 20 ',
    [ $searchPattern, $searchPattern ]
);

$results = [];
foreach ($products as $product) {
    $results[] = [
        'id' => $product->id,
        'title' => $product->title,
        'cover' => $product->cover_name,
        'price' => $product->price,
    ];
}
echo json_encode($results);