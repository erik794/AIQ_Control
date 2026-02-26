<?php
include "conexion.php";
include "sidebar.php";

date_default_timezone_set('America/Mexico_City');
$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day")); 
$ahora_obj = new DateTime(); 


$query = "
    SELECT 
        v.airline_name, v.posicion_id, p.nombre AS pos_nombre, p.tipo AS pos_tipo,
        v.flight_number AS vuelo_ent, v.hour AS h_ent, v.minute AS m_ent, v.date AS fecha_ent,
        v2.flight_number AS vuelo_sal, v2.hour AS h_sal, v2.minute AS m_sal, v2.date AS fecha_sal,
        v.status
    FROM vuelos v
    INNER JOIN vuelos v2 ON v.vuelo_salida = v2.flight_number 
        AND v.posicion_id = v2.posicion_id
        AND v2.flight_type = 0 
    INNER JOIN posiciones p ON v.posicion_id = p.id
    WHERE 
        (v.date = '$hoy' AND v.flight_type = 1)
        OR
        (v.date = '$ayer' AND v2.date = '$hoy' AND v.flight_type = 1)
    GROUP BY 
        v.airline_name, v.posicion_id, p.nombre, p.tipo, 
        v.flight_number, v.hour, v.minute, v.date,
        v2.flight_number, v2.hour, v2.minute, v2.date, v.status
    ORDER BY v.date ASC, v.hour ASC, v.minute ASC
";

$res_vuelos = $conexion->query($query);
$vuelos_en_pantalla = [];

if ($res_vuelos) {
    while($v = $res_vuelos->fetch_assoc()) {
        // Usamos la fecha real de la base de datos para los objetos DateTime
        $hora_llegada = new DateTime("{$v['fecha_ent']} {$v['h_ent']}:{$v['m_ent']}:00");
        $hora_salida = new DateTime("{$v['fecha_sal']} {$v['h_sal']}:{$v['m_sal']}:00");
        
        // Configuración de márgenes (Aparece 5m antes de llegar, desaparece 5m después de salir)
        $margen_aparicion = (clone $hora_llegada)->modify("-5 minutes");
        $margen_desaparicion = (clone $hora_salida)->modify("+5 minutes");

        if($ahora_obj >= $margen_aparicion && $ahora_obj <= $margen_desaparicion) {
            
            if ($ahora_obj > $hora_salida) {
                $status_txt = "DESPEGADO";
                $clase_status = "st-finalizado";
                $tiempo_plat = "Operación Finalizada";
                $es_activa = false;
            } elseif ($ahora_obj >= $hora_llegada) {
                $status_txt = "EN POSICIÓN";
                $clase_status = "st-occupied";
                $intervalo = $hora_llegada->diff($ahora_obj);
                $tiempo_plat = "Tiempo en puerta: " . $intervalo->format('%h h %i m');
                $es_activa = true;
            } else {
                $status_txt = "EN CAMINO";
                $clase_status = "st-incoming";
                $intervalo_lleg = $ahora_obj->diff($hora_llegada);
                $minutos_faltan = ($intervalo_lleg->days * 1440) + ($intervalo_lleg->h * 60) + $intervalo_lleg->i;
                $tiempo_plat = "Llega en: " . $minutos_faltan . " min";
                $es_activa = false;
            }

            $vuelos_en_pantalla[] = [
                'pos_nombre' => $v['pos_nombre'],
                'pos_tipo'   => $v['pos_tipo'],
                'aerolinea'  => $v['airline_name'],
                'vuelo_ent'  => $v['vuelo_ent'],
                'vuelo_sal'  => $v['vuelo_sal'],
                'status'     => $status_txt,
                'clase_st'   => $clase_status,
                'tiempo_plat'=> $tiempo_plat,
                'prog_lleg'  => sprintf('%02d:%02d', $v['h_ent'], $v['m_ent']),
                'prog_sal'   => sprintf('%02d:%02d', $v['h_sal'], $v['m_sal']),
                'highlight'  => $es_activa
            ];
        }
    }
}
?>

