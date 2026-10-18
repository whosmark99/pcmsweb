<?php
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'contact_manager');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQL_ROOT_PASSWORD') ?: '');
define('SITE_NAME', 'PCMS');
