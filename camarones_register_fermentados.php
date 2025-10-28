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
<title>Camarones Alimento Fermentados</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/default_image.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!--Bootstrap 5 Css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<!-- Include Chart.js and Chart.js DataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<!-- Place these in the <head> section in this exact order -->

<!-- jQuery Core (main library) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

<!-- DataTables JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add these in the <head> section, after your existing DataTables CSS/JS -->
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="./camarones.css">
</head>
<body>
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

<!-- Tabla de Estimación de Consumo de Fermentados -->
<div class="container-fluid mt-4">
    <div class="card shadow-lg border-0">
        <div class="card-header position-relative overflow-hidden" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white;">
            <div class="position-relative">
                <h3 class="mb-0 text-center">
                    <i class="fas fa-flask me-3" style="color: white;"></i>
                    📊 Estimación de consumo diario de fermentados por millón de PLs
                    <i class="fas fa-flask ms-3" style="color: white;"></i>
                </h3>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white;">
                        <tr>
                            <th class="text-center"><i class="fas fa-calendar-day me-2"></i>Día del ciclo</th>
                            <th class="text-center"><i class="fas fa-shrimp me-2"></i>Tamaño PL aprox.</th>
                            <th class="text-center"><i class="fas fa-weight me-2"></i>Ración total estimada</th>
                            <th class="text-center"><i class="fas fa-percentage me-2"></i>% fermentados en dieta</th>
                            <th class="text-center"><i class="fas fa-balance-scale me-2"></i>Peso fermentados/día</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row-hover">
                            <td class="text-center fw-bold">Día 1–7</td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white;">PL10–PL15</span>
                            </td>
                            <td class="text-center fw-bold text-primary">20 kg/día</td>
                            <td class="text-center">5–8%</td>
                            <td class="text-center fw-bold" style="color: #6f42c1;">1.0–1.6 kg</td>
                        </tr>
                        <tr class="table-row-hover">
                            <td class="text-center fw-bold">Día 8–14</td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white;">PL15–PL20</span>
                            </td>
                            <td class="text-center fw-bold text-primary">15 kg/día</td>
                            <td class="text-center">4–6%</td>
                            <td class="text-center fw-bold" style="color: #6f42c1;">0.6–0.9 kg</td>
                        </tr>
                        <tr class="table-row-hover">
                            <td class="text-center fw-bold">Día 15–21</td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white;">PL20–PL25</span>
                            </td>
                            <td class="text-center fw-bold text-primary">10 kg/día</td>
                            <td class="text-center">3–5%</td>
                            <td class="text-center fw-bold" style="color: #6f42c1;">0.3–0.5 kg</td>
                        </tr>
                        <tr class="table-row-hover">
                            <td class="text-center fw-bold">Día 22–30</td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white;">PL25–PL30</span>
                            </td>
                            <td class="text-center fw-bold text-primary">8 kg/día</td>
                            <td class="text-center">2–4%</td>
                            <td class="text-center fw-bold" style="color: #6f42c1;">0.16–0.32 kg</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                <h5 class="mb-4 text-center" style="color: #6f42c1;">
                    💰 Precio estimado por kilo de fermentados
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 text-center">
                            <h6 class="fw-bold text-dark">
                                Costo por kilogramo:
                            </h6>
                            <p class="text-muted mb-0">
                                <span class="badge" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white; font-size: 1rem;">
                                    USD $2.50–$5.00/kg
                                </span>
                            </p>
                            <small class="text-muted">dependiendo del tipo (soya fermentada, levaduras, enzimas activadas, etc.)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 text-center">
                            <h6 class="fw-bold text-dark">
                                Costo diario por millón de PLs:
                            </h6>
                            <p class="text-muted mb-0">
                                <span class="badge" style="background: linear-gradient(135deg, #6f42c1 0%, #9561e2 100%); color: white; font-size: 1rem;">
                                    USD $0.40–$8.00
                                </span>
                            </p>
                            <small class="text-muted">según formulación</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Add back button before the header container -->
