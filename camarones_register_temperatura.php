<?php
require_once './pdo_conexion.php';  

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Camarones Register Temperatura</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/default_image.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap 5.3.2 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- DataTables 1.13.7 / Responsive 2.5.0 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<!-- DataTables Buttons 2.4.1 -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="./camarones.css">

<!-- Custom Modal Styles -->
<style>
/* Modern Modal Styling */
.modern-modal .modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}
/* Temperature Chart Full Width Styling */
.chart-container {
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden;
}

.chart-container canvas {
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
}

.modern-modal .modal-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0;
    padding: 1.5rem;
}

.modern-modal .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modern-modal .btn-close {
    background: none;
    border: none;
    color: white;
    opacity: 0.8;
    font-size: 1.2rem;
}

.modern-modal .btn-close:hover {
    opacity: 1;
}

/* Form Field Styling */
.form-field {
    margin-bottom: 1.5rem;
}

.form-field label {
    display: block;
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    text-align: left;
}

.form-field .field-container {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-field .field-container:hover {
    border-color: #ced4da;
}

.form-field .field-container:focus-within {
    background: #fff;
    border-color: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    transform: translateY(-2px);
}

.form-field .field-icon {
    color: #28a745;
    margin-right: 0.75rem;
    font-size: 1.1rem;
    min-width: 20px;
}

.form-field input,
.form-field select {
    border: none;
    background: transparent;
    flex: 1;
    padding: 0.25rem 0;
    font-size: 1rem;
    color: #2c3e50;
}

.form-field input:focus,
.form-field select:focus {
    outline: none;
    box-shadow: none;
}

.form-field input::placeholder {
    color: #6c757d;
}

/* Modal Footer Styling */
.modern-modal .modal-footer {
    border: none;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 0 0 15px 15px;
}

.modern-modal .btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    border: none;
    transition: all 0.3s ease;
}

.modern-modal .btn-secondary {
    background: #6c757d;
    color: white;
}

