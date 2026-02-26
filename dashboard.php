<?php include "conexion.php"; ?>
<?php include "sidebar.php"; ?>

<div class="contenido">
    <h2>Torre de Control</h2>

    <?php
    $res = $conexion->query("
        SELECT p.id, p.nombre, p.ocupada, v.flight_number
        FROM posiciones p
        LEFT JOIN vuelos v ON v.posicion_id = p.id
    ");

    while($r = $res->fetch_assoc()){
        echo "<div class='pos ".($r['ocupada']?'ocupada':'libre')."'>
            <b>{$r['nombre']}</b><br>
            ".($r['flight_number'] ?? 'LIBRE')."
        </div>";
    }
    ?>
</div>
