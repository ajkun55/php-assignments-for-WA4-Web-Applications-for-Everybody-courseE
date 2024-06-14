<?php
require_once 'pdo.php';
header("Content-type: application/json; charset=utf-8");

$stmt = $pdo->query("SELECT * FROM profile WHERE profile_id=" . $_GET['profile_id']);
$profile = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM position WHERE profile_id=" . $_GET['profile_id']);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id
WHERE profile_id = :prof ORDER BY rank");
$stmt->execute(array(":prof" => $_GET['profile_id']));
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$array = array(
    "profile" => $profile,
    "positions" => $positions,
    "schools" => $educations
);

echo(json_encode($array, JSON_PRETTY_PRINT));
