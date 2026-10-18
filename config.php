<?php
// Railway uses MYSQL_ROOT_PASSWORD instead of MYSQLPASSWORD
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway'); 
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQL_ROOT_PASSWORD') ?: ''); 
define('SITE_NAME', 'PCMS');
