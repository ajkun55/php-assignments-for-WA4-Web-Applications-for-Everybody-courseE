<?php
require_once 'pdo.php';
header("Content-type: application/json; charset=utf-8");

$stmt = $pdo->query('SELECT * FROM Profile');
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo(json_encode($profiles, JSON_PRETTY_PRINT));
