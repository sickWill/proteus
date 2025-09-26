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
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    echo json_encode(['status' => 'error', 'msg' => 'Falta ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM snapshots WHERE id = ?");
    $stmt->execute([$id]);
    $snapshot = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($snapshot ?: []);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
