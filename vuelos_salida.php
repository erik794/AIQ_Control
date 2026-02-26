<?php
include "conexion.php";
include "sidebar.php";

date_default_timezone_set('America/Mexico_City');
$hoy = date("Y-m-d");


$llegadas = $conexion->query("
    SELECT v.* FROM vuelos v 
    WHERE v.date = '$hoy' 
    AND v.flight_type = 1 
    AND (v.vuelo_salida IS NULL OR v.vuelo_salida = '')
    ORDER BY v.hour ASC, v.minute ASC
");


$res_salidas = $conexion->query("
    SELECT flight_number, destination, airline_name, hour, minute 
    FROM vuelos 
    WHERE date = '$hoy' 
    AND flight_type = 0 
    AND flight_number NOT IN (
        SELECT DISTINCT vuelo_salida FROM vuelos 
        WHERE date = '$hoy' AND flight_type = 1 AND vuelo_salida IS NOT NULL AND vuelo_salida != ''
    )
    ORDER BY hour ASC
");
$lista_salidas = $res_salidas->fetch_all(MYSQLI_ASSOC);
?>

<style>
    :root { 
        --primary: #005cb9; 
        --success: #27ae60;
        --dark: #2c3e50;
        --bg: #f8f9fa; 
    }

    body { background: var(--bg); font-family: 'Inter', sans-serif; margin: 0; }
    .contenido { padding: 40px; margin-left: 260px; min-height: 100vh; }

    .card-tabla { background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
    .tabla { width: 100%; border-collapse: collapse; }
    .tabla th { background: var(--dark); color: white; padding: 18px; text-transform: uppercase; font-size: 11px; text-align: left; }
    .fila-gestion td { padding: 15px 18px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }

    .select-custom { 
        padding: 10px; border-radius: 8px; border: 1px solid #dce1e7; 
        width: 100%; background: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
        color: #333;
    }

    .badge-hora { background: #edf2f7; color: var(--dark); padding: 5px 10px; border-radius: 6px; font-family: monospace; font-weight: bold; }

    .btn-vincular { 
        background: var(--success); color: white; border: none; padding: 12px; 
        border-radius: 10px; cursor: pointer; font-weight: 700; width: 100%; transition: 0.3s;
    }
    .btn-vincular:hover { background: #219150; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(39,174,96,0.2); }
</style>

<div class="contenido">
    <div style="margin-bottom: 30px;">
        <h2 style="margin:0; color: var(--dark);">Vinculación de Vuelos</h2>
        <p style="color: #7f8c8d;">Selecciona a qué vuelo de salida se le asignará el avión que llega.</p>
    </div>

    <div class="card-tabla">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Aerolínea</th>
                    <th>Avión (Llegada)</th>
                    <th>Hora Arribo</th>
                    <th style="width: 350px;">Asignar a Vuelo de Salida</th>
                    <th style="text-align: center;">¿Pernocta?</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($llegadas as $l): ?>
                <tr class="fila-gestion" data-h-lleg="<?= $l['hour'] ?>" data-m-lleg="<?= $l['minute'] ?>">
                    <form method="POST" action="update_vuelo.php">
                        <td style="font-weight: 800; color: var(--primary);"><?= htmlspecialchars($l['airline_name']) ?></td>
                        <td><span style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 5px; font-weight: bold;"><?= $l['flight_number'] ?></span></td>
                        <td><span class="badge-hora"><?= sprintf('%02d:%02d', $l['hour'], $l['minute']) ?></span></td>

                        <td>
                            <select name="vuelo_salida" class="select-custom" required onchange="detectarPernocta(this)">
                                <option value="">Seleccionar Salida</option>
                                <?php foreach($lista_salidas as $s): 
                                    if(strtoupper($s['airline_name']) == strtoupper($l['airline_name'])): 
                                        $texto_opcion = $s['flight_number'] . " (Dest: " . $s['destination'] . " - " . sprintf('%02d:%02d', $s['hour'], $s['minute']) . ")";
                                    ?>
                                    <option value="<?= htmlspecialchars($s['flight_number']) ?>" 
                                            data-h-sal="<?= $s['hour'] ?>" 
                                            data-m-sal="<?= $s['minute'] ?>">
                                        <?= htmlspecialchars($texto_opcion) ?>
                                    </option>
                                <?php endif; endforeach; ?>
                            </select>
                        </td>

                        <td style="text-align: center;">
                            <label style="cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                <input type="checkbox" name="es_pernocta" value="1" style="transform: scale(1.4);">
                                <span style="font-size: 11px; font-weight: bold; color: #7f8c8d;">Pernocta 🌙</span>
                            </label>
                        </td>

                        <td>
                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                            <button type="submit" class="btn-vincular">ASIGNAR</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function detectarPernocta(select) {
    const fila = select.closest('tr');
    const checkPernocta = fila.querySelector('input[name="es_pernocta"]');
    const opt = select.options[select.selectedIndex];
    
    if (!opt.value) return;


    const hLleg = parseInt(fila.getAttribute('data-h-lleg'));
    const mLleg = parseInt(fila.getAttribute('data-m-lleg'));
    
  
    const hSal = parseInt(opt.getAttribute('data-h-sal'));
    const mSal = parseInt(opt.getAttribute('data-m-sal'));

    const totalLlegada = (hLleg * 60) + mLleg;
    const totalSalida = (hSal * 60) + mSal;

    
    if (totalLlegada >= totalSalida) {
        checkPernocta.checked = true;
    } else {
        checkPernocta.checked = false;
    }
}
</script>