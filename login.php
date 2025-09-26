<?php
session_start();

$host = "localhost";
$dbname = "proteus";
$user = "root";
$pass = "";

$conexion = new mysqli($host, $user, $pass, $dbname);
if ($conexion->connect_error) {
    die("Error de conexión");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conexion->prepare("SELECT id, id_tipo, password FROM users WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    header("Location: log.html?error=1");
    exit;
}

$stmt->bind_result($id, $id_tipo, $pwd_db);
$stmt->fetch();

// Validación directa (sin hash)
$valid = ($password === $pwd_db);

if ($valid) {
    $_SESSION['id'] = $id;
    $_SESSION['id_tipo'] = intval($id_tipo); // <-- ahora sí tomamos el valor correcto
    session_regenerate_id(true);
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: log.html?error=1");
    exit;
}

}

// Si entra por GET o sin POST
header("Location: log.html");
exit;
