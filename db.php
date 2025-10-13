<?php

require(ROOT . "src/libs/rb-mysql.php");

#R::setup( 'pgsql:host='. DB_HOST . ';port='. DB_PORT .';dbname=' . DB_NAME, DB_USER, DB_PASS);
R::setup( 'mysql:host='. DB_HOST . ';dbname='. DB_NAME, DB_USER, DB_PASS );