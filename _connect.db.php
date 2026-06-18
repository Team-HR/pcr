<?php
date_default_timezone_set("Asia/Manila");
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: '';
$port = getenv('DB_PORT') ?: '3306';