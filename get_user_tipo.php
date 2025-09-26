<?php
session_start();
header('Content-Type: application/json');
echo json_encode(['tipo' => $_SESSION['id_tipo'] ?? 1]);
