<?php
$pdo = new PDO("mysql:host=sql208.infinityfree.com;dbname=if0_40028271_proteus;charset=utf8", "if0_40028271", "Dom2bk9eqheaqZi");
$stmt = $pdo->query("SELECT id, nombre FROM users ORDER BY nombre");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