<style>
    :root { --dark-bg: #1e272e; --card-bg: #2d3436; --accent-red: #d63031; --accent-blue: #0984e3; --accent-green: #2ecc71; }
    .contenido { padding: 30px; margin-left: 250px; background: #f1f2f6; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
    
    .grid-plataforma { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 25px; padding: 20px 0; }
    
    .card-posicion { display: flex; background: var(--card-bg); border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid #444; transition: transform 0.2s; }
    .card-posicion:hover { transform: translateY(-5px); }
    .active-border { border: 2px solid #ff7675 !important; box-shadow: 0 0 15px rgba(214, 48, 49, 0.4) !important; }
    
    .side-pos { color: white; padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 90px; text-align: center; }
    .side-pos .num { font-size: 2.2em; font-weight: 900; line-height: 1; }
    .side-pos .type { font-size: 0.7em; font-weight: bold; opacity: 0.8; margin-top: 5px; text-transform: uppercase; }

    .bg-occupied { background: var(--accent-red) !important; } 
    .bg-incoming { background: var(--accent-blue) !important; }
    .bg-finalizado { background: #636e72 !important; }

    .main-info { padding: 15px 20px; flex-grow: 1; color: white; display: flex; flex-direction: column; justify-content: space-between; }
    .top-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .airline { font-weight: bold; color: #fab1a0; font-size: 0.95em; text-transform: uppercase; }
    
    .badge-status { font-size: 0.75em; padding: 4px 12px; border-radius: 20px; font-weight: bold; color: white; }
    .st-occupied { background: var(--accent-red); }
    .st-incoming { background: var(--accent-blue); }
    .st-finalizado { background: #636e72; color: #b2bec3; }

    .vuelos-row { display: flex; align-items: center; background: rgba(0,0,0,0.4); border-radius: 8px; padding: 12px; }
    .v-box { flex: 1; text-align: center; }
    .v-box small { display: block; font-size: 0.65em; color: #b2bec3; margin-bottom: 4px; }
    .v-box strong { font-size: 1.4em; color: #ff7675; letter-spacing: 1px; }
    .v-divider { width: 1px; height: 35px; background: #444; margin: 0 15px; }

    .footer-row { margin-top: 12px; font-size: 0.9em; border-top: 1px solid #444; padding-top: 10px; color: #ffeaa7; font-weight: 600; }
    
    .empty-state { grid-column: 1 / -1; text-align: center; padding: 100px; background: white; border-radius: 20px; border: 3px dashed #ccc; color: #a4b0be; }
</style>

<div class="contenido">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="margin:0; color:var(--dark-bg); font-size: 2em;">Torre de Control</h2>
            <p style="margin:0; color:#7f8c8d; font-weight: 500;">Monitoreo en tiempo real (Salida +5m de margen)</p>
        </div>
        <div id="reloj-torre" style="background:var(--dark-bg); color:var(--accent-green); padding:12px 25px; border-radius:10px; font-family:monospace; font-size:1.8em; font-weight:bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
            <?= date("H:i:s") ?>
        </div>
    </div>

    <div style="width: 100%; height: 6px; background: #dfe6e9; border-radius: 10px; margin-bottom: 30px; overflow: hidden;">
        <div id="progress-bar" style="width: 100%; height: 100%; background: var(--accent-green); transition: width 1s linear;"></div>
    </div>

    <div class="grid-plataforma">
        <?php if(empty($vuelos_en_pantalla)): ?>
            <div class="empty-state">
                <div style="font-size: 5em; margin-bottom: 10px;">✈️</div>
                <h3 style="margin:0;">Sin operaciones activas</h3>
                <p>No hay aeronaves reportadas en este momento.</p>
            </div>
        <?php else: ?>
            <?php foreach($vuelos_en_pantalla as $info): ?>
                <div class="card-posicion <?= $info['highlight'] ? 'active-border' : '' ?>">
                    <?php 
                        $bg_class = 'bg-incoming';
                        if($info['status'] == 'EN POSICIÓN') $bg_class = 'bg-occupied';
                        if($info['status'] == 'DESPEGADO') $bg_class = 'bg-finalizado';
                    ?>
                    <div class="side-pos <?= $bg_class ?>">
                        <span class="num"><?= $info['pos_nombre'] ?></span>
                        <span class="type"><?= $info['pos_tipo'] ?></span>
                    </div>

                    <div class="main-info">
                        <div class="top-row">
                            <span class="airline"><?= htmlspecialchars($info['aerolinea']) ?></span>
                            <span class="badge-status <?= $info['clase_st'] ?>">
                                <?= $info['status'] ?>
                            </span>
                        </div>
                        
                        <div class="vuelos-row">
                            <div class="v-box">
                                <small>LLEGADA (<?= $info['prog_lleg'] ?>)</small>
                                <strong><?= $info['vuelo_ent'] ?></strong>
                            </div>
                            <div class="v-divider"></div>
                            <div class="v-box">
                                <small>SALIDA (<?= $info['prog_sal'] ?>)</small>
                                <strong style="color:#55efc4;"><?= $info['vuelo_sal'] ?></strong>
                            </div>
                        </div>
                        
                        <div class="footer-row">
                            <i class="far fa-clock"></i> <span><?= $info['tiempo_plat'] ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    
    let timeLeft = 30; 
    const bar = document.getElementById('progress-bar');
    const reloj = document.getElementById('reloj-torre');
    
    setInterval(() => {
        timeLeft--;
        if (bar) bar.style.width = (timeLeft / 30 * 100) + "%";
        
        
        const now = new Date();
        reloj.innerText = now.toLocaleTimeString('es-MX', {hour12: false});

        if (timeLeft <= 0) {
            window.location.reload();
        }
    }, 1000);
</script>