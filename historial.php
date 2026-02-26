<?php include "conexion.php"; ?>
<?php include "sidebar.php"; ?>

<div class="contenido">
<h2>Historial de vuelos</h2>

<form>
    <input type="date" name="fecha">
    <button>Buscar</button>
</form>

<?php
if(isset($_GET['fecha'])){
    $fecha = $_GET['fecha'];

    $res = $conexion->query("
    SELECT v.flight_number, p.nombre, v.created_at
    FROM vuelos v
    JOIN posiciones p ON p.id = v.posicion_id
    WHERE DATE(v.created_at)='$fecha'
    ");

    while($r = $res->fetch_assoc()){
        echo "{$r['flight_number']} - {$r['nombre']} - {$r['created_at']}<br>";
    }
}
?>
<button onclick="window.print()">Imprimir</button>
</div>