<a href="./camarones_registros.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="container text-center">
  <h3  class="container mt-4 text-white" class="collapse" id="fermentados">
  REGISTROS DE ALIMENTO FERMENTADOS
  </h3>
    
  <!-- New Concentrado Entry Modal -->
  
  <div class="modal fade" id="newConcentradoModal" tabindex="-1" aria-labelledby="newConcentradoModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newConcentradoModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Fermentado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newConcentradoForm">
                    <input type="hidden" name="id" id="new_id" value="">
                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="new_tagid" class="form-label text-start d-block">
                                    <i class="fas fa-tag me-2"></i>Estanque Tag ID
                                </label>
                                <input type="text" class="form-control" id="new_tagid" name="tagid" required>
                            </div>
                            <div class="mb-4">
                                <label for="new_alimento" class="form-label text-start d-block">
                                    <i class="fa-solid fa-syringe me-2"></i>Fermentado
                                </label>
                                <select class="form-select" id="new_alimento" name="alimento" required>
                                    <option value="">Productos</option>
                                    <?php
                                    try {
                                        $sql_alimentos = "SELECT DISTINCT cac_fermentados_nombre FROM cac_fermentados
                                         ORDER BY cac_fermentados_nombre ASC";
                                        $stmt_alimentos = $conn->prepare($sql_alimentos);
                                        $stmt_alimentos->execute();
                                        $alimentos = $stmt_alimentos->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($alimentos as $alimento_row) {
                                            echo '<option value="' . htmlspecialchars($alimento_row['cac_fermentados_nombre']) . '">' . htmlspecialchars($alimento_row['cac_fermentados_nombre']) . '</option>';
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Error fetching alimentos: " . $e->getMessage());
                                        echo '<option value="">Error al cargar alimentos</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="new_etapa" class="form-label text-start d-block">
                                    <i class="fa-solid fa-syringe me-2"></i>Etapa
                                </label>
                                <select class="form-select" id="new_etapa" name="etapa" required>
                                    <option value="">Seleccionar</option>
                                    <?php
                                    try {
                                        $sql_etapas = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
                                        $stmt_etapas = $conn->prepare($sql_etapas);
                                        $stmt_etapas->execute();
                                        $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($etapas as $etapa_row) {
                                            echo '<option value="' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '</option>';
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Error fetching etapas: " . $e->getMessage());
                                        echo '<option value="">Error al cargar etapas</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="new_racion" class="form-label text-start d-block">
                                    <i class="fa-solid fa-weight me-2"></i>Racion (kg)
                                </label>
                                <input type="text" class="form-control" id="new_racion" name="racion" required>
                            </div>
                        </div>
                        
                        <!-- Columna Derecha -->
                        <div class="col-md-6">
                        <div class="mb-4">
                                <label for="new_fecha_inicio" class="form-label text-start d-block">
                                    <i class="fas fa-calendar me-2"></i>Fecha Inicio
                                </label>
                                <input type="date" class="form-control" id="new_fecha_inicio" name="fecha_inicio" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="new_fecha_fin" class="form-label text-start d-block">
                                    <i class="fas fa-calendar me-2"></i>Fecha Fin
                                </label>
                                <input type="date" class="form-control" id="new_fecha_fin" name="fecha_fin" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="new_costo" class="form-label text-start d-block">
                                    <i class="fa-solid fa-dollar-sign me-2"></i>Costo ($/kg)
                                </label>
                                <input type="text" class="form-control" id="new_costo" name="costo" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewConcentrado">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
  
  <!-- DataTable for cah_fermentados records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="fermentadosTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">Acciones</th>
                      <th class="text-center">Fecha Inicio</th>
                      <th class="text-center">Fecha Fin</th>
                      <th class="text-center">Estanque</th>
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Etapa</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Racion (Kg/1M PLs/dia)</th>
                      <th class="text-center">Costo ($/kg)</th>
                      <th class="text-center">Valor Total ($/dia)</th>
                      <th class="text-center">Estatus</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                       // Query to get ALL fermentados records AND animals without fermentados records
                         $fermentadosQuery = "
                             -- First: All existing fermentados records with animal info
                             SELECT 
                                 a.id AS camarones_id,
                                 a.tagid,
                                 a.nombre,
                                 h.cah_fermentados_fecha_inicio,
                                 h.cah_fermentados_fecha_fin,
                                 h.id AS fermentados_record_id,
                                 h.cah_fermentados_etapa,
                                 h.cah_fermentados_producto,
                                 h.cah_fermentados_racion,
                                 h.cah_fermentados_costo,
                                 CAST((h.cah_fermentados_racion * h.cah_fermentados_costo) AS DECIMAL(10,2)) as total_value,
                                 1 AS in_fermentados_history,
                                 1 AS sort_order
                             FROM 
                                 cah_fermentados h
                             INNER JOIN 
                                 camarones a ON h.cah_fermentados_tagid = a.tagid
                             
                             UNION ALL
                             
                             -- Second: Animals without any fermentados records
                             SELECT 
                                 a.id AS camarones_id,
                                 a.tagid,
                                 a.nombre,
                                 NULL as cah_fermentados_fecha_inicio,
                                 NULL as cah_fermentados_fecha_fin,
                                 NULL as fermentados_record_id,
                                 NULL as cah_fermentados_etapa,
                                 NULL as cah_fermentados_producto,
                                 NULL as cah_fermentados_racion,
                                 NULL as cah_fermentados_costo,
                                 NULL as total_value,
                                 0 AS in_fermentados_history,
                                 2 AS sort_order
                             FROM 
                                 camarones a
                             WHERE 
                                 a.tagid NOT IN (SELECT DISTINCT cah_fermentados_tagid FROM cah_fermentados WHERE cah_fermentados_tagid IS NOT NULL)
                             
                             ORDER BY 
                                 sort_order ASC, 
                                 CASE WHEN sort_order = 1 THEN cah_fermentados_fecha_inicio END DESC,
                                 CASE WHEN sort_order = 1 THEN fermentados_record_id END DESC,
                                 CASE WHEN sort_order = 2 THEN nombre END ASC";

                        $stmt = $conn->prepare($fermentadosQuery);
                        $stmt->execute();
                        $fermentadosData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // If no data, display a message
                      if (empty($fermentadosData)) {
                          echo "<tr><td colspan='10' class='text-center'>No hay animales ni registros de fermentados</td></tr>";
                      } else {
                          // Get vigencia setting for fermentados records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT ca_vencimiento_fermentados FROM ca_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              
                              // Explicitly use PDO fetch method
                              $row = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row && isset($row['ca_vencimiento_fermentados'])) {
                                  $vigencia = intval($row['ca_vencimiento_fermentados']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                              // Continue with default value
                          }
                          
                          $currentDate = new DateTime();
                          
                          foreach ($fermentadosData as $row) {
                              echo "<tr>";
                              
                              // Actions column - Different buttons based on whether record has fermentados history
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              
                              // Always show Add (+) button
                              echo '        <button class="btn btn-success btn-sm" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#newConcentradoModal" 
                                              data-tagid-prefill="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                              title="Registrar Nueva Harina">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              // Only show Edit and Delete buttons for records with fermentados history
                              if ($row['in_fermentados_history'] == 1) {
                                  // Edit button
                                  echo '        <button class="btn btn-warning btn-sm edit-fermentados" 
                                                  data-id="'.htmlspecialchars($row['fermentados_record_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                                  data-etapa="'.htmlspecialchars($row['cah_fermentados_etapa'] ?? '').'" 
                                                  data-producto="'.htmlspecialchars($row['cah_fermentados_producto'] ?? '').'" 
                                                  data-racion="'.htmlspecialchars($row['cah_fermentados_racion'] ?? '').'" 
                                                  data-costo="'.htmlspecialchars($row['cah_fermentados_costo'] ?? '').'" 
                                                  data-fecha_inicio="'.htmlspecialchars($row['cah_fermentados_fecha_inicio'] ?? '').'" 
                                                  data-fecha_fin="'.htmlspecialchars($row['cah_fermentados_fecha_fin'] ?? '').'" 
                                                  title="Editar Registro">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                                  
                                  // Delete button
                                  echo '        <button class="btn btn-danger btn-sm delete-fermentados" 
                                                  data-id="'.htmlspecialchars($row['fermentados_record_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                                  title="Eliminar Registro">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              }
                              
                              echo '    </div>';
                              echo '</td>';

                              // Column 1: Fecha Inicio
                              echo "<td>" . htmlspecialchars($row['cah_fermentados_fecha_inicio'] ?? '') . "</td>";
                              // Column 2: Fecha Fin
                              echo "<td>" . htmlspecialchars($row['cah_fermentados_fecha_fin'] ?? '') . "</td>";
                              // Column 3: Estanque
                              echo "<td>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                              // Column 4: Tag ID Estanque
                              echo "<td>" . htmlspecialchars($row['tagid'] ?? 'N/A') . "</td>";

                              if ($row['in_fermentados_history'] == 1) {
                                  // Record has fermentados data
                                  // Column 5: Etapa
                                  echo "<td>" . htmlspecialchars($row['cah_fermentados_etapa'] ?? 'N/A') . "</td>";
                                  // Column 6: Producto
                                  echo "<td>" . htmlspecialchars($row['cah_fermentados_producto'] ?? 'N/A') . "</td>";
                                  // Column 7: Racion
                                  echo "<td>" . htmlspecialchars($row['cah_fermentados_racion'] ?? 'N/A') . "</td>";
                                  // Column 8: Costo
                                  echo "<td>" . htmlspecialchars($row['cah_fermentados_costo'] ?? 'N/A') . "</td>";
                                  // Column 9: Total Value
                                  echo "<td>" . htmlspecialchars($row['total_value'] ?? 'N/A') . "</td>";

                                  // Column 10: Status - Calculate due date and determine status
                                  try {
                                      if (!empty($row['cah_fermentados_fecha_inicio'])) {
                                          $fermentadosDate = new DateTime($row['cah_fermentados_fecha_inicio']);
                                          $dueDate = clone $fermentadosDate;
                                          $dueDate->modify("+{$vigencia} days");

                                          if ($currentDate > $dueDate) {
                                              echo '<td class="text-center"><span class="badge bg-danger">VENCIDO</span></td>';
                                          } else {
                                              echo '<td class="text-center"><span class="badge bg-success">VIGENTE</span></td>';
                                          }
                                      } else {
                                           echo '<td class="text-center"><span class="badge bg-secondary">Sin Fecha</span></td>';
                                      }
                                  } catch (Exception $e) {
                                      error_log("Date error: " . $e->getMessage() . " for date: " . $row['cah_fermentados_fecha_inicio']);
                                      echo '<td class="text-center"><span class="badge bg-secondary">ERROR FECHA</span></td>';
                                  }
                              } else {
                                  // Animal has no fermentados history - show placeholder data
                                  echo "<td><em>No Registrado</em></td>"; // Etapa
                                  echo "<td><em>No Registrado</em></td>"; // Producto
                                  echo "<td><em>No Registrado</em></td>"; // Racion
                                  echo "<td><em>No Registrado</em></td>"; // Costo
                                  echo "<td><em>No Registrado</em></td>"; // Total Value
                                  echo '<td class="text-center"><span class="badge bg-warning">SIN REGISTRO</span></td>'; // Status
                              }
                              
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in fermentados table: " . $e->getMessage());
                      echo "<tr><td colspan='10' class='text-center'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Initialize DataTable for VH Concentrado -->
<script>
$(document).ready(function() {
    $('#fermentadosTable').DataTable({
        // Set initial page length
        pageLength: 25,
        
        // Configure length menu options
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
        ],
        
        // Order by fecha (date) column descending
        order: [[1, 'desc']],
        
        // Spanish language
        language: {
            url: 'es-ES.json',
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        
        // Enable responsive features
        responsive: true,
        
        // Configure DOM layout and buttons
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"l>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        
        buttons: [
            {
                extend: 'collection',
                text: 'Exportar',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                    'print'
                ]
            }
        ],
        
        // Column specific settings
        columnDefs: [
            {
                targets: [0], // Actions column
                orderable: false,
                searchable: false
            },
            {
                targets: [7, 8, 9], // Racion, Costo, Valor Total columns
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data === 'N/A') return data; // Pass through 'N/A'
                        const number = parseFloat(data);
                        if (!isNaN(number)) {
                            return number.toLocaleString('es-ES', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        } else {
                            return data; // Return original if parsing failed but wasn't N/A
                        }
                    }
                    return data;
                }
            },
            {
                targets: [1,2], // Fecha Inicio, Fecha Fin columns
                type: 'date-eu', // Help DataTables sort European date format
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data === 'N/A') return data; // Pass through 'N/A'
                        // Date is already formatted DD/MM/YYYY in PHP
                        return data; 
                    }
                    // For sorting/filtering, return the original YYYY-MM-DD if possible, or null
                    if (type === 'sort' || type === 'filter') {
                         // We need the original YYYY-MM-DD date here for correct sorting.
                         // Let's assume the raw data is the 2nd element in the row array `row[1]`
                         // Note: This depends on DataTables internal structure and might need adjustment
                         // A better approach is to fetch YYYY-MM-DD in PHP and pass it via a hidden column or data attribute
                         // For now, let's try getting it from the raw row data for the corresponding display column
                         // If the display data `data` is 'N/A', sorting value should be null or minimal
                         if (data === 'N/A') return null; 
                         // Attempt to convert DD/MM/YYYY back to YYYY-MM-DD for sorting
                         const parts = data.split('/');
                         if (parts.length === 3) {
                            return parts[2] + '-' + parts[1] + '-' + parts[0];
                         }
                         return null; // Fallback if conversion fails
                    }
                    return data;
                }
            },
            {
                targets: [10], // Status column
                orderable: true,
                searchable: true
            }
        ]
    });
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    var newConcentradoModalEl = document.getElementById('newConcentradoModal');
    var tagIdInput = document.getElementById('new_tagid');

    // --- Pre-fill Tag ID when New Concentrado Modal opens --- 
    if (newConcentradoModalEl && tagIdInput) {
        newConcentradoModalEl.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget; 
            
            if (button) { // Check if modal was triggered by a button
                // Extract info from data-* attributes
                var tagid = button.getAttribute('data-tagid-prefill');
                
                // Update the modal's input field
                if (tagid) {
                    tagIdInput.value = tagid;
                } else {
                     tagIdInput.value = ''; // Clear if no tagid passed
                }
            } else {
                tagIdInput.value = ''; // Clear if opened programmatically without a relatedTarget
            }
        });

        // Optional: Clear the input when the modal is hidden to avoid stale data
        newConcentradoModalEl.addEventListener('hidden.bs.modal', function (event) {
            tagIdInput.value = ''; 
            // Optionally reset form validation state
            $('#newConcentradoForm').removeClass('was-validated'); 
            document.getElementById('newConcentradoForm').reset(); // Reset other fields too
        });
    }
    // --- End Pre-fill Logic ---
    
    // Handle new entry form submission
    $('#saveNewConcentrado').click(function() {
        // Validate the form
        var form = document.getElementById('newConcentradoForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            racion: $('#new_racion').val(),
            etapa: $('#new_etapa').val(),
            producto: $('#new_alimento').val(),
            costo: $('#new_costo').val(),
            fecha_inicio: $('#new_fecha_inicio').val(),
            fecha_fin: $('#new_fecha_fin').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar el registro de alimento fermentados ${formData.racion} kg para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_fermentados.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        racion: formData.racion,
                        etapa: formData.etapa,
                        producto: formData.producto,
                        costo: formData.costo,
                        fecha_inicio: formData.fecha_inicio,
                        fecha_fin: formData.fecha_fin
                    },
                    success: function(response) {
                        // Parse response if it's a string
                        if (typeof response === 'string') {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {
                                console.error('Error parsing response:', e);
                                response = { success: false, message: 'Error en la respuesta del servidor' };
                            }
                        }
                        
                        if (response.success) {
                            // Close the modal
                            var modal = bootstrap.Modal.getInstance(document.getElementById('newConcentradoModal'));
                            modal.hide();
                            
                            // Show success message
                            Swal.fire({
                                title: '¡Registro exitoso!',
                                text: 'El registro de alimento fermentados ha sido guardado correctamente',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to show updated data
                                location.reload();
                            });
                        } else {
                            // Show error message from server
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Error al procesar la solicitud',
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

    // Handle edit button click
    $('.edit-fermentados').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var etapa = $(this).data('etapa');
        var producto = $(this).data('producto');
        var racion = $(this).data('racion');
        var costo = $(this).data('costo');
        var fecha_inicio = $(this).data('fecha_inicio');
        var fecha_fin = $(this).data('fecha_fin');
        
        // Edit Concentrado Modal dialog for editing

        var modalHtml = `
        <div class="modal fade" id="editConcentradoModal" tabindex="-1" aria-labelledby="editConcentradoModalLabel">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editConcentradoModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Alimento Concentrado
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editConcentradoForm">
                            <input type="hidden" id="edit_id" value="${id}">
                            <div class="row">
                                <!-- Columna Izquierda -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="edit_tagid" class="form-label text-start d-block">
                                            <i class="fas fa-tag me-2"></i>Estanque Tag ID
                                        </label>
                                        <input type="text" class="form-control" id="edit_tagid" value="${tagid}" readonly>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="edit_producto" class="form-label text-start d-block">
                                            <i class="fa-solid fa-syringe me-2"></i>Concentrado
                                        </label>
                                        <select class="form-select" id="edit_producto" name="producto" required>
                                            <option value="">Seleccionar Producto</option>
                                            <?php
                                            try {
                                                $sql_productos = "SELECT DISTINCT cac_fermentados_nombre FROM cac_fermentados ORDER BY cac_fermentados_nombre ASC";
                                                $stmt_productos = $conn->prepare($sql_productos);
                                                $stmt_productos->execute();
                                                $productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($productos as $producto_row) {
                                                    echo '<option value="' . htmlspecialchars($producto_row['cac_fermentados_nombre']) . '">' . htmlspecialchars($producto_row['cac_fermentados_nombre']) . '</option>';
                                                }
                                            } catch (PDOException $e) {
                                                error_log("Error fetching productos: " . $e->getMessage());
                                                echo '<option value="">Error al cargar productos</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit_etapa" class="form-label text-start d-block">
                                            <i class="fa-solid fa-syringe me-2"></i>Etapa
                                        </label>
                                        <select class="form-select" id="edit_etapa" name="etapa" required>
                                            <option value="">Seleccionar Etapa</option>
                                            <?php
                                            try {
                                                $sql_etapas = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
                                                $stmt_etapas = $conn->prepare($sql_etapas);
                                                $stmt_etapas->execute();
                                                $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($etapas as $etapa_row) {
                                                    echo '<option value="' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '</option>';
                                                }
                                            } catch (PDOException $e) {
                                                error_log("Error fetching etapas: " . $e->getMessage());
                                                echo '<option value="">Error al cargar etapas</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit_racion" class="form-label text-start d-block">
                                            <i class="fas fa-weight me-2"></i>Racion (kg)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="edit_racion" value="${racion}" required>
                                    </div>
                                </div>
                                
                                <!-- Columna Derecha -->
                                <div class="col-md-6">                                
                                    <div class="mb-4">
                                        <label for="edit_fecha_inicio" class="form-label text-start d-block">
                                            <i class="fas fa-calendar me-2"></i>Fecha Inicio
                                        </label>
                                        <input type="date" class="form-control" id="edit_fecha_inicio" name="fecha_inicio" value="${fecha_inicio}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit_fecha_fin" class="form-label text-start d-block">
                                            <i class="fas fa-calendar me-2"></i>Fecha Fin
                                        </label>
                                        <input type="date" class="form-control" id="edit_fecha_fin" name="fecha_fin" value="${fecha_fin}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit_costo" class="form-label text-start d-block">
                                            <i class="fas fa-dollar-sign me-2"></i>Costo ($/kg)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="edit_costo" value="${costo}" required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditConcentrado">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editConcentradoModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editConcentradoModal'));
        editModal.show();
        
        // Set the selected values for the dropdowns after modal is shown
        setTimeout(function() {
            $('#edit_etapa').val(etapa);
            $('#edit_producto').val(producto);
        }, 100);
        
        // Handle save button click
        $('#saveEditConcentrado').click(function() {
            var formData = {
                id: $('#edit_id').val(),
                tagid: $('#edit_tagid').val(),
                racion: $('#edit_racion').val(),
                etapa: $('#edit_etapa').val(),
                producto: $('#edit_producto').val(),
                costo: $('#edit_costo').val(),
                fecha_inicio: $('#edit_fecha_inicio').val(),
                fecha_fin: $('#edit_fecha_fin').val()
            };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar el registro de alimento fermentados para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_fermentados.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            tagid: formData.tagid,
                            racion: formData.racion,
                            etapa: formData.etapa,
                            producto: formData.producto,
                            costo: formData.costo,
                            fecha_inicio: formData.fecha_inicio,
                            fecha_fin: formData.fecha_fin
                        },
                        success: function(response) {
                            // Parse response if it's a string
                            if (typeof response === 'string') {
                                try {
                                    response = JSON.parse(response);
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                    response = { success: false, message: 'Error en la respuesta del servidor' };
                                }
                            }
                            
                            if (response.success) {
                                // Close the modal
                                editModal.hide();
                                
                                // Show success message
                                Swal.fire({
                                    title: '¡Actualización exitosa!',
                                    text: 'El registro ha sido actualizado correctamente',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Reload the page to show updated data
                                    location.reload();
                                });
                            } else {
                                // Show error message from server
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Error al procesar la solicitud',
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
    });
    
    // Handle delete button click
    $('.delete-fermentados').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).closest('tr').find('td:eq(3)').text().trim(); // Get Tag ID from the 4th column
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `¿Está seguro de que desea eliminar el registro para el animal con Tag ID ${tagid}? Esta acción no se puede deshacer.`,
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
                    url: 'process_fermentados.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        // Show success message
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'El registro ha sido eliminado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Reload the page to show updated data
                            location.reload();
                        });
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
});
</script>

<!-- Monthly Concentrado Expense Chart -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>Gastos Mensuales en Alimento Concentrado
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <select id="fermentadosYearFilter" class="form-select">
                        <option value="all">Todos los años</option>
                        <!-- Years will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showCumulativeToggle" checked>
                        <label class="form-check-label" for="showCumulativeToggle">
                            Mostrar línea acumulativa
                        </label>
                    </div>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height:60vh; width:100%">
                <canvas id="fermentadosExpenseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Monthly Concentrado Expense Chart -->
<script>
$(document).ready(function() {
    let fermentadosExpenseChart = null;
    let allConcentradoData = [];
    
    // Fetch monthly fermentados expense data from server
    $.ajax({
        url: 'get_monthly_fermentados_expense_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            console.log('Concentrado expense data received:', data);
            allConcentradoData = data;
            
            if (data.length === 0) {
                console.log('No fermentados expense data available');
                // Still create chart with empty data to show "No data" message
                createConcentradoExpenseChart([]);
                return;
            }
            
            createConcentradoExpenseChart(data);
            populateConcentradoYearFilter(data);
            
            // Add event listeners for filters
            $('#fermentadosYearFilter').on('change', function() {
                updateConcentradoChart();
            });
            
            $('#showCumulativeToggle').on('change', function() {
                updateConcentradoChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching fermentados expense data:', error);
            // Fallback to generate the chart with empty data
            createConcentradoExpenseChart([]);
        }
    });
    
    function populateConcentradoYearFilter(data) {
        // Extract unique years from the data
        const years = [...new Set(data.map(item => item.year))];
        years.sort(); // Sort years chronologically
        
        // Add options to the dropdown
        const yearFilter = $('#fermentadosYearFilter');
        yearFilter.empty().append('<option value="all">Todos los años</option>');
        years.forEach(year => {
            yearFilter.append(`<option value="${year}">${year}</option>`);
        });
    }
    
    function updateConcentradoChart() {
        const selectedYear = $('#fermentadosYearFilter').val();
        
        let filteredData = [...allConcentradoData];
        
        // Filter by year if not "all"
        if (selectedYear !== 'all') {
            filteredData = filteredData.filter(item => item.year == selectedYear);
        }
        
        // Update chart with filtered data
        if (fermentadosExpenseChart) {
            fermentadosExpenseChart.destroy();
        }
        createConcentradoExpenseChart(filteredData);
    }
    
    function createConcentradoExpenseChart(data) {
        const ctx = document.getElementById('fermentadosExpenseChart').getContext('2d');
        
        // Prepare data arrays
        const monthLabels = [];
        const expenseData = [];
        const cumulativeData = [];
        const showCumulative = $('#showCumulativeToggle').is(':checked');
        
        console.log('Creating fermentados expense chart with data:', data);
        
        if (data.length === 0) {
            // Show "No data" message
            monthLabels.push('Sin Datos');
            expenseData.push(0);
            cumulativeData.push(0);
        } else {
            // Sort data by year and month
            data.sort((a, b) => {
                if (a.year !== b.year) {
                    return a.year - b.year;
                }
                return a.month - b.month;
            });
            
            // Calculate cumulative total
            let runningTotal = 0;
            
            // Prepare data for chart
            data.forEach(item => {
                // Format month name
                const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                const monthIndex = parseInt(item.month) - 1; // Convert to 0-based index
                const monthName = monthNames[monthIndex];
                
                // Create label in format "Month Year" (e.g., "Enero 2023")
                const label = `${monthName} ${item.year}`;
                monthLabels.push(label);
                
                // Monthly expense data
                const monthlyExpense = parseFloat(item.total_expense);
                expenseData.push(monthlyExpense);
                
                // Add to cumulative total
                runningTotal += monthlyExpense;
                cumulativeData.push(runningTotal);
            });
        }
        
        console.log('Chart data - Labels:', monthLabels, 'Expense:', expenseData, 'Cumulative:', cumulativeData);
        
        // Prepare datasets
        const datasets = [
            {
                label: 'Gasto Mensual ($)',
                data: expenseData,
                backgroundColor: 'rgba(255, 193, 7, 0.6)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 2,
                type: 'bar',
                yAxisID: 'y'
            }
        ];
        
        // Add cumulative line if toggle is checked
        if (showCumulative) {
            datasets.push({
                label: 'Acumulado ($)',
                data: cumulativeData,
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 3,
                pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3,
                type: 'line',
                yAxisID: 'y1',
                fill: false
            });
        }
        
        // Create chart
        fermentadosExpenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.type === 'line') {
                                    return 'Acumulado: $' + context.parsed.y.toLocaleString('es-ES', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                } else {
                                    return 'Gasto Mensual: $' + context.parsed.y.toLocaleString('es-ES', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            },
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            const selectedYear = $('#fermentadosYearFilter').val();
                            if (selectedYear !== 'all') {
                                return `Gastos en Alimento Concentrado - ${selectedYear}`;
                            }
                            return 'Gastos Mensuales en Alimento Concentrado - Todos los Años';
                        },
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Gasto Mensual ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(255, 193, 7, 1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            },
                            color: 'rgba(255, 193, 7, 1)'
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(255, 193, 7, 0.1)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: showCumulative,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Acumulado ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(220, 53, 69, 1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            },
                            color: 'rgba(220, 53, 69, 1)'
                        },
                        grid: {
                            drawOnChartArea: false,
                            color: 'rgba(220, 53, 69, 0.1)'
                        }
                    }
                }
            }
        });
    }
});
</script>

<!-- Monthly Feed Conversion Ratio Chart -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-line me-2"></i>Índice de Conversión Alimenticia Mensual (FCR)
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <select id="fcrYearFilter" class="form-select">
                        <option value="all">Todos los años</option>
                        <!-- Years will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showEfficiencyToggle" checked>
                        <label class="form-check-label" for="showEfficiencyToggle">
                            Mostrar línea de eficiencia objetivo (FCR 2.0)
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">
                        <strong>FCR:</strong> kg de alimento por kg de peso ganado<br>
                        <span class="badge bg-success">≤2.0 Excelente</span>
                        <span class="badge bg-primary">2.1-2.5 Bueno</span>
                        <span class="badge bg-warning">2.6-3.0 Regular</span>
                        <span class="badge bg-danger">>3.0 Mejorar</span>
                    </small>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height:60vh; width:100%">
                <canvas id="fcrChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Monthly FCR Chart -->
<script>
$(document).ready(function() {
    let fcrChart = null;
    let allFCRData = [];
    
    // Fetch monthly FCR data from server
    $.ajax({
        url: 'get_monthly_fcr_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            console.log('FCR data received:', data);
            allFCRData = data;
            
            if (data.length === 0) {
                console.log('No FCR data available');
                // Still create chart with empty data to show "No data" message
                createFCRChart([]);
                return;
            }
            
            createFCRChart(data);
            populateFCRYearFilter(data);
            
            // Add event listeners for filters
            $('#fcrYearFilter').on('change', function() {
                updateFCRChart();
            });
            
            $('#showEfficiencyToggle').on('change', function() {
                updateFCRChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching FCR data:', error);
            // Fallback to generate the chart with empty data
            createFCRChart([]);
        }
    });
    
    function populateFCRYearFilter(data) {
        // Extract unique years from the data
        const years = [...new Set(data.map(item => item.year))];
        years.sort(); // Sort years chronologically
        
        // Add options to the dropdown
        const yearFilter = $('#fcrYearFilter');
        yearFilter.empty().append('<option value="all">Todos los años</option>');
        years.forEach(year => {
            yearFilter.append(`<option value="${year}">${year}</option>`);
        });
    }
    
    function updateFCRChart() {
        const selectedYear = $('#fcrYearFilter').val();
        
        let filteredData = [...allFCRData];
        
        // Filter by year if not "all"
        if (selectedYear !== 'all') {
            filteredData = filteredData.filter(item => item.year == selectedYear);
        }
        
        // Update chart with filtered data
        if (fcrChart) {
            fcrChart.destroy();
        }
        createFCRChart(filteredData);
    }
    
    function createFCRChart(data) {
        const ctx = document.getElementById('fcrChart').getContext('2d');
        
        // Prepare data arrays
        const monthLabels = [];
        const fcrData = [];
        const averageFCRData = [];
        const efficiencyLine = [];
        const showEfficiency = $('#showEfficiencyToggle').is(':checked');
        
        console.log('Creating FCR chart with data:', data);
        
        if (data.length === 0) {
            // Show "No data" message
            monthLabels.push('Sin Datos');
            fcrData.push(0);
            averageFCRData.push(0);
            efficiencyLine.push(2.0);
        } else {
            // Sort data by year and month
            data.sort((a, b) => {
                if (a.year !== b.year) {
                    return a.year - b.year;
                }
                return a.month - b.month;
            });
            
            // Prepare data for chart
            data.forEach(item => {
                // Format month name
                const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                const monthIndex = parseInt(item.month) - 1; // Convert to 0-based index
                const monthName = monthNames[monthIndex];
                
                // Create label in format "Month Year" (e.g., "Enero 2023")
                const label = `${monthName} ${item.year}`;
                monthLabels.push(label);
                
                // FCR data
                fcrData.push(parseFloat(item.overall_fcr));
                averageFCRData.push(parseFloat(item.average_fcr));
                efficiencyLine.push(2.0); // Target efficiency line
            });
        }
        
        console.log('FCR Chart data - Labels:', monthLabels, 'Overall FCR:', fcrData, 'Average FCR:', averageFCRData);
        
        // Prepare datasets
        const datasets = [
            {
                label: 'FCR General (kg alimento/kg ganancia)',
                data: fcrData,
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 3,
                pointBackgroundColor: function(context) {
                    const value = context.parsed.y;
                    if (value <= 2.0) return 'rgba(40, 167, 69, 1)'; // Green - Excellent
                    if (value <= 2.5) return 'rgba(23, 162, 184, 1)'; // Blue - Good
                    if (value <= 3.0) return 'rgba(255, 193, 7, 1)'; // Yellow - Regular
                    return 'rgba(220, 53, 69, 1)'; // Red - Needs improvement
                },
                pointBorderColor: '#fff',
                pointRadius: 6,
                pointHoverRadius: 8,
                tension: 0.3,
                type: 'line',
                yAxisID: 'y'
            },
            {
                label: 'FCR Promedio Individual',
                data: averageFCRData,
                backgroundColor: 'rgba(108, 117, 125, 0.1)',
                borderColor: 'rgba(108, 117, 125, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(108, 117, 125, 1)',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.3,
                type: 'line',
                yAxisID: 'y',
                borderDash: [5, 5] // Dashed line
            }
        ];
        
        // Add efficiency target line if toggle is checked
        if (showEfficiency) {
            datasets.push({
                label: 'Objetivo de Eficiencia (FCR 2.0)',
                data: efficiencyLine,
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 0,
                tension: 0,
                type: 'line',
                yAxisID: 'y',
                borderDash: [10, 5], // Dashed line
                fill: false
            });
        }
        
        // Create chart
        fcrChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dataIndex = context.dataIndex;
                                const monthData = data[dataIndex];
                                
                                if (context.dataset.label.includes('Objetivo')) {
                                    return 'Objetivo: 2.0 kg alimento/kg ganancia';
                                }
                                
                                if (monthData) {
                                    const tooltipLines = [
                                        context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' kg/kg',
                                        'Eficiencia: ' + monthData.efficiency_rating,
                                        'Animales evaluados: ' + monthData.animal_count,
                                        'Alimento total: ' + monthData.total_feed.toFixed(2) + ' kg',
                                        'Ganancia total: ' + monthData.total_weight_gain.toFixed(2) + ' kg'
                                    ];
                                    return tooltipLines;
                                }
                                
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' kg/kg';
                            },
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            const selectedYear = $('#fcrYearFilter').val();
                            if (selectedYear !== 'all') {
                                return `Índice de Conversión Alimenticia - ${selectedYear}`;
                            }
                            return 'Índice de Conversión Alimenticia Mensual - Todos los Años';
                        },
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'FCR (kg alimento / kg ganancia de peso)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(23, 162, 184, 1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2) + ' kg/kg';
                            },
                            color: 'rgba(23, 162, 184, 1)'
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(23, 162, 184, 0.1)'
                        },
                        // Add color zones for FCR efficiency
                        plugins: {
                            annotation: {
                                annotations: {
                                    excellent: {
                                        type: 'box',
                                        yMin: 0,
                                        yMax: 2.0,
                                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                        borderWidth: 0
                                    },
                                    good: {
                                        type: 'box',
                                        yMin: 2.0,
                                        yMax: 2.5,
                                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                                        borderWidth: 0
                                    },
                                    regular: {
                                        type: 'box',
                                        yMin: 2.5,
                                        yMax: 3.0,
                                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                        borderWidth: 0
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

</body>
</html>
