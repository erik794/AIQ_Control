<?php
include "conexion.php";
include "sidebar.php";

date_default_timezone_set('America/Mexico_City');
$hoy = date("Y-m-d");
$error_msg = ""; 

// 1. PROCESADOR DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    $id_salida = intval($_POST['id_salida']);
    $id_llegada = intval($_POST['id_llegada']);
    $accion = $_POST['accion'];

    if ($accion == 'update') {
        $h_lleg = intval($_POST['h_lleg']);
        $m_lleg = intval($_POST['m_lleg']);
        $h_sal = intval($_POST['h_sal']);
        $m_sal = intval($_POST['m_sal']);
        $pos = !empty($_POST['posicion_id']) ? intval($_POST['posicion_id']) : null;

        if ($pos) {
            $nuevo_inicio = ($h_lleg * 60) + $m_lleg;
            $nuevo_fin = ($h_sal * 60) + $m_sal;

            $check_query = "
                SELECT v.flight_number as f_sal, v2.flight_number as f_lleg, 
                       v.hour as hs, v.minute as ms, v2.hour as hl, v2.minute as ml
                FROM vuelos v
                INNER JOIN vuelos v2 ON v.vuelo_salida = v2.flight_number AND v2.date = v.date
                WHERE v.posicion_id = $pos 
                AND v.date = '$hoy' 
                AND v.id != $id_salida 
                AND v2.id != $id_llegada
            ";
            $res_check = $conexion->query($check_query);

            $hay_choque = false;
            while ($row = $res_check->fetch_assoc()) {
                $existente_inicio = ($row['hl'] * 60) + $row['ml'];
                $existente_fin = ($row['hs'] * 60) + $row['ms'];
                if ($nuevo_inicio < $existente_fin && $nuevo_fin > $existente_inicio) {
                    $hay_choque = true;
                    $vuelo_conflicto = $row['f_lleg'] . "/" . $row['f_sal'];
                    break;
                }
            }

            if ($hay_choque) {
                echo "<script>alert('⚠️ Conflicto: La posición ya está ocupada por el vuelo $vuelo_conflicto.');</script>";
            } else {
                $conexion->query("UPDATE vuelos SET hour = $h_lleg, minute = $m_lleg, posicion_id = $pos WHERE id = $id_llegada");
                $conexion->query("UPDATE vuelos SET hour = $h_sal, minute = $m_sal, posicion_id = $pos WHERE id = $id_salida");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    } elseif ($accion == 'delete') {
        $conexion->query("UPDATE vuelos SET vuelo_salida = NULL, posicion_id = NULL WHERE id = $id_salida");
        $conexion->query("UPDATE vuelos SET vuelo_salida = NULL, posicion_id = NULL WHERE id = $id_llegada");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// 2. CONSULTAS
$safe_hoy = $conexion->real_escape_string($hoy);
$ligados_res = $conexion->query("
    SELECT 
        v.id AS id_salida, v.flight_number AS f_salida, v.hour AS h_sal, v.minute AS m_sal, v.airline_name,
        v2.id AS id_llegada, v2.flight_number AS f_lleg, v2.hour AS h_lleg, v2.minute AS m_lleg,
        p.nombre AS pos_nombre, v.posicion_id
    FROM vuelos v
    INNER JOIN vuelos v2 ON v.vuelo_salida = v2.flight_number AND v2.date = v.date AND v2.flight_type = 1
    LEFT JOIN posiciones p ON p.id = v.posicion_id
    WHERE v.date = '$safe_hoy' AND v.flight_type = 0
    ORDER BY h_lleg ASC
");

$ligados = $ligados_res->fetch_all(MYSQLI_ASSOC);
$todas_posiciones = $conexion->query("SELECT * FROM posiciones ORDER BY LENGTH(nombre), nombre ASC")->fetch_all(MYSQLI_ASSOC);

function colorAerolinea($nombre) {
    $nombre = strtoupper($nombre);
    if (strpos($nombre, 'AMERICAN') !== false) return '#2d3436'; 
    if (strpos($nombre, 'UNITED') !== false) return '#0984e3'; 
    if (strpos($nombre, 'AERO') !== false) return '#005cb9'; 
    if (strpos($nombre, 'VOLA') !== false) return '#a3238e'; 
    if (strpos($nombre, 'VIVA') !== false) return '#4f8a10'; 
    return '#636e72'; 
}
?>

<style>
    :root { --primary-blue: #005cb9; --bg-gray: #f4f7f9; --sidebar-width: 250px; }
    body { background-color: var(--bg-gray); margin: 0; font-family: 'Segoe UI', sans-serif; }
    .contenido { padding: 30px; margin-left: var(--sidebar-width); min-height: 100vh; }
    
    .tabla-contenedor { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .tabla-gestion { width: 100%; border-collapse: collapse; }
    .tabla-gestion th { padding: 15px; font-size: 11px; color: #718096; text-transform: uppercase; background: #f8f9fa; }
    .tabla-gestion td { padding: 12px 15px; text-align: center; border-bottom: 1px solid #edf2f7; }

    .f-badge { padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 13px; }
    .llegada { color: #2f855a; background: #f0fff4; }
    .salida { color: #2b6cb0; background: #ebf8ff; }
    .inp-h { border: 1px solid #e2e8f0; border-radius: 4px; padding: 4px; width: 45px; text-align: center; font-weight: bold; }
    
    /* Tiempos */
    .countdown { font-family: monospace; font-weight: 700; padding: 6px 10px; border-radius: 6px; font-size: 12px; min-width: 110px; display: inline-block; }
    .espera { background: #edf2f7; color: #4a5568; } 
    .en-plataforma { background: #feebc8; color: #9c4221; border: 1px solid #fbd38d; } 
    .finalizado { background: #c6f6d5; color: #22543d; }

    /* Alerta de Posición Mejorada */
    .alerta-pos { 
        background: #fff5f5 !important; 
        border: 2px solid #feb2b2 !important; 
        animation: pulse-red 2s infinite ease-in-out;
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0px rgba(229, 62, 62, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(229, 62, 62, 0); }
        100% { box-shadow: 0 0 0 0px rgba(229, 62, 62, 0); }
    }

    /* Estilos GANTT CORREGIDOS */
    .gantt-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow-x: auto; }
    .gantt-container { min-width: 1200px; position: relative; }
    .gantt-header { display: flex; margin-bottom: 10px; padding-left: 100px; border-bottom: 2px solid #f1f5f9; }
    .gantt-time-slot { flex: 1; text-align: center; font-size: 10px; color: #94a3b8; border-left: 1px solid #f8fafc; min-width: 40px; }
    .gantt-row { display: flex; align-items: center; height: 42px; border-bottom: 1px solid #f8fafc; }
    .gantt-label { width: 100px; font-weight: 800; font-size: 11px; color: #334155; flex-shrink: 0; background: white; z-index: 2; }
    .gantt-timeline { flex: 1; position: relative; background: #fafafa; height: 30px; border-radius: 4px; }
    .gantt-bar { 
        position: absolute; 
        height: 100%; 
        border-radius: 4px; 
        color: white; 
        font-size: 10px; 
        font-weight: bold; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 0 4px;
    }
</style>

<div class="contenido">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="color: #1a202c; margin: 0; font-size: 28px; font-weight: 800;">Monitor de Turnaround</h2>
            <p style="color: #718096; margin: 5px 0 0 0;">Control de Flujos 24 Horas</p>
        </div>
        <div id="reloj-actual" style="background: #2d3436; color: #00ffcc; padding: 15px 30px; border-radius: 12px; font-family: monospace; font-size: 1.6em; font-weight: bold;"></div>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Aerolínea</th>
                    <th>Vuelo ARR / DEP</th>
                    <th>Horarios</th>
                    <th>Posición</th>
                    <th>Estatus / Tiempo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($ligados as $l): 
                    $color = colorAerolinea($l['airline_name']);
                    $h_lleg_iso = sprintf('%02d:%02d:00', $l['h_lleg'], $l['m_lleg']);
                    $h_sal_iso = sprintf('%02d:%02d:00', $l['h_sal'], $l['m_sal']);
                ?>
                <tr>
                    <form method="POST">
                        <td style="text-align:left; border-left: 5px solid <?= $color ?>; font-weight:bold;">
                            <?= htmlspecialchars($l['airline_name']) ?>
                        </td>
                        <td>
                            <span class="f-badge llegada"><?= $l['f_lleg'] ?></span>
                            <span style="color:#cbd5e0">➔</span>
                            <span class="f-badge salida"><?= $l['f_salida'] ?></span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                                <input type="number" name="h_lleg" value="<?= $l['h_lleg'] ?>" class="inp-h" max="23" min="0">:
                                <input type="number" name="m_lleg" value="<?= $l['m_lleg'] ?>" class="inp-h" max="59" min="0">
                                <span style="margin: 0 10px">|</span>
                                <input type="number" name="h_sal" value="<?= $l['h_sal'] ?>" class="inp-h" max="23" min="0">:
                                <input type="number" name="m_sal" value="<?= $l['m_sal'] ?>" class="inp-h" max="59" min="0">
                            </div>
                        </td>
                        <td class="<?= (!$l['posicion_id']) ? 'alerta-js' : '' ?>" data-llegada="<?= $h_lleg_iso ?>">
                            <select name="posicion_id" style="padding: 6px; border-radius: 6px; font-weight: 600;">
                                <option value="">Sin Asignar</option>
                                <?php foreach($todas_posiciones as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($p['id'] == $l['posicion_id']) ? 'selected' : '' ?>><?= $p['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <div class="countdown espera" data-llegada="<?= $h_lleg_iso ?>" data-salida="<?= $h_sal_iso ?>">
                                Calculando...
                            </div>
                        </td>
                        <td>
                            <input type="hidden" name="id_salida" value="<?= $l['id_salida'] ?>">
                            <input type="hidden" name="id_llegada" value="<?= $l['id_llegada'] ?>">
                            <button type="submit" name="accion" value="update" style="border:none; background:none; cursor:pointer; font-size:1.2rem;">💾</button>
                            <button type="submit" name="accion" value="delete" style="border:none; background:none; cursor:pointer; font-size:1.2rem;" onclick="return confirm('¿Desligar?')">🔗</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="gantt-card">
        <h3 style="margin-top:0">Ocupación de Posiciones (Vista 24h)</h3>
        <div class="gantt-container">
            <div class="gantt-header">
                <?php for($i=0; $i<24; $i++): ?>
                    <div class="gantt-time-slot"><?= sprintf('%02d:00', $i) ?></div>
                <?php endfor; ?>
            </div>

            <?php foreach($todas_posiciones as $p): ?>
            <div class="gantt-row">
                <div class="gantt-label"><?= $p['nombre'] ?></div>
                <div class="gantt-timeline">
                    <?php foreach($ligados as $l): if($l['posicion_id'] == $p['id']): 
                        $start_min = ($l['h_lleg'] * 60) + $l['m_lleg'];
                        $end_min = ($l['h_sal'] * 60) + $l['m_sal'];
                        
                        if ($end_min <= $start_min) $end_min = 1440; 
                        
                        $width_pct = (($end_min - $start_min) / 1440) * 100;
                        $left_pct = ($start_min / 1440) * 100;
                    ?>
                        <div class="gantt-bar" 
                             style="left:<?= $left_pct ?>%; width:<?= $width_pct ?>%; background:<?= colorAerolinea($l['airline_name']) ?>;" 
                             title="<?= $l['f_lleg'] ?> (<?= sprintf('%02d:%02d', $l['h_lleg'], $l['m_lleg']) ?> a <?= sprintf('%02d:%02d', $l['h_sal'], $l['m_sal']) ?>)">
                            <?= $l['f_lleg'] ?>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function formatTimeDiff(ms) {
    const totalMinutes = Math.floor(Math.abs(ms) / 60000);
    const h = Math.floor(totalMinutes / 60);
    const m = totalMinutes % 60;
    return (h > 0) ? `${h}h ${m}m` : `${m}m`;
}

function updateDashboard() {
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    const hoyStr = `${y}-${m}-${d}`;

    document.getElementById('reloj-actual').innerText = now.toLocaleTimeString('es-MX', {hour12: false});

    document.querySelectorAll('.countdown').forEach(div => {
        const tLlegada = new Date(hoyStr + 'T' + div.getAttribute('data-llegada'));
        let tSalida = new Date(hoyStr + 'T' + div.getAttribute('data-salida'));
        
        if (tSalida <= tLlegada) tSalida.setDate(tSalida.getDate() + 1);
        
        // --- MARGEN DE 5 MINUTOS ---
        const margenMS = 5 * 60 * 1000;
        const radarInicio = new Date(tLlegada.getTime() - margenMS);
        const radarFin = new Date(tSalida.getTime() + margenMS);

        if (now < radarInicio) {
            const diff = tLlegada - now;
            div.innerText = `LLEGA EN: ${formatTimeDiff(diff)}`;
            div.className = "countdown espera";
        } 
        else if (now >= radarInicio && now <= radarFin) {
            const diff = now - tLlegada; 
            div.innerText = `EN PLAT: ${formatTimeDiff(diff)}`;
            div.className = "countdown en-plataforma";
        } 
        else {
            div.innerText = "DESPEGADO";
            div.className = "countdown finalizado";
        }
    });

    document.querySelectorAll('.alerta-js').forEach(td => {
        const tLlegada = new Date(hoyStr + 'T' + td.getAttribute('data-llegada'));
        const diffMin = (tLlegada - now) / 60000;
        
        if (diffMin <= 30 && diffMin > -10) {
            td.classList.add('alerta-pos');
        } else {
            td.classList.remove('alerta-pos');
        }
    });
}

setInterval(updateDashboard, 1000);
updateDashboard();
</script>