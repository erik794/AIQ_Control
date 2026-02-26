<?php
include "conexion.php";
include "sidebar.php";

date_default_timezone_set('America/Mexico_City');
$hoy = date("Y-m-d");


$vuelos = $conexion->query("
    SELECT 
        airline_name, 
        flight_number, 
        flight_type, 
        origin, 
        destination, 
        hour, 
        minute, 
        status
    FROM vuelos 
    WHERE date = '$hoy' 
    AND (vuelo_salida IS NULL OR vuelo_salida = '') 
    AND flight_number NOT IN (
        SELECT DISTINCT vuelo_salida 
        FROM vuelos 
        WHERE date = '$hoy' 
        AND vuelo_salida IS NOT NULL 
        AND vuelo_salida != ''
    )
    ORDER BY hour ASC, minute ASC
");


$total_vuelos = ($vuelos) ? $vuelos->num_rows : 0;
?>

<div class="contenido">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div>
                <h2 style="color: #2c3e50; margin: 0;">Vuelos Generales</h2>
                <p style="color: #7f8c8d; margin: 5px 0 0 0;">Tablero de operaciones para el día: <strong><?= $hoy ?></strong></p>
            </div>
            <div style="background: #3498db; color: white; padding: 5px 15px; border-radius: 10px; font-weight: bold; box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);">
                <span style="font-size: 0.8em; opacity: 0.9;">Total:</span> 
                <span style="font-size: 1.2em;"><?= $total_vuelos ?></span>
            </div>
        </div>
        
        <div style="position: relative; width: 300px;">
            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #95a5a6;">🔍</span>
            <input type="text" id="inputBusqueda" placeholder="Buscar por vuelo o aerolínea..." 
                   style="width: 100%; padding: 10px 10px 10px 35px; border-radius: 25px; border: 1px solid #dcdde1; outline: none; transition: all 0.3s ease; font-family: inherit;">
        </div>
    </div>

    <div class="contenedor-tabla">
        <table class="tabla" id="tablaVuelos">
            <thead>
                <tr>
                    <th>Aerolínea</th>
                    <th>Vuelo</th>
                    <th>Operación</th>
                    <th style="text-align: center;">Ruta (Origen ➔ Destino)</th>
                    <th>Hora Programada</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($vuelos && $vuelos->num_rows > 0): ?>
                    <?php while ($v = $vuelos->fetch_assoc()): ?>
                    <tr class="fila-vuelo">
                        <td class="col-aerolinea">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 4px; height: 25px; background: #34495e; margin-right: 10px; border-radius: 2px;"></div>
                                <strong><?= htmlspecialchars($v['airline_name']) ?></strong>
                            </div>
                        </td>
                        <td class="col-vuelo"><b class="vuelo-tag"><?= htmlspecialchars($v['flight_number']) ?></b></td>
                        <td>
                            <?php if ($v['flight_type'] == 1): ?>
                                <span class="badge llegada">🛬 LLEGADA</span>
                            <?php else: ?>
                                <span class="badge salida">🛫 SALIDA</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div class="ruta">
                                <span><?= htmlspecialchars($v['origin']) ?></span>
                                <span class="flecha">➔</span>
                                <span><?= htmlspecialchars($v['destination']) ?></span>
                            </div>
                        </td>
                        <td style="font-size: 1.1em; color: #2c3e50;">
                            <strong><?= sprintf('%02d:%02d', $v['hour'], $v['minute']) ?></strong>
                        </td>
                        <td>
                            <?php 
                                $st = strtoupper($v['status']);
                                $color_bg = "#27ae60"; 
                                if (strpos($st, 'DEMORADO') !== false) $color_bg = "#e67e22"; 
                                if (strpos($st, 'CANCELADO') !== false) $color_bg = "#c0392b"; 
                            ?>
                            <span class="status-pill" style="background: <?= $color_bg ?>;">
                                <?= $st ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr id="sinResultados">
                        <td colspan="6" class="tabla-vacia">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="80" style="opacity: 0.3; margin-bottom: 15px;">
                            <h3>No hay vuelos disponibles</h3>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('inputBusqueda').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('.fila-vuelo');
    let hayResultados = false;

    filas.forEach(fila => {
        let aerolinea = fila.querySelector('.col-aerolinea').textContent.toLowerCase();
        let vuelo = fila.querySelector('.col-vuelo').textContent.toLowerCase();

        if (aerolinea.includes(filtro) || vuelo.includes(filtro)) {
            fila.style.display = "";
            hayResultados = true;
        } else {
            fila.style.display = "none";
        }
    });
});
</script>

<style>
    #inputBusqueda:focus {
        border-color: #3498db;
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
    }
    
    .contenedor-tabla { background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; }
    .tabla { width: 100%; border-collapse: collapse; }
    .tabla th { background:rgb(8, 99, 190); color: #ecf0f1; padding: 18px 15px; text-align: left; font-size: 0.9em; text-transform: uppercase; letter-spacing: 1px; }
    .tabla td { padding: 15px; border-bottom: 1px solid #f1f2f6; }
    .vuelo-tag { background: #f1f2f6; padding: 4px 8px; border-radius: 4px; color: #2c3e50; border: 1px solid #dcdde1; }
    .badge { padding: 6px 12px; border-radius: 6px; font-size: 0.75em; font-weight: 800; display: inline-block; }
    .llegada { background: #eafaf1; color: #27ae60; }
    .salida { background: #ebf5fb; color: #2980b9; }
    .ruta { display: flex; align-items: center; justify-content: center; font-weight: bold; color: #34495e; }
    .flecha { color: #bdc3c7; margin: 0 12px; font-weight: normal; }
    .status-pill { color: white; padding: 5px 14px; border-radius: 20px; font-size: 0.75em; font-weight: bold; letter-spacing: 0.5px; }
    .tabla-vacia { text-align: center; padding: 80px 20px !important; color: #95a5a6; }
</style>