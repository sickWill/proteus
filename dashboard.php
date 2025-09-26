
<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Métricas</title>
     <script>
        const USER_TIPO = <?php echo json_encode($_SESSION['id_tipo'] ?? 1); ?>;
        console.log("Tipo de usuario logueado:", USER_TIPO);
    </script>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>
    <header>
       
        <h1>Métricas del Equipo</h1>
        <div class="filters">
            <label>Desde: <input type="date" id="startDate"></label>
            <label>Hasta: <input type="date" id="endDate"></label>
            <label>Agente: 
                <select id="agentSelect">
                    <option value="">Todos</option>
                </select>
            </label>
            <label>Campaña: 
                <select id="campaignSelect">
                    <option value="">Todas</option>
                </select>
            </label>
            <button id="filterBtn">Aplicar Filtros</button>
            <a href="logout.php" class="logout-btn">Cerrar sesión</a>
        </div>
    </header>

    <main class="charts-wrapper">
    <div class="metricas-circulares" id="metricsContainer">
        </div>
    <div class="piechart-wrapper">
    <canvas id="graficoResultados" width="300" height="300"></canvas>
    <div id="piechartLegend" class="piechart-legend"></div>
    </div>
</main>

    <section class="snapshots">
        <h2>Gestión de Snapshots</h2>
        <div class="snapshot-controls">
            <input type="text" id="snapshotName" placeholder="Nombre del snapshot">
            <button id="guardarSnapshot">Guardar Snapshot</button>
        </div>

        <h3>Snapshots previos</h3>
        <ul id="snapshotsList"></ul>

        <div id="snapshotDetalle">
            <!-- Aquí se mostrará el snapshot seleccionado -->
        </div>
    </section>
    
    <script src="script.js"></script>
</body>
</html>
