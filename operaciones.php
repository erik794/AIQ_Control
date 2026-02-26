<?php
include "conexion.php";
include "sidebar.php";

date_default_timezone_set('America/Mexico_City');
$hoy = date("Y-m-d");

$llegadas = $conexion->query("SELECT * FROM vuelos WHERE date = '$hoy' AND flight_type = 1 ORDER BY hour ASC");
?>

<div class="contenido">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="color:rgb(0, 0, 0); margin: 0;">Monitor de Llegadas</h2>
            <p style="color: #7f8c8d; margin: 5px 0 0 0;">Control de vuelos entrantes para el día de hoy.</p>
        </div>

        <div style="position: relative; width: 320px;">
            <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;">🔍</span>
            <input type="text" id="inputBusqueda" placeholder="Buscar aerolínea, vuelo u origen..." 
                   style="width: 100%; padding: 12px 15px 12px 40px; border-radius: 30px; border: 1px solid #dcdde1; outline: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s;">
        </div>
    </div>

    <div class="contenedor-tabla">
        <table class="tabla" id="tablaLlegadas">
            <thead>
                <tr style="background-color:rgb(8, 99, 190); color: white;">
                    <th>Aerolínea</th>
                    <th>Vuelo</th>
                    <th>Origen</th>
                    <th style="text-align: center;">Hora Estimada</th>
                    <th style="text-align: center;">Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($llegadas && $llegadas->num_rows > 0): ?>
                    <?php while($l = $llegadas->fetch_assoc()): ?>
                    <tr class="fila-vuelo">
                        <td class="col-aerolinea">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 4px; height: 20px; background:rgb(8, 99, 190); margin-right: 10px; border-radius: 10px;"></div>
                                <strong><?= htmlspecialchars($l['airline_name']) ?></strong>
                            </div>
                        </td>
                        <td class="col-vuelo">
                            <span style="background: #f1f2f6; padding: 5px 10px; border-radius: 5px; font-weight: bold; color: #2c3e50;">
                                <?= htmlspecialchars($l['flight_number']) ?>
                            </span>
                        </td>
                        <td class="col-origen"><?= htmlspecialchars($l['origin']) ?></td>
                        <td style="text-align: center; font-weight: bold; font-size: 1.1em;">
                            <?= sprintf('%02d:%02d', $l['hour'], $l['minute']) ?>
                        </td>
                        <td style="text-align: center;">
                            <?php 
                                $st = $l['status'];
                                $color = ($st == 'A Tiempo') ? '#27ae60' : (($st == 'Demorado') ? '#e67e22' : '#c0392b');
                            ?>
                            <span style="color: white; background: <?= $color ?>; padding: 5px 15px; border-radius: 20px; font-size: 0.8em; font-weight: bold; text-transform: uppercase;">
                                <?= $st ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 50px; color: #95a5a6;">
                            <h3>No hay llegadas registradas para hoy</h3>
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

    filas.forEach(fila => {
        let aerolinea = fila.querySelector('.col-aerolinea').textContent.toLowerCase();
        let vuelo = fila.querySelector('.col-vuelo').textContent.toLowerCase();
        let origen = fila.querySelector('.col-origen').textContent.toLowerCase();

        if (aerolinea.includes(filtro) || vuelo.includes(filtro) || origen.includes(filtro)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
});
</script>

<style>
    .contenedor-tabla {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .tabla { width: 100%; border-collapse: collapse; }
    .tabla th { padding: 18px; text-align: left; font-size: 0.9em; letter-spacing: 1px; }
    .tabla td { padding: 15px; border-bottom: 1px solid #f1f2f6; color: #34495e; }
    .tabla tr:hover { background-color: #f9fffb; }
    #inputBusqueda:focus { border-color:hsl(209, 87.80%, 38.60%); box-shadow: 0 0 10px rgba(39, 174, 96, 0.2); }
</style>