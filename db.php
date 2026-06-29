<?php
$DB_HOST = getenv('MYSQLHOST')     ?: "localhost";
$DB_NAME = getenv('MYSQLDATABASE') ?: "cocomo_db";
$DB_USER = getenv('MYSQLUSER')     ?: "root";
$DB_PASS = getenv('MYSQLPASSWORD') ?: "";
$DB_PORT = getenv('MYSQLPORT')     ?: "3306";

try {
  $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Koneksi gagal: " . $e->getMessage());
}
