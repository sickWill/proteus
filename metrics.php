<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Tipo de usuario (1,2,3) y ID del usuario logueado
$id_tipo = $_SESSION['id_tipo'] ?? 1;
$session_user_id = $_SESSION['id'] ?? null;

// Inicializamos métricas por defecto
$metrics = [
    'contactabilidad'     => 0.0,
    'penetracion_bruta'   => 0.0,
    'penetracion_neta'    => 0.0
];

try {
    // Conexión
    $pdo = new PDO("mysql:host=sql208.infinityfree.com;dbname=if0_40028271_proteus;charset=utf8", "if0_40028271", "Dom2bk9eqheaqZi");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ====== FILTROS GET ======
    $user_id     = isset($_GET['user_id']) && $_GET['user_id'] !== '' ? intval($_GET['user_id']) : null;
    $campaign_id = isset($_GET['campaign_id']) && $_GET['campaign_id'] !== '' ? intval($_GET['campaign_id']) : null;
    $start_date  = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
    $end_date    = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

    // ====== CONTROL DE PERMISOS SEGÚN id_tipo ======
    if($id_tipo == 1){
        // Usuario tipo 1: siempre solo sus métricas
        $user_id = $session_user_id;
        $campaign_id = null; // no puede filtrar por campaña
    }
    // Tipo 2 y 3: pueden filtrar libremente, no sobreescribimos

    // ====== Construir WHERE dinámico ======
    $where = [];
    $params = [];

    if ($user_id) {
        $where[] = 'g.id_broker = :user_id';
        $params[':user_id'] = $user_id;
    }

    if ($campaign_id && ($id_tipo >= 2)) {
        $where[] = 'g.id_campaign = :campaign_id';
        $params[':campaign_id'] = $campaign_id;
    }

    if ($start_date) {
        $start_ts = str_replace('-', '', $start_date) . '000000';
        $where[] = 'g.timestamp >= :start_ts';
        $params[':start_ts'] = $start_ts;
    }

    if ($end_date) {
        $end_ts = str_replace('-', '', $end_date) . '235959';
        $where[] = 'g.timestamp <= :end_ts';
        $params[':end_ts'] = $end_ts;
    }

    $whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // ---------------------------
    // 1) Contactabilidad
    // ---------------------------
    $sql_contact = "
        SELECT COALESCE(
            (SUM(CASE WHEN r.nombre IN ('Coordinado','Contactado','Agendado') THEN 1 ELSE 0 END)
            / NULLIF(COUNT(*),0)) * 100
        , 0) AS contactabilidad
        FROM gestiones g
        JOIN gestiones_resultado r ON g.id_resultado = r.id
        $whereSQL
    ";
    $stmt = $pdo->prepare($sql_contact);
    $stmt->execute($params);
    $metrics['contactabilidad'] = round((float)($stmt->fetchColumn() ?? 0), 2);

    // ---------------------------
    // 2) Penetración Bruta
    // ---------------------------
    $totalContactos = (int)$pdo->query("SELECT COUNT(*) FROM contactos")->fetchColumn();
    $denomContactos = $totalContactos > 0 ? $totalContactos : 1;

    $sql_pb = "
        SELECT COUNT(DISTINCT g.id_contacto) AS contactos_gestionados
        FROM gestiones g
        $whereSQL
    ";
    $stmt = $pdo->prepare($sql_pb);
    $stmt->execute($params);
    $contactosGestionados = (int)($stmt->fetchColumn() ?? 0);

    $metrics['penetracion_bruta'] = round(($contactosGestionados / $denomContactos) * 100, 2);

    // ---------------------------
    // 3) Penetración Neta
    // ---------------------------
    $sql_pn = "
        SELECT COUNT(DISTINCT CASE WHEN r.nombre IN ('Coordinado','Contactado','Agendado') THEN g.id_contacto END) AS contactos_contactados
        FROM gestiones g
        JOIN gestiones_resultado r ON g.id_resultado = r.id
        $whereSQL
    ";
    $stmt = $pdo->prepare($sql_pn);
    $stmt->execute($params);
    $contactosContactados = (int)($stmt->fetchColumn() ?? 0);


    $metrics['penetracion_neta'] = round(($contactosContactados / $denomContactos) * 100, 2);

    $action = $_GET['action'] ?? 'default';

   if ($action === 'resultados') {
    $sql = "
        SELECT gr.nombre AS resultado, COUNT(g.id) AS total
        FROM gestiones g
        INNER JOIN gestiones_resultado gr ON g.id_resultado = gr.id
        GROUP BY gr.id, gr.nombre
        ORDER BY total DESC
    ";
    $stmt = $pdo->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

    // Asegurar límites 0..100
    foreach ($metrics as $k => $v) {
        if (!is_finite($v) || is_nan($v)) $metrics[$k] = 0.0;
        if ($metrics[$k] < 0) $metrics[$k] = 0.0;
        if ($metrics[$k] > 100) $metrics[$k] = 100.0;
    }

    // Salida limpia
    echo json_encode($metrics, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("[metrics.php] DEBUG: id_tipo=$id_tipo, user_id=$user_id, campaign_id=$campaign_id, error=" . $e->getMessage());
    echo json_encode($metrics, JSON_UNESCAPED_UNICODE);
}
