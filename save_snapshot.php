<?php
header('Content-Type: application/json');

//  Configuración de conexión
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $metricas = $_POST['metricas'] ?? '';
    $filtros = $_POST['filtros'] ?? '';

    if (!$nombre || !$metricas || !$filtros) {
        echo json_encode(['status' => 'error', 'msg' => 'Faltan datos']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO snapshots (nombre, metricas, filtros) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $metricas, $filtros]);
        echo json_encode(['status' => 'ok']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}
