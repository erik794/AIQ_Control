<?php
include "conexion.php";
include "sidebar.php";
$posiciones = $conexion->query("SELECT id, nombre FROM posiciones ORDER BY nombre ASC");
?>

<div class="contenido container-centro">
    <div class="form-wrapper">
        <div style="text-align: center; margin-bottom: 25px;">
            <h2 style="color:#2c3e50; margin: 0; font-size: 1.8em; font-weight: 800;">Registro de Operación Manual</h2>
            <p style="color: #7f8c8d; font-size: 0.9em;">Carga, Comercial y Operaciones Especiales</p>
        </div>
        
        <div class="card-formulario">
            <form method="POST" action="procesar_alterno.php">
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; color: #34495e;">Tipo de Operación:</label>
                    <input type="text" name="flight_category" class="input-form" placeholder="Ej. CARGA, AMBULANCIA, MILITAR..." required oninput="this.value = this.value.toUpperCase()">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; color: #34495e;">Aerolínea / Cliente:</label>
                    <input type="text" name="airline_name" class="input-form" required placeholder="Ej. FEDEX, DHL, EJÉRCITO" oninput="this.value = this.value.toUpperCase()">
                </div>

                <div class="vuelo-flex-container">
                    <div class="seccion-vuelo llegada">
                        <label>⬇️ Entrada (Llegada):</label>
                        <input type="text" name="vuelo_llegada" class="input-form" required placeholder="N° Vuelo">
                        <div class="time-inputs">
                            <input type="number" name="h_llegada" placeholder="HH" min="0" max="23" required class="input-form">
                            <span>:</span>
                            <input type="number" name="m_llegada" placeholder="MM" min="0" max="59" required class="input-form">
                        </div>
                    </div>

                    <div class="seccion-vuelo salida">
                        <label>⬆️ Salida (Opcional):</label>
                        <input type="text" name="vuelo_salida" class="input-form" placeholder="N° Vuelo">
                        <div class="time-inputs">
                            <input type="number" name="h_salida" placeholder="HH" min="0" max="23" class="input-form">
                            <span>:</span>
                            <input type="number" name="m_salida" placeholder="MM" min="0" max="59" class="input-form">
                        </div>
                    </div>
                </div>

                <div style="margin: 20px 0;">
                    <label style="font-weight: bold; color: #34495e;">Posición en Plataforma:</label>
                    <select name="posicion_id" class="input-form select-pos" required>
                        <option value="">Seleccionar Posición</option>
                        <?php while($p = $posiciones->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="pernocta-box">
                    <label style="cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; margin: 0;">
                        <input type="checkbox" name="es_pernocta" value="1" style="transform: scale(1.3);"> 
                        <span>¿La aeronave pernocta en plataforma? 🌙</span>
                    </label>
                </div>

                <button type="submit" class="btn-alterno">REGISTRAR OPERACIÓN</button>
            </form>
        </div>
    </div>
</div>

<style>
    .container-centro { display: flex; justify-content: center; align-items: center; min-height: 85vh; padding: 20px; margin-left: 260px; }
    .form-wrapper { width: 100%; max-width: 650px; }
    .card-formulario { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 15px 45px rgba(0,0,0,0.1); border-top: 6px solid #2c3e50; }
    .input-form { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #dcdde1; margin-top: 5px; box-sizing: border-box; font-size: 1em; transition: 0.3s; }
    .input-form:focus { border-color: #3498db; outline: none; box-shadow: 0 0 8px rgba(52,152,219,0.2); }
    
    .vuelo-flex-container { display: flex; gap: 15px; margin-bottom: 20px; }
    .seccion-vuelo { flex: 1; padding: 18px; border-radius: 12px; transition: 0.3s; }
    .seccion-vuelo.llegada { background: #f0f7ff; border: 1px solid #c3dafe; }
    .seccion-vuelo.salida { background: #f0fff4; border: 1px solid #c6f6d5; }
    .seccion-vuelo label { font-weight: 800; font-size: 0.8em; display: block; margin-bottom: 8px; color: #2d3748; text-transform: uppercase; }
    
    .time-inputs { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
    .time-inputs input { text-align: center; font-weight: bold; }
    
    .pernocta-box { margin-bottom: 25px; padding: 15px; background: #fff5f5; border: 2px dashed #feb2b2; border-radius: 12px; color: #c53030; font-weight: bold; }
    .btn-alterno { background: #2c3e50; color: white; border: none; padding: 20px; width: 100%; border-radius: 12px; font-weight: 800; font-size: 1.1em; cursor: pointer; transition: 0.3s; letter-spacing: 1px; }
    .btn-alterno:hover { background: #1a252f; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }

    @media (max-width: 600px) {
        .vuelo-flex-container { flex-direction: column; }
        .container-centro { margin-left: 0; }
    }
</style>