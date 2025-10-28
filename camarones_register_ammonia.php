<?php
require_once './pdo_conexion.php';  

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch sensor options from cac_sensores table
$sensorOptions = [];
try {
    $sql = "SELECT id, sensores FROM cac_sensores ORDER BY sensores ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $sensorOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching sensor options: " . $e->getMessage());
    $sensorOptions = [];
}

// Fetch phase options from cac_etapas table
$phaseOptions = [];
try {
    $sql = "SELECT id, cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $phaseOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching phase options: " . $e->getMessage());
    $phaseOptions = [];
}

// Fetch product options from cac_ammonia table
$productOptions = [];
try {
    $sql = "SELECT id, cac_ammonia_vacuna, cac_ammonia_dosis, cac_ammonia_costo FROM cac_ammonia ORDER BY cac_ammonia_vacuna ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $productOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching product options: " . $e->getMessage());
    $productOptions = [];
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Camarones Register Oxigeno</title>
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

.modern-modal .modal-header {
    background: linear-gradient(135deg,rgb(55, 175, 59),rgb(42, 148, 0));
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
    color:rgb(26, 106, 4);
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
    border-color:rgb(77, 193, 34);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    transform: translateY(-2px);
}

.form-field .field-icon {
    color:rgb(87, 184, 61);
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
    color:rgb(50, 80, 44);
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
    background: linear-gradient(135deg,rgb(94, 191, 81),rgb(31, 124, 10));
    color: white;
}

.modern-modal .btn-success:hover {
    background: linear-gradient(135deg,rgb(94, 191, 81),rgb(47, 144, 30));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
}

/* Date and Time Input Styling */
.form-field input[type="date"],
.form-field input[type="time"] {
    cursor: pointer;
}

.form-field input[type="date"]::-webkit-calendar-picker-indicator,
  .form-field input[type="time"]::-webkit-calendar-picker-indicator {
      cursor: pointer;
      color:rgb(29, 163, 38);
  }

  /* Number input spinner styling */
  .form-field input[type="number"] {
      -moz-appearance: textfield; /* Firefox */
      appearance: textfield; /* Standard property */
  }

  .form-field input[type="number"]::-webkit-outer-spin-button,
  .form-field input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: auto;
      appearance: auto; /* Standard property */
      cursor: pointer;
      height: 100%;
  }

  /* Enhanced spinner button styling */
  .form-field input[type="number"]::-webkit-inner-spin-button {
      opacity: 1;
      height: 40px;
      width: 20px;
  }

