# proteus
Un pequeño dashboard con metricas desde una base de datos.

# Proteus Dashboard

## Descripción
Proteus Dashboard es una aplicación web para monitorear métricas de un equipo, gestionar snapshots y visualizar resultados mediante gráficos interactivos.  
El proyecto incluye un sistema de login, control de sesiones y dashboards con gráficos circulares y pie charts, listo para desplegar en hosting gratuito como InfinityFree.

---

## Requisitos
- PHP 7.4+ (InfinityFree soporta PHP 7.4 – 8.1)  
- MySQL 5.7+  
- Navegador moderno (Chrome, Firefox, Edge)  
- Conexión a internet para cargar Chart.js desde CDN  

---

## Instalación / Despliegue
1. Subir todos los archivos del proyecto al hosting (ej. InfinityFree) en la carpeta `htdocs` o raíz del dominio.  
2. Crear la base de datos en el panel de InfinityFree:
   - Host: `sql208.infinityfree.com`
   - Database Name: `if0_40028271_proteus`
   - Username: `if0_40028271`
   - Password: la que configuraste al crear la BD
3. Importar el archivo `tabla.sql` desde phpMyAdmin del hosting para crear las tablas y registros iniciales.  
4. Configurar las credenciales en `login.php` (host, DB, user, password) según el panel de InfinityFree.  

---

## Uso
- Acceder al dominio (`- Acceder al dominio https://proteus.infinityfreeapp.com/log.html)/`) → redirige automáticamente al login.  `) 
- Ingresar credenciales válidas.  
  supervisor : -usuario : amartinez - password: hashabc
  agente : - usuario : crodriguez - password: hash789
- Dashboard:
  - Visualiza métricas circulares y pie charts de resultados.  
  - Gestiona snapshots: crear, ver y eliminar.  
  - Botón “Cerrar sesión” para terminar la sesión y volver al login.  

---

## Estructura de archivos
/htdocs
│── index.php # Landing page → redirige al login
│── login.php # Página de login
│── logout.php # Cierra sesión
│── dashboard.php # Dashboard principal
│── metrics.php # API interna para obtener métricas (JSON)
│── script.js # Lógica de gráficos y snapshots
│── styles.css # Estilos generales
│── tabla.sql # SQL para crear tablas y registros iniciales

----
Prioridades y workflow : 
1.    lograr que se mostraran las metricas en pantalla llamandolas desde la base de datos
2.    crear el dashboard correspondiente y su style.css
3.    crear la funcionalidad de snapshots y que se guardaran en la base de datos
4.    Crear un login que luego permitiera dentro de el dashboard, que segun el tipo de user, se vizualisaran distintos filtros
5.    Crear el piechart para las metricas secundarias y ajustar el css para que fuera legible y seleccionable
6.    Crear el boton de cierre de sesion para completar la UI de el agente y que nos re-diriga al login.
---

## Créditos
- Autor: Guillermo Neves  
- Proyecto: Prueba técnica para aplicar a posición IT / desarrollo web  
- Librerías: [Chart.js](https://www.chartjs.org/) para visualización de gráficos

---

## Notas
- Este proyecto está pensado para desplegarse en hosting gratuito como InfinityFree.  
- Se recomienda usar rutas relativas y respetar mayúsculas/minúsculas en nombres de archivos (case sensitive).  
- Para pruebas locales, modificar las credenciales de MySQL a `localhost` y usuario/password de tu entorno.
