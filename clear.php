<?php
require_once "db.php";
try {
    $pdo->exec("TRUNCATE TABLE estimations");
} catch (PDOException $e) {
    die('Error: '.htmlspecialchars($e->getMessage()));
}
header("Location: result.php");
exit;
