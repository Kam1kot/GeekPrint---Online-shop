<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require(ROOT . "src/libs/rb-mysql.php");

#R::setup( 'pgsql:host='. DB_HOST . ';port='. DB_PORT .';dbname=' . DB_NAME, DB_USER, DB_PASS);
R::setup( 'mysql:host='. $_ENV['DB_HOST'] . ';dbname='. $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'] );