.modern-modal .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.modern-modal .btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.modern-modal .btn-success:hover {
    background: linear-gradient(135deg, #218838, #1ea085);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

/* Date and Time Input Styling */
.form-field input[type="date"],
.form-field input[type="time"] {
    cursor: pointer;
}

.form-field input[type="date"]::-webkit-calendar-picker-indicator,
.form-field input[type="time"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    color: #28a745;
}

/* Select Styling */
.form-field select {
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2328a745' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}

/* Animation for modal entrance */
.modern-modal.fade .modal-dialog {
    transform: translateY(-50px);
    transition: transform 0.3s ease-out;
}

.modern-modal.show .modal-dialog {
    transform: translateY(0);
}

/* Reference Table Styling */
.reference-table-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.reference-table-card .card-header {
    background: linear-gradient(135deg, #17a2b8, #138496);
    border: none;
    padding: 1rem 1.5rem;
}

.reference-table-card .table {
    margin-bottom: 0;
}

.reference-table-card .table tbody tr {
    transition: all 0.2s ease;
}

.reference-table-card .table tbody tr:hover {
    background-color: rgba(23, 162, 184, 0.05);
    transform: scale(1.01);
}

.reference-table-card .badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
}

.reference-table-card .card-footer {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: none;
    padding: 0.75rem 1.5rem;
}
</style>

<!-- JS -->
<!-- jQuery 3.7.0 -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<!-- Bootstrap 5.3.2 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables 1.13.7 / Responsive 2.5.0 -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<!-- DataTables Buttons 2.4.1 -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

</head>
<body>
<!-- Icon Navigation Buttons -->

<div class="container nav-icons-container">
    <div class="icon-button-container">
        <button onclick="window.location.href='../inicio.php'" class="icon-button">
            <img src="./images/default_image.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INICIO</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./inventario_camarones.php'" class="icon-button">
            <img src="./images/robot-de-chat.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">VETERINARIO</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./camarones_indices.php'" class="icon-button">
            <img src="./images/indices.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INDICES</span>
    </div>

    
    <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion.php'" class="icon-button">
                <img src="./images/configuracion.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">CONFIG</span>
        </div>

</div>

<!-- Add back button before the header container -->
<a href="./camarones_registros.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="container text-center">
  <h3  class="container mt-4 text-white" class="collapse" id="section-historial-produccion-camarones">
  REGISTROS DE TEMPERATURA
  </h3>

  <!-- Temperature Reference Table -->
  <div class="container mt-4 mb-4">
    <div class="card shadow-sm reference-table-card">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0 text-center">
          <i class="fas fa-chart-bar me-2"></i>📊 Fases típicas del cultivo de Litopenaeus vannamei
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th class="text-center" style="width: 20%;">Valor en phase</th>
                <th class="text-center" style="width: 50%;">Descripción</th>
                <th class="text-center" style="width: 30%;">Rango óptimo de temperatura</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center fw-bold text-primary">
                  <span class="badge bg-primary">PL</span>
                </td>
                <td class="text-center">Postlarvas (PL10–PL30)</td>
                <td class="text-center">
                  <span class="badge bg-danger fs-6">29–32 °C</span>
                </td>
              </tr>
              <tr>
                <td class="text-center fw-bold text-success">
                  <span class="badge bg-success">juvenile</span>
                </td>
                <td class="text-center">Juveniles (1–5 g)</td>
                <td class="text-center">
                  <span class="badge bg-warning text-dark fs-6">28–31 °C</span>
                </td>
              </tr>
              <tr>
                <td class="text-center fw-bold text-info">
                  <span class="badge bg-info">growout</span>
                </td>
                <td class="text-center">Engorde (>5 g)</td>
                <td class="text-center">
                  <span class="badge bg-success fs-6">27–30 °C</span>
                </td>
              </tr>
              <tr>
                <td class="text-center fw-bold text-warning">
                  <span class="badge bg-warning text-dark">preharvest</span>
                </td>
                <td class="text-center">Pre-cosecha</td>
                <td class="text-center">
                  <span class="badge bg-info fs-6">26–29 °C</span>
                </td>
              </tr>
              <tr>
                <td class="text-center fw-bold text-secondary">
                  <span class="badge bg-secondary">nursery</span>
                </td>
                <td class="text-center">Fase intermedia (si aplica)</td>
                <td class="text-center">
                  <span class="badge bg-danger fs-6">28–32 °C</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer bg-light text-muted text-center small">
        <i class="fas fa-info-circle me-1"></i>
        Rangos de temperatura óptimos para cada fase del cultivo de camarón blanco del Pacífico
      </div>
    </div>
  </div>
   
  <!-- New Water Temperature Entry Modal -->
  <div class="modal fade modern-modal" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Temperatura del Agua
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newTemperaturaForm">
                    <input type="hidden" id="new_id" name="id" value="">
                    <div class="form-field">
                        <label for="new_fecha">Fecha de Registro</label>
                        <div class="field-container">
                            <i class="fas fa-calendar field-icon"></i>
                            <input type="date" id="new_fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="new_hora">Hora de Registro</label>
                        <div class="field-container">
                            <i class="fas fa-clock field-icon"></i>
                            <input type="time" id="new_hora" name="hora" value="<?php echo date('H:i'); ?>" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="new_pond_id">Estanque ID</label>
                        <div class="field-container">
                            <i class="fas fa-swimming-pool field-icon"></i>
                            <input type="text" id="new_pond_id" name="pond_id" placeholder="Ingrese el ID del estanque" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="new_temperature_celsius">Temperatura (°C)</label>
                        <div class="field-container">
                            <i class="fas fa-thermometer-half field-icon"></i>
                            <input type="number" step="0.01" id="new_temperature_celsius" name="temperature_celsius" placeholder="Ej: 28.5" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="new_source">Fuente de Medición</label>
                        <div class="field-container">
                            <i class="fas fa-tools field-icon"></i>
                            <select id="new_source" name="source" required>
                                <option value="">Seleccionar fuente</option>
                                <option value="sensor">Sensor Automático</option>
                                <option value="manual">Medición Manual</option>
                                <option value="termometro">Termómetro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="new_phase">Fase del Cultivo</label>
                        <div class="field-container">
                            <i class="fas fa-layer-group field-icon"></i>
                            <select id="new_phase" name="phase" required>
                                <option value="">Seleccionar fase</option>
                                <option value="pre-cria">Pre-cría</option>
                                <option value="crecimiento">Crecimiento</option>
                                <option value="engorde">Engorde</option>
                                <option value="cosecha">Cosecha</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewTemperatura">
                    <i class="fas fa-save me-1"></i>Guardar Registro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add New Record Button -->
<div class="container mt-4 text-center">
    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#newEntryModal">
        <i class="fas fa-plus me-2"></i>Nuevo Registro de Temperatura
    </button>
</div>

<!-- DataTable for water_temperature_log records -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0 text-center"><i class="fas fa-table me-2"></i>Registros de Temperatura del Agua</h5>
        </div>

        <div class="card-body">
            <table id="temperaturaTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Estanque ID</th>
                        <th class="text-center">Estanque Nombre</th>
                        <th class="text-center">Temperatura (°C)</th>
                        <th class="text-center">Fuente</th>
                        <th class="text-center">Fase</th>
                        <th class="text-center">Fecha/Hora</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Edit Water Temperature -->
<div class="modal fade modern-modal" id="editTemperaturaModal" tabindex="-1" aria-labelledby="editTemperaturaModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTemperaturaModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Temperatura del Agua
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTemperaturaForm">
                    <input type="hidden" id="edit_id" name="id" value="">
                    <div class="form-field">
                        <label for="edit_pond_id">Estanque ID</label>
                        <div class="field-container">
                            <i class="fas fa-swimming-pool field-icon"></i>
                            <input type="text" id="edit_pond_id" name="pond_id" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="edit_estanque_nombre">Nombre del Estanque</label>
                        <div class="field-container">
                            <i class="fas fa-tag field-icon"></i>
                            <input type="text" id="edit_estanque_nombre" name="estanque_nombre" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="edit_fecha">Fecha de Registro</label>
                        <div class="field-container">
                            <i class="fas fa-calendar field-icon"></i>
                            <input type="date" id="edit_fecha" name="fecha" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="edit_hora">Hora de Registro</label>
                        <div class="field-container">
                            <i class="fas fa-clock field-icon"></i>
                            <input type="time" id="edit_hora" name="hora" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="edit_temperature_celsius">Temperatura (°C)</label>
                        <div class="field-container">
                            <i class="fas fa-thermometer-half field-icon"></i>
                            <input type="number" step="0.01" id="edit_temperature_celsius" name="temperature_celsius" placeholder="Ej: 28.5" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="edit_source">Fuente de Medición</label>
                        <div class="field-container">
                            <i class="fas fa-tools field-icon"></i>
                            <select id="edit_source" name="source" required>
                                <option value="">Seleccionar fuente</option>
                                <option value="sensor">Sensor Automático</option>
                                <option value="manual">Medición Manual</option>
                                <option value="termometro">Termómetro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="edit_phase">Fase del Cultivo</label>
                        <div class="field-container">
                            <i class="fas fa-layer-group field-icon"></i>
                            <select id="edit_phase" name="phase" required>
                                <option value="">Seleccionar fase</option>
                                <option value="pre-cria">Pre-cría</option>
                                <option value="crecimiento">Crecimiento</option>
                                <option value="engorde">Engorde</option>
                                <option value="cosecha">Cosecha</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveEditTemperatura">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Water Temperature CRUD operations -->
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#temperaturaTable').DataTable({
        ajax: {
            url: 'get_water_temperature_data.php',
            type: 'GET',
            dataSrc: function(json) {
                if (json.error) {
                    console.error('Error loading data:', json.error);
                    return [];
                }
                return json;
            }
        },
        columns: [
            { data: 'id', className: 'text-center' },
            { data: 'pond_id', className: 'text-center' },
            { data: 'estanque_nombre', className: 'text-center' },
            { 
                data: 'temperature_celsius', 
                className: 'text-center',
                render: function(data) {
                    return parseFloat(data).toFixed(2) + '°C';
                }
            },
            { 
                data: 'source', 
                className: 'text-center',
                render: function(data) {
                    const sourceLabels = {
                        'sensor': 'Sensor Automático',
                        'manual': 'Medición Manual',
                        'termometro': 'Termómetro'
                    };
                    return sourceLabels[data] || data;
                }
            },
            { 
                data: 'phase', 
                className: 'text-center',
                render: function(data) {
                    const phaseLabels = {
                        'pre-cria': 'Pre-cría',
                        'crecimiento': 'Crecimiento',
                        'engorde': 'Engorde',
                        'cosecha': 'Cosecha'
                    };
                    return phaseLabels[data] || data;
                }
            },
            { 
                data: null, 
                className: 'text-center',
                render: function(data, type, row) {
                    if (row.fecha && row.hora) {
                        // Parse date string manually to avoid timezone issues
                        // Database date format: YYYY-MM-DD
                        const dateParts = row.fecha.split('-');
                        if (dateParts.length === 3) {
                            const year = parseInt(dateParts[0]);
                            const month = parseInt(dateParts[1]) - 1; // Month is 0-indexed in Date constructor
                            const day = parseInt(dateParts[2]);
                            const fecha = new Date(year, month, day);
                            const fechaFormatted = fecha.toLocaleDateString('es-ES');
                            
                            // Format time as HH:MM (remove seconds if present)
                            let horaFormatted = row.hora;
                            if (horaFormatted && horaFormatted.length > 5) {
                                horaFormatted = horaFormatted.substring(0, 5);
                            }
                            
                            return `${fechaFormatted}<br><small class="text-muted">${horaFormatted}</small>`;
                        }
                    }
                    return '';
                }
            },
            {
                data: null,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning edit-temperatura me-1" 
                                data-id="${row.id}" 
                                data-pond_id="${row.pond_id}" 
                                data-temperature_celsius="${row.temperature_celsius}" 
                                data-source="${row.source}"
                                data-phase="${row.phase}"
                                data-fecha="${row.fecha}"
                                data-hora="${row.hora}"
                                data-estanque_nombre="${row.estanque_nombre}"
                                title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-temperatura" 
                                data-id="${row.id}" 
                                data-pond_id="${row.pond_id}"
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[1, 'asc']], // Order by fecha ascending
        pageLength: 25
    });

    // Handle new entry form submission
    $('#saveNewTemperatura').click(function() {
        // Validate the form
        var form = document.getElementById('newTemperaturaForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            pond_id: $('#new_pond_id').val(),
            temperature_celsius: $('#new_temperature_celsius').val(),
            source: $('#new_source').val(),
            phase: $('#new_phase').val(),
            fecha: $('#new_fecha').val(),
            hora: $('#new_hora').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar la temperatura ${formData.temperature_celsius}°C para el estanque ${formData.pond_id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Por favor espere mientras se procesa la información',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to insert the record
                $.ajax({
                    url: 'process_water_temperature.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'insert',
                        pond_id: formData.pond_id,
                        temperature_celsius: formData.temperature_celsius,
                        source: formData.source,
                        phase: formData.phase,
                        fecha: formData.fecha,
                        hora: formData.hora
                    },
                    success: function(result) {
                        if (result.success) {
                        // Close the modal
                            $('#newEntryModal').modal('hide');
                            
                            // Clear form
                            $('#newTemperaturaForm')[0].reset();
                            // Reset to current date and time
                            $('#new_fecha').val('<?php echo date('Y-m-d'); ?>');
                            $('#new_hora').val('<?php echo date('H:i'); ?>');
                            
                            // Reload table
                            table.ajax.reload();
                            
                            // Trigger statistics update
                            $(document).trigger('tableUpdated');
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                                text: result.message,
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: result.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMsg = response.message;
                                }
                            } catch (e) {
                                // Use default error message
                                console.error('Error parsing server response:', e);
                            }
                        }
                        
                        Swal.fire({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });

    // Handle edit button click
    $(document).on('click', '.edit-temperatura', function() {
        var id = $(this).data('id');
        var pond_id = $(this).data('pond_id');
        var temperature_celsius = $(this).data('temperature_celsius');
        var source = $(this).data('source');
        var phase = $(this).data('phase');
        var fecha = $(this).data('fecha');
        var hora = $(this).data('hora');
        var estanque_nombre = $(this).data('estanque_nombre');

        // Debug logging
        console.log('Edit modal data:', {
            id: id,
            pond_id: pond_id,
            temperature_celsius: temperature_celsius,
            source: source,
            phase: phase,
            fecha: fecha,
            hora: hora,
            estanque_nombre: estanque_nombre
        });

        // Populate edit modal - all available columns
        $('#edit_id').val(id);
        $('#edit_pond_id').val(pond_id);
        $('#edit_temperature_celsius').val(temperature_celsius);
        $('#edit_source').val(source);
        $('#edit_phase').val(phase);
        $('#edit_fecha').val(fecha);
        $('#edit_hora').val(hora);
        $('#edit_estanque_nombre').val(estanque_nombre || 'Sin nombre');
        
        // Format hora to HH:MM for HTML time input (remove seconds if present)
        let formattedHora = hora;
        if (hora && hora.length > 5) {
            formattedHora = hora.substring(0, 5); // Take only HH:MM part
        }
        $('#edit_hora').val(formattedHora);
        
        // Show edit modal
        $('#editTemperaturaModal').modal('show');
    });

    // Handle edit form submission
        $('#saveEditTemperatura').click(function() {
        // Validate the form
        var form = document.getElementById('editTemperaturaForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
            var formData = {
                id: $('#edit_id').val(),
            temperature_celsius: $('#edit_temperature_celsius').val(),
            source: $('#edit_source').val(),
            phase: $('#edit_phase').val(),
            fecha: $('#edit_fecha').val(),
            hora: $('#edit_hora').val()
        };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
            text: `¿Desea actualizar el registro de temperatura?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Por favor espere mientras se procesa la información',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send AJAX request to update the record
                    $.ajax({
                        url: 'process_water_temperature.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'update',
                            id: formData.id,
                            temperature_celsius: formData.temperature_celsius,
                            source: formData.source,
                            phase: formData.phase,
                            fecha: formData.fecha,
                            hora: formData.hora
                        },
                        success: function(result) {
                        if (result.success) {
                            // Close the modal
                            $('#editTemperaturaModal').modal('hide');
                            
                            // Reload table
                            table.ajax.reload();
                            
                            // Trigger statistics update
                            $(document).trigger('tableUpdated');
                            
                            // Show success message
                            Swal.fire({
                                title: '¡Actualización exitosa!',
                                text: result.message,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: result.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                        },
                        error: function(xhr, status, error) {
                            // Show error message
                            let errorMsg = 'Error al procesar la solicitud';
                            
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMsg = response.message;
                                }
                            } catch (e) {
                                // Use default error message
                            }
                            
                            Swal.fire({
                                title: 'Error',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
        });
    });
    
    // Handle delete button click
    $(document).on('click', '.delete-temperatura', function() {
        var id = $(this).data('id');
        var pond_id = $(this).data('pond_id');
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `¿Está seguro de que desea eliminar el registro para el estanque ${pond_id}? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espere mientras se procesa la solicitud',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to delete the record
                $.ajax({
                    url: 'process_water_temperature.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(result) {
                        if (result.success) {
                            // Reload table
                            table.ajax.reload();
                            
                            // Trigger statistics update
                            $(document).trigger('tableUpdated');
                            
                        // Show success message
                        Swal.fire({
                            title: '¡Eliminado!',
                                text: result.message,
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: result.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMsg = response.message;
                                }
                            } catch (e) {
                                // Use default error message
                                console.error('Error parsing server response:', e);
                            }
                        }
                        
                        Swal.fire({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
});
</script>

<!-- Water Temperature Statistics -->
<div class="container mt-5 mb-5">
    <div class="row">
                <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-thermometer-half me-2"></i>Temperatura Promedio</h6>
                </div>
                <div class="card-body text-center">
                    <h4 id="avgTemperature">--°C</h4>
                    </div>
                    </div>
                    </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Temperatura Máxima</h6>
                </div>
                <div class="card-body text-center">
                    <h4 id="maxTemperature">--°C</h4>
                    </div>
                    </div>
                     </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Temperatura Mínima</h6>
                </div>
                <div class="card-body text-center">
                    <h4 id="minTemperature">--°C</h4>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Script -->
<script>
$(document).ready(function() {
    // Load temperature statistics
    function loadStatistics() {
    $.ajax({
            url: 'get_water_temperature_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
                if (data.error || !Array.isArray(data) || data.length === 0) {
                    $('#avgTemperature').text('--°C');
                    $('#maxTemperature').text('--°C');
                    $('#minTemperature').text('--°C');
                return;
            }

                // Calculate statistics
                const temperatures = data.map(item => parseFloat(item.temperature_celsius));
                const avgTemp = temperatures.reduce((a, b) => a + b, 0) / temperatures.length;
                const maxTemp = Math.max(...temperatures);
                const minTemp = Math.min(...temperatures);
                
                // Update display
                $('#avgTemperature').text(avgTemp.toFixed(1) + '°C');
                $('#maxTemperature').text(maxTemp.toFixed(1) + '°C');
                $('#minTemperature').text(minTemp.toFixed(1) + '°C');
        },
        error: function(xhr, status, error) {
                console.error('Error loading statistics:', error);
                $('#avgTemperature').text('Error');
                $('#maxTemperature').text('Error');
                $('#minTemperature').text('Error');
            }
        });
    }
    
    // Load statistics on page load
    loadStatistics();
    
    // Reload statistics when table is updated
    $(document).on('tableUpdated', function() {
        loadStatistics();
    });
});
</script>

<!-- Monthly Temperature Trends Chart -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-center">
                <i class="fas fa-chart-line me-2"></i>Tendencias de Temperatura Mensual por Estanque
            </h5>
        </div>
        <div class="card-body">
            <!-- Filter Controls -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="pond-filter" class="form-label">
                        <i class="fas fa-filter me-2"></i>Filtrar por Estanque:
                    </label>
                    <select id="pond-filter" class="form-select">
                        <option value="all">Todos los Estanques</option>
                    </select>
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <button id="refresh-chart" class="btn btn-outline-primary">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar Gráfico
                    </button>
                </div>
            </div>
            
            <!-- Chart Container -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-container" style="position: relative; height: 500px; width: 100%;">
                        <canvas id="temperatureChart" style="width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Chart Info -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Este gráfico muestra las temperaturas promedio mensuales para cada estanque. 
                        Use el filtro para ver datos específicos de un estanque.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Temperature Chart Variables
let temperatureChart = null;
let temperatureChartData = {};

// Initialize Temperature Chart
function initTemperatureChart() {
    const ctx = document.getElementById('temperatureChart').getContext('2d');
    
    temperatureChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 10,
                    bottom: 10
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Temperatura Promedio Mensual por Estanque',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#fff',
                    borderWidth: 1,
                    callbacks: {
                        title: function(tooltipItems) {
                            return 'Mes: ' + tooltipItems[0].label;
                        },
                        label: function(context) {
                            const datasetLabel = context.dataset.label;
                            const value = context.parsed.y;
                            if (value !== null) {
                                return datasetLabel + ': ' + value.toFixed(2) + '°C';
                            }
                            return datasetLabel + ': Sin datos';
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Mes',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Temperatura (°C)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    beginAtZero: false,
                    min: function(context) {
                        // Set minimum based on data, but ensure reasonable range
                        const data = context.chart.data.datasets.flatMap(d => d.data.filter(v => v !== null));
                        if (data.length > 0) {
                            const min = Math.min(...data);
                            return Math.max(0, min - 2); // At least 2 degrees below minimum
                        }
                        return 20; // Default minimum
                    },
                    max: function(context) {
                        // Set maximum based on data
                        const data = context.chart.data.datasets.flatMap(d => d.data.filter(v => v !== null));
                        if (data.length > 0) {
                            const max = Math.max(...data);
                            return max + 2; // At least 2 degrees above maximum
                        }
                        return 35; // Default maximum
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                },
                line: {
                    borderWidth: 2,
                    tension: 0.4
                }
            }
        }
    });
    
    // Force resize after chart creation to ensure proper sizing
    setTimeout(() => {
        temperatureChart.resize();
    }, 100);
}

// Load Temperature Chart Data
function loadTemperatureData(tagidFilter = 'all') {
    const url = `get_monthly_temperature_data.php${tagidFilter !== 'all' ? '?tagid=' + encodeURIComponent(tagidFilter) : ''}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store data for reference
                temperatureChartData = data;
                
                // Update chart
                temperatureChart.data.labels = data.labels;
                temperatureChart.data.datasets = data.datasets;
                temperatureChart.update();
                
                // Force resize after data update
                setTimeout(() => {
                    temperatureChart.resize();
                }, 50);
                
                // Update pond filter dropdown if needed
                updatePondFilter(data.ponds);
                
                console.log('Temperature chart updated successfully');
            } else {
                console.error('Error loading temperature data:', data.message);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar datos de temperatura: ' + data.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error fetching temperature data:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al obtener datos de temperatura',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        });
}

// Update Pond Filter Dropdown
function updatePondFilter(ponds) {
    const select = document.getElementById('pond-filter');
    const currentValue = select.value;
    
    // Clear existing options except "All"
    select.innerHTML = '<option value="all">Todos los Estanques</option>';
    
    // Add pond options
    ponds.forEach(pond => {
        const option = document.createElement('option');
        option.value = pond;
        option.textContent = `Estanque ${pond}`;
        select.appendChild(option);
    });
    
    // Restore previous selection if still valid
    if (ponds.includes(currentValue) || currentValue === 'all') {
        select.value = currentValue;
    }
}

// Initialize temperature chart when page loads
$(document).ready(function() {
    // Initialize chart
    initTemperatureChart();
    
    // Load initial data
    loadTemperatureData();
    
    // Handle window resize to ensure chart remains responsive
    $(window).resize(function() {
        if (temperatureChart) {
            temperatureChart.resize();
        }
    });
    
    // Handle filter change
    $('#pond-filter').on('change', function() {
        const selectedPond = $(this).val();
        loadTemperatureData(selectedPond);
    });
    
    // Handle refresh button
    $('#refresh-chart').on('click', function() {
        const selectedPond = $('#pond-filter').val();
        loadTemperatureData(selectedPond);
    });
    
    // Reload chart when table is updated
    $(document).on('tableUpdated', function() {
        const selectedPond = $('#pond-filter').val();
        loadTemperatureData(selectedPond);
    });
});
</script>

</body>
</html>