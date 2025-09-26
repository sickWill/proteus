// ------------------------------
// Variables del DOM
// ------------------------------
const metricsContainer = document.getElementById('metricsContainer');
const filterBtn = document.getElementById('filterBtn');
const startDateInput = document.getElementById('startDate');
const endDateInput = document.getElementById('endDate');
const agentSelect = document.getElementById('agentSelect');
const campaignSelect = document.getElementById('campaignSelect');

// ------------------------------
// Función para crear tarjeta de métrica
// ------------------------------
function createMetricCard(name, value, color) {
    const card = document.createElement('div');
    card.classList.add('metric-card');

    const title = document.createElement('h2');
    title.textContent = name.replace(/_/g, ' ');
    card.appendChild(title);

    const canvas = document.createElement('canvas');
    card.appendChild(canvas);

    const safeValue = Number(value);
    const chartValue = isNaN(safeValue) ? 0 : safeValue;
    card.dataset.value = chartValue;

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: [name, 'Restante'],
            datasets: [{
                data: [chartValue, 100 - chartValue],
                backgroundColor: [color, 'rgba(255,255,255,0.2)'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            let rawValue = Number(context.raw);
                            if (isNaN(rawValue)) rawValue = 0;
                            return `${context.label}: ${rawValue.toFixed(2)}%`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    metricsContainer.appendChild(card);
}

// ------------------------------
// Renderizar todas las métricas
// ------------------------------
function renderMetrics(data) {
    metricsContainer.innerHTML = '';
    const colors = ['#f4e5bdff', '#f1cc6fff', '#f1c540ff'];
    let i = 0;
    for (const key in data) {
        createMetricCard(key, data[key], colors[i % colors.length]);
        i++;
    }
}

// ------------------------------
// Construir URL con filtros según tipo de usuario
// ------------------------------
function buildURL() {
    const params = new URLSearchParams();
    if (startDateInput.value) params.append('start_date', startDateInput.value);
    if (endDateInput.value) params.append('end_date', endDateInput.value);

    // Tipo 2 y 3: pueden filtrar por cualquier usuario
    if ((USER_TIPO === 2 || USER_TIPO === 3) && agentSelect.value) {
        params.append('user_id', agentSelect.value);
    }

    // Solo tipo 3: filtra por campaña
    if ((USER_TIPO === 3 || USER_TIPO === 2) && campaignSelect.value) {
        params.append('campaign_id', campaignSelect.value);
    }

    return "metrics.php?" + params.toString();
}

// ------------------------------
// Traer métricas desde PHP
// ------------------------------
function fetchMetrics() {
    fetch(buildURL())
        .then(res => res.json())
        .then(data => renderMetrics(data))
        .catch(err => console.error("Error al cargar métricas:", err));
}

function fetchResultados() {
    fetch("metrics.php?action=resultados")
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById("graficoResultados").getContext("2d");

            // Si ya existe un gráfico previo, lo destruimos para no superponer
            if (window.pieChartResultados) {
                window.pieChartResultados.destroy();
            }

            window.pieChartResultados = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: data.map(d => d.resultado),
                    datasets: [{
                        data: data.map(d => d.total),
                        backgroundColor: [
                            "#4CAF50", "#2196F3", "#FFC107",
                            "#F44336", "#9C27B0", "#FF9800"
                        ]
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',        // leyenda a la derecha
                            align: 'start',           // alinear al top
                            labels: {
                                color: '#ffffff',     // texto blanco
                                font: {
                                    size: 8,
                                    weight: 'bold'
                                },
                                boxWidth: 20,
                                padding: 10
                            }
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        })
        .catch(err => console.error("Error cargando resultados:", err));
}
// ------------------------------
// Cargar lista de agentes
// ------------------------------
function loadAgents() {
    fetch("get_agents.php")
        .then(res => res.json())
        .then(data => {
            data.forEach(agent => {
                const option = document.createElement('option');
                option.value = agent.id;
                option.textContent = agent.nombre;
                agentSelect.appendChild(option);
            });
        });
}

// ------------------------------
// Cargar lista de campañas
// ------------------------------
function loadCampaigns() {
    fetch("get_campaigns.php")
        .then(res => res.json())
        .then(data => {
            data.forEach(camp => {
                const option = document.createElement('option');
                option.value = camp.id;
                option.textContent = camp.nombre;
                campaignSelect.appendChild(option);
            });
        });
}

// ------------------------------
// Guardar snapshot
// ------------------------------
document.getElementById('guardarSnapshot').addEventListener('click', () => {
    if (USER_TIPO === 1) return alert("No tiene permisos para guardar snapshots");

    const nombre = document.getElementById('snapshotName').value;
    if(!nombre) return alert('Ingrese un nombre para el snapshot');

    const metricas = {};
    document.querySelectorAll('.metric-card').forEach(card => {
        const key = card.querySelector('h2').textContent.replace(/ /g, '_');
        metricas[key] = Number(card.dataset.value) || 0;
    });

    const filtros = {
        start_date: startDateInput.value,
        end_date: endDateInput.value,
        user_id: agentSelect.value,
        campaign_id: campaignSelect.value
    };

    fetch('save_snapshot.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `nombre=${encodeURIComponent(nombre)}&filtros=${encodeURIComponent(JSON.stringify(filtros))}&metricas=${encodeURIComponent(JSON.stringify(metricas))}`
    })
    .then(res => res.json())
    .then(resp => {
        if(resp.status === 'ok'){
            alert('Snapshot guardado');
            listarSnapshots();
        } else {
            alert('Error: ' + resp.msg);
        }
    });
});

// ------------------------------
// Listar snapshots
// ------------------------------
function listarSnapshots(){
    fetch('list_snapshots.php')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('snapshotsList');
            list.innerHTML = '';
            data.forEach(s => {
                const li = document.createElement('li');
                li.innerHTML = `<a href="#" data-id="${s.id}">${s.nombre} - ${s.fecha}</a>`;
                list.appendChild(li);
            });

            // Click para ver snapshot
            document.querySelectorAll('#snapshotsList a').forEach(a => {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    mostrarSnapshot(a.dataset.id);
                });
            });
        });
}

// ------------------------------
// Mostrar snapshot
// ------------------------------
function mostrarSnapshot(id){
    fetch(`get_snapshot.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            const metricas = JSON.parse(data.metricas);
            let html = `<h3>${data.nombre}</h3>`;
            for(const key in metricas){
                html += `<p>${key.replace(/_/g,' ')}: ${metricas[key]}%</p>`;
            }
            html += `<p><b>Filtros:</b> ${data.filtros}</p>`;
            document.getElementById('snapshotDetalle').innerHTML = html;
        });
}

// ------------------------------
// Inicializar
// ------------------------------
loadAgents();
loadCampaigns();

// Configurar filtros según tipo de usuario

if (USER_TIPO === 1) {
    agentSelect.disabled = true;
    campaignSelect.disabled = true;
} else if (USER_TIPO === 2) {
    agentSelect.disabled = false;
    campaignSelect.disabled = false; 
} else if (USER_TIPO === 3) {
    agentSelect.disabled = false;
    campaignSelect.disabled = false;
}


filterBtn.addEventListener('click', fetchMetrics);
fetchMetrics();
listarSnapshots();
fetchResultados();
