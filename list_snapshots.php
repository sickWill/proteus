<?php
header('Content-Type: application/json');

// Configuración de conexión
$host = "sql208.infinityfree.com";
$dbname = "if0_40028271_proteus";
$user = "if0_40028271";
$pass = "Dom2bk9eqheaqZi";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, nombre, fecha FROM snapshots ORDER BY fecha DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode([]);
}