/* Select Styling */
.form-field select {
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23007bff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
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
    background: linear-gradient(135deg,rgb(116, 188, 94),rgb(72, 133, 7));
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
    background-color: rgba(111, 66, 193, 0.05);
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
    REGISTROS DE AMONIACO
    </h3>



    <!-- New Water Ammonia Entry Modal -->
    <div class="modal fade modern-modal" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Amoniaco
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newAmmoniaForm">
                    <input type="hidden" id="new_id" name="id" value="">
                    <!-- Two-column responsive layout -->
                    <div class="row">
                        <div class="form-field">
                            <label for="new_pond_id">Estanque ID</label>
                            <div class="field-container">
                                <i class="fas fa-swimming-pool field-icon"></i>
                                <input type="number" id="new_pond_id" name="pond_id" placeholder="ID Estanque" required>
                            </div>
                        </div>
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="new_fecha">Fecha Registro</label>
                                <div class="field-container">
                                    <i class="fas fa-calendar field-icon"></i>
                                    <input type="date" id="new_fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="new_hora">Hora Registro</label>
                                <div class="field-container">
                                    <i class="fas fa-clock field-icon"></i>
                                    <input type="time" id="new_hora" name="hora" value="<?php echo date('H:i'); ?>" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="new_total_ammonia_mg_l">Nivel amoniaco (ppt)</label>
                                <div class="field-container">
                                    <i class="fas fa-water field-icon"></i>
                                    <input type="number" step="0.01" min="0" id="new_total_ammonia_mg_l" name="total_ammonia_mg_l" placeholder="Ej: 15.50" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="new_product_qty">Cantidad (Litros)</label>
                                <div class="field-container">
                                    <i class="fas fa-weight-scale field-icon"></i>
                                    <input type="number" step="0.01" min="0" id="new_product_qty" name="product_qty" placeholder="Litros" required>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="new_source">Sensor</label>
                                <div class="field-container">
                                    <i class="fas fa-tools field-icon"></i>
                                    <select id="new_source" name="source" required>
                                        <option value="">Sensores</option>
                                        <?php foreach ($sensorOptions as $sensor): ?>
                                            <option value="<?php echo htmlspecialchars($sensor['sensores']); ?>">
                                                <?php echo htmlspecialchars($sensor['sensores']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="new_phase">Fase</label>
                                <div class="field-container">
                                    <i class="fas fa-layer-group field-icon"></i>
                                    <select id="new_phase" name="phase" required>
                                        <option value="">Fases</option>
                                        <?php foreach ($phaseOptions as $phase): ?>
                                            <option value="<?php echo htmlspecialchars($phase['cac_etapas_nombre']); ?>">
                                                <?php echo htmlspecialchars($phase['cac_etapas_nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="new_product">Producto Amoniaco</label>
                                <div class="field-container">
                                    <i class="fas fa-syringe field-icon"></i>
                                    <select id="new_product" name="product" required>
                                        <option value="">Productos</option>
                                        <?php foreach ($productOptions as $product): ?>
                                            <option value="<?php echo htmlspecialchars($product['cac_ammonia_vacuna']); ?>">
                                                <?php echo htmlspecialchars($product['cac_ammonia_vacuna']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="new_product_price">Precio ($/Litro)</label>
                                <div class="field-container">
                                    <i class="fas fa-dollar-sign field-icon"></i>
                                    <input type="number" step="0.01" min="0" id="new_product_price" name="product_price" placeholder="$" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewAmmonia">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add New Record Button -->
<div class="container mt-4 text-center">
    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#newEntryModal">
        <i class="fas fa-plus me-2"></i>Nuevo Registro de Amoniaco
    </button>
  </div>
  
<!-- Simple DataTable for Water Ammonia Records -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0 text-center"><i class="fas fa-table me-2"></i>Registros de Amoniaco</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ammoniaTable" class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">ID Estanque</th>
                            <th class="text-center">Amoniaco Total (mg/L)</th>
                            <th class="text-center">NH3 (mg/L)</th>
                            <th class="text-center">Fuente</th>
                            <th class="text-center">Fecha/Hora</th>
                            <th class="text-center">Fase</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Precio ($/Litro)</th>
                            <th class="text-center">Cantidad (Litros)</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ammoniaTableBody">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Simple CSS for the table -->
<style>
.table th {
    vertical-align: middle;
    font-weight: 600;
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.85rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.pagination .page-link {
    color: #17a2b8;
}

.pagination .page-item.active .page-link {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.loading-spinner {
    text-align: center;
    padding: 2rem;
}

.no-data {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
    font-style: italic;
}

/* DataTables buttons styling */
.dt-buttons {
    margin-bottom: 1rem;
}

.dt-buttons .btn {
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

/* DataTables search box styling */
.dataTables_filter input[type="search"] {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
}

/* DataTables info and pagination styling */
.dataTables_info {
    color: #6c757d;
    font-size: 0.875rem;
}

.dataTables_paginate .paginate_button {
    border-radius: 0.375rem !important;
    margin: 0 0.125rem;
}

/* Responsive table styling */
@media (max-width: 768px) {
    .dt-buttons {
        text-align: center;
    }
    
    .dt-buttons .btn {
        margin: 0.25rem;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>

<!-- Modal for Edit Water Oxigen -->
<div class="modal fade modern-modal" id="editAmmoniaModal" tabindex="-1" aria-labelledby="editAmmoniaModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAmmoniaModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Registros Amoniaco
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAmmoniaForm">
                    <input type="hidden" id="edit_id" name="id" value="">
                    <!-- Two-column responsive layout -->
                    <div class="row">
                        <!-- Left Column -->
                        <div class="form-field">
                                <label for="edit_pond_id">Estanque ID</label>
                                <div class="field-container">
                                    <i class="fas fa-swimming-pool field-icon"></i>
                                    <input type="number" id="edit_pond_id" name="pond_id" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                </div>
                            </div>
                        <div class="col-md-6">

                            <div class="form-field">
                                <label for="edit_fecha">Fecha Registro</label>
                                <div class="field-container">
                                    <i class="fas fa-calendar field-icon"></i>
                                    <input type="date" id="edit_fecha" name="fecha" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="edit_hora">Hora Registro</label>
                                <div class="field-container">
                                    <i class="fas fa-clock field-icon"></i>
                                    <input type="time" id="edit_hora" name="hora" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="edit_ammonia_level">Nivel Amoniaco (ppt)</label>
                                <div class="field-container">
                                    <i class="fas fa-tint field-icon"></i>
                                    <input type="number" step="0.01" min="0" id="edit_ammonia_level" name="total_ammonia_mg_l" placeholder="Ej: 15.50" required>
                                </div>
                            </div>
                            <div class="form-field">
                                 <label for="edit_product_qty">Cantidad (Litros)</label>
                                 <div class="field-container">
                                     <i class="fas fa-weight-scale field-icon"></i>
                                     <input type="number" step="0.01" min="0" id="edit_product_qty" name="product_qty" placeholder="Kg">
                                 </div>
                             </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="edit_source">Sensor</label>
                                <div class="field-container">
                                    <i class="fas fa-tools field-icon"></i>
                                    <select id="edit_source" name="source" required>
                                        <option value="">Sensores</option>
                                        <?php foreach ($sensorOptions as $sensor): ?>
                                            <option value="<?php echo htmlspecialchars($sensor['sensores']); ?>">
                                                <?php echo htmlspecialchars($sensor['sensores']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="edit_phase">Fase</label>
                                <div class="field-container">
                                    <i class="fas fa-layer-group field-icon"></i>
                                    <select id="edit_phase" name="phase" required>
                                        <option value="">Fases</option>
                                        <?php foreach ($phaseOptions as $phase): ?>
                                            <option value="<?php echo htmlspecialchars($phase['cac_etapas_nombre']); ?>">
                                                <?php echo htmlspecialchars($phase['cac_etapas_nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="edit_product">Producto Amoniaco</label>
                                <div class="field-container">
                                    <i class="fas fa-syringe field-icon"></i>
                                    <select id="edit_product" name="product" required>
                                        <option value="">Productos</option>
                                        <?php foreach ($productOptions as $product): ?>
                                            <option value="<?php echo htmlspecialchars($product['cac_ammonia_vacuna']); ?>">
                                                <?php echo htmlspecialchars($product['cac_ammonia_vacuna']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="edit_product_price">Precio ($/Litro)</label>
                                <div class="field-container">
                                    <i class="fas fa-dollar-sign field-icon"></i>
                                    <input type="number" step="0.01" min="0" id="edit_product_price" name="product_price" placeholder="$">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveEditAmmonia">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Water Oxigen Statistics -->
<div class="container mt-5 mb-5">
    <div class="row">
                <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-tint me-2"></i>Amoniaco Promedio</h6>
                </div>
                <div class="card-body text-center">
                    <h4 id="avgAmmonia">-- ppt</h4>
                    </div>
                    </div>
                    </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Amoniaco Máxima</h6>
                </div>
                <div class="card-body text-center">
                    <h4 id="maxAmmonia">-- ppt</h4>
                    </div>
                    </div>
                     </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Amoniaco Mínima</h6>
                </div>
                <div class="card-body text-center">
                    <h4 id="minAmmonia">-- ppt</h4>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Script -->
<script>
$(document).ready(function() {
    // Load ammonia statistics
    function loadStatistics() {
        $.ajax({
            url: 'get_water_ammonia_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response.success || !response.records || response.records.length === 0) {
                    $('#avgAmmonia').text('-- mg/L');
                    $('#maxAmmonia').text('-- mg/L');
                    $('#minAmmonia').text('-- mg/L');
                    return;
                }

                // Calculate statistics from total_ammonia_mg_l column
                const ammoniaLevels = response.records
                    .map(item => parseFloat(item.total_ammonia_mg_l))
                    .filter(value => !isNaN(value) && value > 0); // Filter out invalid values
                
                if (ammoniaLevels.length === 0) {
                    $('#avgAmmonia').text('-- mg/L');
                    $('#maxAmmonia').text('-- mg/L');
                    $('#minAmmonia').text('-- mg/L');
                    return;
                }
                
                // Calculate statistics
                const avgAmmonia = ammoniaLevels.reduce((a, b) => a + b, 0) / ammoniaLevels.length;
                const maxAmmonia = Math.max(...ammoniaLevels);
                const minAmmonia = Math.min(...ammoniaLevels);
                
                // Update display with proper formatting
                $('#avgAmmonia').text(avgAmmonia.toFixed(2) + ' mg/L');
                $('#maxAmmonia').text(maxAmmonia.toFixed(2) + ' mg/L');
                $('#minAmmonia').text(minAmmonia.toFixed(2) + ' mg/L');
            },
            error: function(xhr, status, error) {
                console.error('Error loading statistics:', error);
                $('#avgAmmonia').text('Error');
                $('#maxAmmonia').text('Error');
                $('#minAmmonia').text('Error');
            }
        });
    }
    
    // Load statistics on page load
    loadStatistics();
    
    // Reload statistics when DataTable is updated
    $(document).on('tableUpdated', function() {
        loadStatistics();
    });
    
    // Also reload statistics when DataTable finishes loading
    if (typeof ammoniaDataTable !== 'undefined') {
        ammoniaDataTable.on('xhr.dt', function() {
            loadStatistics();
        });
    }
});
</script>
<!-- Monthly ammonia Trends Chart -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-center">
                <i class="fas fa-chart-line me-2"></i>Tendencias de amoniaco Mensual por Estanque
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
                        <canvas id="ammoniaChart" style="width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Chart Info -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Este gráfico muestra los niveles de amoniaco promedio mensuales para cada estanque.
                        Use el filtro para ver datos específicos de un estanque.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ammonia Chart Variables
let ammoniaChart = null;
let ammoniaChartData = {};

// Initialize ammonia Chart
function initammoniaChart() {
    const ctx = document.getElementById('ammoniaChart').getContext('2d');
    
    ammoniaChart = new Chart(ctx, {
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
                    text: 'Amoniaco Promedio Mensual por Estanque',
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
                                return datasetLabel + ': ' + value.toFixed(2) + 'ppt';
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
                        text: 'Amoniaco (ppt)',
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
        ammoniaChart.resize();
    }, 100);
}

// Load ammonia Chart Data
function loadammoniaData(tagidFilter = 'all') {
    const url = `get_monthly_ammonia_data.php${tagidFilter !== 'all' ? '?tagid=' + encodeURIComponent(tagidFilter) : ''}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store data for reference
                ammoniaChartData = data;
                
                // Update chart
                ammoniaChart.data.labels = data.labels;
                ammoniaChart.data.datasets = data.datasets;
                ammoniaChart.update();
                
                // Force resize after data update
                setTimeout(() => {
                    ammoniaChart.resize();
                }, 50);
                
                // Update pond filter dropdown if needed
                updatePondFilter(data.ponds);
                
                console.log('ammonia chart updated successfully');
            } else {
                console.error('Error loading ammonia data:', data.message);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar datos de amoniaco: ' + data.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error fetching ammonia data:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al obtener datos de amoniaco',
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

// Initialize ammonia chart when page loads
$(document).ready(function() {
    // Initialize chart
    initammoniaChart();
    
    // Load initial data
    loadammoniaData();
    
    // Handle window resize to ensure chart remains responsive
    $(window).resize(function() {
        if (ammoniaChart) {
            ammoniaChart.resize();
        }
    });
    
    // Handle filter change
    $('#pond-filter').on('change', function() {
        const selectedPond = $(this).val();
        loadammoniaData(selectedPond);
    });
    
    // Handle refresh button
    $('#refresh-chart').on('click', function() {
        const selectedPond = $('#pond-filter').val();
        loadammoniaData(selectedPond);
    });
    
    // Reload chart when table is updated
    $(document).on('tableUpdated', function() {
        const selectedPond = $('#pond-filter').val();
        loadammoniaData(selectedPond);
    });
});

// Enhanced DataTables implementation with full features
let ammoniaDataTable;

// Initialize DataTable on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDataTable();
    
    // Attach event listeners to save buttons
    document.getElementById('saveNewAmmonia').addEventListener('click', saveNewRecord);
    document.getElementById('saveEditAmmonia').addEventListener('click', saveEditRecord);
});

// Initialize DataTable with full features
function initializeDataTable() {
    ammoniaDataTable = $('#ammoniaTable').DataTable({
        ajax: {
            url: 'get_water_ammonia_data.php',
            type: 'GET',
            dataSrc: function(json) {
                if (json.error || !json.success) {
                    console.error('Error loading data:', json.error || json.message);
                    return [];
                }
                return json.records || [];
            }
        },
        columns: [
            { 
                data: 'pond_id', 
                title: 'ID Estanque',
                className: 'text-center'
            },
            { 
                data: 'total_ammonia_mg_l', 
                title: 'Amoniaco Total (mg/L)',
                className: 'text-center',
                render: function(data) {
                    return parseFloat(data).toFixed(3) + ' mg/L';
                }
            },
            { 
                data: 'nh3_mg_l', 
                title: 'NH3 (mg/L)',
                className: 'text-center',
                render: function(data) {
                    return parseFloat(data).toFixed(3) + ' mg/L';
                }
            },
            { 
                data: 'source', 
                title: 'Fuente',
                className: 'text-center'
            },
            {
                data: 'product', 
                title: 'Producto',
                className: 'text-center'
            },
            {
                data: 'product_price', 
                title: 'Precio ($/Litro)',
                className: 'text-center'
            },
            {
                data: 'product_qty', 
                title: 'Cantidad (Litros)',
                className: 'text-center'
            },
            { 
                data: 'timestamp', 
                title: 'Fecha/Hora',
                className: 'text-center',
                render: function(data) {
                    if (data) {
                        const date = new Date(data);
                        return date.toLocaleString('es-ES');
                    }
                    return '';
                }
            },
            { 
                data: 'phase', 
                title: 'Fase',
                className: 'text-center'
            },
            {
                data: null,
                title: 'Acciones',
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editRecord(${row.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteRecord(${row.id}, '${row.pond_id}')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        // DataTables configuration with full features
        responsive: true,
        processing: true,
        pageLength: 25,
        lengthMenu: [[ 25, 50, 100, -1], [25, 50, 100, "Todos"]],
        order: [[3, 'desc']], // Order by timestamp descending
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        // Export buttons configuration
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copiar',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude actions column
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                filename: 'amoniaco_' + new Date().toISOString().split('T')[0]
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                filename: 'amoniaco_' + new Date().toISOString().split('T')[0]
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                filename: 'amoniaco_' + new Date().toISOString().split('T')[0],
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                text: '<i class="fas fa-sync-alt"></i> Actualizar',
                className: 'btn btn-primary btn-sm',
                action: function(e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
        ],
        // Column visibility and reordering
        colReorder: true,
        stateSave: true,
        // Search highlighting
        searchHighlight: true,
        // Row selection
        select: {
            style: 'multi',
            selector: 'td:not(:last-child)'
        }
    });

    // Custom search functionality
    $('#ammoniaTable_filter input').attr('placeholder', 'Buscar en todos los campos...');
    
    // Add custom styling to buttons
    $('.dt-buttons').addClass('mb-3');
    $('.dt-button').removeClass('dt-button');
}

// Edit record
function editRecord(id) {
    // Get record data from DataTable
    const rowData = ammoniaDataTable.rows().data().toArray().find(r => r.id == id);
    if (!rowData) return;
    
    // Populate edit modal with record data
    document.getElementById('edit_id').value = rowData.id;
    document.getElementById('edit_pond_id').value = rowData.pond_id;
    document.getElementById('edit_ammonia_level').value = rowData.total_ammonia_mg_l;
    document.getElementById('edit_product').value = rowData.product;
    document.getElementById('edit_product_price').value = rowData.product_price;
    document.getElementById('edit_product_qty').value = rowData.product_qty;
    document.getElementById('edit_source').value = rowData.source;
    document.getElementById('edit_phase').value = rowData.phase;
    document.getElementById('edit_product').value = rowData.product || '';
    document.getElementById('edit_product_qty').value = rowData.product_qty || '';
    document.getElementById('edit_product_price').value = rowData.product_price || '';
    
    // Parse timestamp for date and time inputs
    const date = new Date(rowData.timestamp);
    document.getElementById('edit_fecha').value = date.toISOString().split('T')[0];
    document.getElementById('edit_hora').value = date.toTimeString().split(' ')[0].substring(0, 5);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editAmmoniaModal'));
    modal.show();
}

// Delete record
function deleteRecord(id, pondId) {
    Swal.fire({
        title: '¿Confirmar eliminación?',
        text: `¿Está seguro de eliminar el registro del estanque ${pondId}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
        
        fetch('process_water_ammonia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'delete',
            id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: 'Registro eliminado exitosamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            ammoniaDataTable.ajax.reload(); // Reload DataTable
            $(document).trigger('tableUpdated'); // Trigger statistics update
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al eliminar',
                text: data.message,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Por favor, inténtalo de nuevo.',
            confirmButtonText: 'Reintentar',
            confirmButtonColor: '#d33'
        });
    });
    }); // Close SweetAlert2 .then() block
}

// Save new record
function saveNewRecord() {
    const formData = {
        pond_id: document.getElementById('new_pond_id').value,
        total_ammonia_mg_l: document.getElementById('new_total_ammonia_mg_l').value,
        product: document.getElementById('new_product').value,
        product_price: document.getElementById('new_product_price').value,
        product_qty: document.getElementById('new_product_qty').value,
        source: document.getElementById('new_source').value,
        phase: document.getElementById('new_phase').value,
        fecha: document.getElementById('new_fecha').value,
        hora: document.getElementById('new_hora').value,
        product: document.getElementById('new_product').value,
        product_qty: document.getElementById('new_product_qty').value,
        product_price: document.getElementById('new_product_price').value
    };
    
    fetch('process_water_ammonia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'create',
            ...formData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Registro creado exitosamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            document.getElementById('newAmmoniaForm').reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('newEntryModal'));
            modal.hide();
            ammoniaDataTable.ajax.reload(); // Reload DataTable
            $(document).trigger('tableUpdated'); // Trigger statistics update
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al crear',
                text: data.message,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Por favor, inténtalo de nuevo.',
            confirmButtonText: 'Reintentar',
            confirmButtonColor: '#d33'
        });
    });
}

// Save edited record
function saveEditRecord() {
    const formData = {
        id: document.getElementById('edit_id').value,
        pond_id: document.getElementById('edit_pond_id').value,
        total_ammonia_mg_l: document.getElementById('edit_total_ammonia_level').value,
        product: document.getElementById('edit_product').value,
        product_price: document.getElementById('edit_product_price').value,
        product_qty: document.getElementById('edit_product_qty').value,
        source: document.getElementById('edit_source').value,
        phase: document.getElementById('edit_phase').value,
        fecha: document.getElementById('edit_fecha').value,
        hora: document.getElementById('edit_hora').value,
        product: document.getElementById('edit_product').value,
        product_qty: document.getElementById('edit_product_qty').value,
        product_price: document.getElementById('edit_product_price').value
    };
    
    fetch('process_water_ammonia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'update',
            ...formData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Registro actualizado exitosamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            const modal = bootstrap.Modal.getInstance(document.getElementById('editAmmoniaModal'));
            modal.hide();
            ammoniaDataTable.ajax.reload(); // Reload DataTable
            $(document).trigger('tableUpdated'); // Trigger statistics update
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al actualizar',
                text: data.message,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Por favor, inténtalo de nuevo.',
            confirmButtonText: 'Reintentar',
            confirmButtonColor: '#d33'
        });
    });
}
</script>
</body>
</html>