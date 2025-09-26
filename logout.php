<?php
session_start();
// Destruye todas las variables de sesión
$_SESSION = [];
session_destroy();

// Redirige al login
header("Location: Login.php");
exit;
?>
