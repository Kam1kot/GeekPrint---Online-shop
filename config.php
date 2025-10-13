<?php

// ДБ НАСТРОЙКИ
define('DB_HOST', 'localhost');
define('DB_NAME', 'geekprint');
define('DB_USER', 'root');
define('DB_PASS', '');

define('MAX_UPLOAD_FILE_SIZE', 10*1024*1024);


// Настройки сайта
define('ROOT',dirname(__FILE__) . "/");
define('HOST',"http://" . $_SERVER['HTTP_HOST'] . '/');

// Тайтлы
$title = "GeekPrint | Главная";

$allowed_file_types = [
    'image/jpeg',
    'image/jpg',
    'image/png'
];

$allowed_extentions = ['jpg','jpeg','png'];