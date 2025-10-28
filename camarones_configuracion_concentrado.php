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
<title>Camarones PLAN ALIMENTO CONCENTRADO</title>
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
<!-- Add back button before the header container -->
<a href="./camarones_configuracion.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
<!-- Icon Navigation Buttons -->

<div class="container nav-icons-container">
    <div class="icon-button-container">
        <button onclick="window.location.href='../inicio.php'" class="icon-button">
            <img src="./images/default_image.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INICIO</span>
    </div>
    <div class="icon-button-container">
        <button onclick="window.location.href='./camarones_registros.php'" class="icon-button">
            <img src="./images/registros.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">REGISTROS</span>
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
</div>

<!-- Nutritional Plan Table -->
<div class="container mt-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-center text-dark">
                <i class="fas fa-flask me-2"></i>🧪 Plan nutricional por fase – en gramos por lote de 100 kg
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 20%;">Fase del camarón</th>
                            <th class="text-center" style="width: 20%;">ABA comercial (g)</th>
                            <th class="text-center" style="width: 20%;">Harinas proteicas (g)</th>
                            <th class="text-center" style="width: 20%;">Fermentados / ensilados (g)</th>
                            <th class="text-center" style="width: 20%;">Observaciones clave</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center fw-bold text-primary">
                                <span class="badge bg-primary">Larvicultura (PL1–PL15)</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">70,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6">28,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark fs-6">2,000</span>
                            </td>
                            <td class="text-center small">Alta digestibilidad. Fermentados limitados por sensibilidad intestinal</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold text-success">
                                <span class="badge bg-success">Juvenil (5–10 g)</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">45,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6">45,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark fs-6">10,000</span>
                            </td>
                            <td class="text-center small">Se introduce ensilado de pescado o subproductos vegetales</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold text-info">
                                <span class="badge bg-info">Engorde (10–20 g)</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">35,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6">50,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark fs-6">15,000</span>
                            </td>
                            <td class="text-center small">Fermentados mejoran conversión y salud intestinal</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold text-warning">
                                <span class="badge bg-warning text-dark">Acabado / Pre-cosecha</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">30,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6">55,000</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark fs-6">15,000</span>
                            </td>
                            <td class="text-center small">Se optimiza perfil de aminoácidos y energía digestible</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-12">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>📌 Detalles técnicos por ingrediente
                    </h6>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body p-3">
                            <h6 class="card-title text-primary">
                                <i class="fas fa-cube me-1"></i>ABA comercial
                            </h6>
                            <p class="card-text small text-muted">
                                Incluye núcleo vitamínico-mineral, aminoácidos sintéticos, antioxidantes, y aditivos funcionales.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-body p-3">
                            <h6 class="card-title text-info">
                                <i class="fas fa-seedling me-1"></i>Harinas proteicas
                            </h6>
                            <p class="card-text small text-muted">
                                Mezcla de harina de soya, trigo, arroz, pescado, y subproductos vegetales según disponibilidad local.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body p-3">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-flask me-1"></i>Fermentados / ensilados
                            </h6>
                            <p class="card-text small text-muted">
                                Pueden incluir ensilado biológico de pescado, levaduras funcionales, o subproductos fermentados de arroz y yuca.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container text-center">
  <h3  class="container mt-4 text-white" class="collapse" id="section-historial-produccion-camarones">
  CONFIGURACION ABA CAMARONES
  </h3>
  <p class="text-dark-50 text-center mb-4">Esta tabla muestra el plan de alimentación recomendado para camarones</p>
</div> 
<!-- Add New Alimento Concentrado Button -->
<div class="container my-3 text-center">
  <button type="button" class="btn btn-success text-center" data-bs-toggle="modal" data-bs-target="#newEntryModal">
    <i class="fas fa-plus-circle me-2"></i>Nuevo Concentrado Camarones
  </button>
</div>
<div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="newEntryModalLabel">
                  <i class="fas fa-plus-circle me-2"></i>Configurar Nuevo Concentrado Camarones
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form id="newConcentradoForm">
              <input type="hidden" id="new_id" name="id" value="">
                  <div class="mb-4">
                      <div class="input-group">
                          <span class="input-group-text">
                              <i class="fa-solid fa-syringe"></i>
                              <label for="new_concentrado" class="form-label">Concentrado</label>
                            <input type="text" class="form-control" id="new_concentrado" name="concentrado" required>
                          </span>
                      </div>
                  </div>
                  <div class="mb-4">
                      <div class="input-group">
                          <span class="input-group-text">
                              <i class="fa-solid fa-syringe"></i>
                              <label for="new_etapa" class="form-label">Etapa</label>
                              <select class="form-select" id="new_etapa" name="etapa" required>
                                  <option value="">Seleccionar</option>
                                  <?php
                                  $sql_etapas = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
                                  $stmt_etapas = $conn->prepare($sql_etapas);
                                  $stmt_etapas->execute();
                                  $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                  foreach ($etapas as $etapa_row) {
                                      echo '<option value="' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '</option>';
                                  }
                                  ?>
                              </select>
                          </span>
                      </div>
                  </div>
                  <div class="mb-4">
                      <div class="input-group">
                          <span class="input-group-text">
                              <i class="fa-solid fa-money-bill-1-wave"></i>
                              <label for="new_costo" class="form-label">Costo ($)</label>
                              <input type="number" step="0.01" class="form-control" id="new_costo" name="costo" required>
                          </span>
                      </div>
                  </div>
                  <div class="mb-4">
                      <div class="input-group">
                          <span class="input-group-text">
                              <i class="fa-solid fa-calendar-days"></i>
                              <label for="new_vigencia" class="form-label">Vigencia (dias)</label>
                              <input type="number" class="form-control" id="new_vigencia" name="vigencia" required>
                          </span>
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

  <!-- DataTable for cac_concentrado records -->
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="concentradoTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                    <th class="text-center">Acciones</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center">Etapa</th>
                    <th class="text-center">Costo ($/kg)</th>
                    <th class="text-center">Vigencia (dias)</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                      $concentradoQuery = "SELECT * FROM cac_concentrado";

                      $stmt = $conn->prepare($concentradoQuery);
                      $stmt->execute();
                      $concentradosData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                             if (empty($concentradosData)) {
                           echo "<tr><td colspan='5' class='text-center'>No hay registros disponibles</td></tr>";
                       } else {
                          foreach ($concentradosData as $row) {
                              echo "<tr>";
                              
                              // Column 0: Actions
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              echo '        <button class="btn btn-warning btn-sm edit-concentrado" 
                                              data-id="' . htmlspecialchars($row['id'] ?? '') . '" 
                                              data-concentrado="' . htmlspecialchars($row['cac_concentrado_nombre'] ?? '') . '" 
                                              data-etapa="' . htmlspecialchars($row['cac_concentrado_etapa'] ?? '') . '"
                                              data-costo="' . htmlspecialchars($row['cac_concentrado_costo'] ?? '') . '" 
                                              data-vigencia="' . htmlspecialchars($row['cac_concentrado_vigencia'] ?? '') . '"
                                              title="Editar Configuracion Vacuna Concentrado">
                                              <i class="fas fa-edit"></i>
                                          </button>';
                              echo '        <button class="btn btn-danger btn-sm delete-concentrado" 
                                              data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                              title="Eliminar Configuracion Vacuna Concentrado">
                                              <i class="fas fa-trash"></i>
                                          </button>';
                              echo '    </div>';
                              echo '</td>';
                              
                              // Column 1: Vacuna
                              echo "<td>" . htmlspecialchars($row['cac_concentrado_nombre'] ?? '') . "</td>";
                              // Columna 2: Etapa
                              echo "<td>" . htmlspecialchars($row['cac_concentrado_etapa'] ?? '') . "</td>";
                              // Column 4: Costo
                              echo "<td>" . htmlspecialchars($row['cac_concentrado_costo'] ?? 'N/A') . "</td>";
                              // Column 5: Vigencia
                              echo "<td>" . htmlspecialchars($row['cac_concentrado_vigencia'] ?? 'N/A') . "</td>";
                              echo "</tr>";
                          }
                      }
                  ?>
              </tbody>
          </table>
      </div>
</div>

<!-- Initialize DataTable for concentradoTable -->
<script>
$(document).ready(function() {
    $('#concentradoTable').DataTable({
        // Set initial page length
        pageLength: 10,
        
        // Configure length menu options
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Todos"]
        ],
        
        // Order by Vigencia column descending (column index 4)
        order: [[1, 'asc']],
        
        // Spanish language
        language: {
            url: './es-ES.json',
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
        
                 // Column specific settings - Updated indices for 5 columns
         columnDefs: [
             {
                 targets: [3, 4], // Costo, Vigencia columns
                 render: function(data, type, row) {
                     if (type === 'display' && data !== 'N/A' && data !== 'No Registrado') {
                         // Attempt to parse only if data looks like a number
                          const num = parseFloat(data);
                          if (!isNaN(num)) {
                              return num.toLocaleString('es-ES', {
                                  minimumFractionDigits: 2,
                                  maximumFractionDigits: 2
                              });
                          }
                     }
                     return data; // Return original data if not display or not a valid number
                 }
             },
             {
                 targets: [4], // Vigencia column
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
    // --- Initialize Modals Once --- 
    var newEntryModalElement = document.getElementById('newEntryModal');
    var newEntryModalInstance = new bootstrap.Modal(newEntryModalElement); 
    // Note: editConcentradoModal is created dynamically later, so no need to initialize here.

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
            concentrado: $('#new_concentrado').val(),
            etapa: $('#new_etapa').val(),
            costo: $('#new_costo').val(),
            vigencia: $('#new_vigencia').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar el alimento ${formData.concentrado} ?`,
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
                    url: 'process_configuracion_concentrado.php',
                    type: 'POST',
                    data: {
                        action: 'create',
                        concentrado: formData.concentrado,
                        etapa: formData.etapa,
                        costo: formData.costo,
                        vigencia: formData.vigencia
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        // Close the modal
                        newEntryModalInstance.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de concentrado ha sido guardado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Reload the page to show updated data
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', xhr, status, error);
                        console.log('Request data:', {
                            action: 'create',
                            concentrado: formData.concentrado,
                            etapa: formData.etapa,
                            costo: formData.costo,
                            vigencia: formData.vigencia
                        });
                        
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Error response:', response);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
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
    $('.edit-concentrado').click(function() {
        var id = $(this).data('id');
        var concentrado = $(this).data('concentrado');
        var etapa = $(this).data('etapa');
        var costo = $(this).data('costo');
        var vigencia = $(this).data('vigencia');

        console.log('Edit button clicked. Record ID captured:', id); // Debug log 1
        
        // Simple check if ID is missing before creating modal
        if (!id) {
             console.error('Attempting to edit a record with a missing ID.');
             Swal.fire({
                 title: 'Error',
                 text: 'No se puede editar este registro porque falta el ID.',
                 icon: 'error',
                 confirmButtonColor: '#dc3545'
             });
             return; // Stop execution if ID is missing
        }

        // Edit PLAN ALIMENTO CONCENTRADO Modal dialog for editing
        var modalHtml = `
        <div class="modal fade" id="editConcentradoModal" tabindex="-1" aria-labelledby="editConcentradoModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editConcentradoModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Concentrado
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editConcentradoForm">
                            <input type="hidden" id="edit_id" name="id" value="${id}">
                            <div class="mb-2">                                
                                    
                            <div class="mb-2">                            
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-syringe"></i>
                                        <label for="edit_concentrado" class="form-label">Alimento</label>                                    
                                        <select class="form-select" id="edit_concentrado" name="concentrado" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            // Fetch distinct names from the database
                                            $sql_names = "SELECT DISTINCT cac_concentrado_nombre FROM cac_concentrado ORDER BY cac_concentrado_nombre ASC";
                                            $stmt_names = $conn->prepare($sql_names);
                                            $stmt_names->execute();
                                            $names = $stmt_names->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($names as $name_row) {
                                                echo '<option value="' . htmlspecialchars($name_row['cac_concentrado_nombre']) . '">' . htmlspecialchars($name_row['cac_concentrado_nombre']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </span>                                    
                                </div>                                
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-syringe"></i>
                                        <label for="edit_etapa" class="form-label">Etapa</label>                                    
                                        <select class="form-select" id="edit_etapa" name="etapa" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $sql_etapas = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
                                            $stmt_etapas = $conn->prepare($sql_etapas);
                                            $stmt_etapas->execute();
                                            $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($etapas as $etapa_row) {
                                                echo '<option value="' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['cac_etapas_nombre']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-dollar-sign"></i>
                                        <label for="edit_costo" class="form-label">Costo ($)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_costo" name="costo" value="${costo}" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-days"></i>
                                        <label for="edit_vigencia" class="form-label">Vigencia (dias)</label>
                                        <input type="number" class="form-control" id="edit_vigencia" name="vigencia" value="${vigencia}" required>
                                    </span>
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
        
        // Set the selected values in the form after a short delay to ensure DOM is ready
        setTimeout(function() {
            $('#edit_concentrado').val(concentrado);
            $('#edit_etapa').val(etapa);
            $('#edit_costo').val(costo);
            $('#edit_vigencia').val(vigencia);
            
            // Debug log to verify values are being set
            console.log('Setting modal values:', {
                concentrado: concentrado,
                etapa: etapa,
                costo: costo,
                vigencia: vigencia
            });
            
            // Verify that the values were actually set
            console.log('Actual values set:', {
                concentrado: $('#edit_concentrado').val(),
                etapa: $('#edit_etapa').val(),
                costo: $('#edit_costo').val(),
                vigencia: $('#edit_vigencia').val()
            });
        }, 100);
        
        // Handle save button click
        $('#saveEditConcentrado').click(function() {
            // Create a form object to properly validate
            var form = document.getElementById('editConcentradoForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            var formData = {
                id: $('#edit_id').val(),
                concentrado: $('#edit_concentrado').val(),
                etapa: $('#edit_etapa').val(),
                costo: $('#edit_costo').val(),
                vigencia: $('#edit_vigencia').val()
            };
            
            console.log('Save changes clicked. Form Data being sent:', formData); // Debug log 2
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar la configuracion de concentrado?`,
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
                        url: 'process_configuracion_concentrado.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            concentrado: formData.concentrado,
                            etapa: formData.etapa,
                            costo: formData.costo,
                            vigencia: formData.vigencia
                        },
                        success: function(response) {
                            console.log('Update success response:', response);
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
                        },
                        error: function(xhr, status, error) {
                            console.error('Update AJAX error:', xhr, status, error);
                            console.log('Update request data:', {
                                action: 'update',
                                id: formData.id,
                                concentrado: formData.concentrado,
                                etapa: formData.etapa,
                                costo: formData.costo,
                                vigencia: formData.vigencia
                            });
                            
                            // Show error message
                            let errorMsg = 'Error al procesar la solicitud';
                            
                            try {
                                const response = JSON.parse(xhr.responseText);
                                console.log('Update error response:', response);
                                if (response.message) {
                                    errorMsg = response.message;
                                }
                            } catch (e) {
                                console.error('Error parsing update response:', e);
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
    $('.delete-concentrado').click(function() {
        var id = $(this).data('id');
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `¿Está seguro de que desea eliminar la configuracion de concentrado? Esta acción no se puede deshacer.`,
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
                    url: 'process_configuracion_concentrado.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        console.log('Delete success response:', response);
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
                        console.error('Delete AJAX error:', xhr, status, error);
                        console.log('Delete request data:', {
                            action: 'delete',
                            id: id
                        });
                        
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Delete error response:', response);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing delete response:', e);
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

    // Handle new register button click for animals without history
    $(document).on('click', '.register-new-concentrado-btn', function() { 
        // Get tagid from the button's data-tagid-prefill attribute
        var tagid = $(this).data('tagid-prefill'); 
        
        // Clear previous data in the modal
        $('#newConcentradoForm')[0].reset();
        $('#new_id').val(''); // Ensure ID is cleared
        
      
        
        // Show the new entry modal using the existing instance
        newEntryModalInstance.show(); 
    });
});
</script>
<!-- Poultry Daily Portion Calculator Section -->
<div class="container text-center mt-5">
    <h3 class="container mt-4 text-white">
    CALCULADORA RACION DIARIA CAMARONES Vs RETORNO INVERSION
    </h3>
    <p class="text-dark-50 text-center mb-4">Herramienta de asesoría financiera para determinar la inversión óptima en alimentación concentrada para camarones</p>
</div>

<div class="container mt-4">
    <div class="row">
        <!-- Calculator Form -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Parámetros de Cálculo</h5>
                </div>
                <div class="card-body">
                    <form id="poultryCalculatorForm">
                        <div class="mb-3">
                            <label for="tipo_ave" class="form-label">Tipo de Pez</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-feather"></i></span>
                                <select class="form-select" id="tipo_ave" name="tipo_ave" required>
                                    <option value="">Seleccionar tipo de pez</option>
                                    <option value="Camarones">🐓 Camarones</option>
                                    <option value="Tilapias">🥚 Tilapias</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="peso_inicial" class="form-label">Peso Inicial (kg)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-weight"></i></span>
                                <input type="number" step="0.01" class="form-control" id="peso_inicial" name="peso_inicial" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="precio_kg_inicial" class="form-label">Precio en pie inicial ($/kg)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" step="0.01" class="form-control" id="precio_kg_inicial" name="precio_kg_inicial" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="peso_final" class="form-label">Peso final (kg)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-weight"></i></span>
                                <input type="number" step="0.1" class="form-control" id="peso_final" name="peso_final" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="precio_kg_final" class="form-label">Precio en pie final ($/kg)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" step="0.1" class="form-control" id="precio_kg_final" name="precio_kg_final" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="duracion_dias" class="form-label">Periodo de Evaluación (días)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-days"></i></span>
                                <input type="number" class="form-control" id="duracion_dias" name="duracion_dias" placeholder="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fcr_ajustable" class="form-label">FCR (Factor de Conversión) - Rango: 1.4 - 2.8</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-exchange-alt"></i></span>
                                <input type="number" step="0.1" class="form-control" id="fcr_ajustable" name="fcr_ajustable" placeholder="1.8" min="1.4" max="2.8" value="1.8" required>
                                <button class="btn btn-outline-info" type="button" id="optimizeFcrBtn" title="Calcular FCR Óptimo">
                                    <i class="fas fa-magic"></i> Óptimo
                                </button>
                            </div>
                            <small class="form-text text-muted">FCR típico - Camarones: 1.4-2.2, Tilapias: 2.0-2.8. Menor FCR = más eficiente. Use "Óptimo" para calcular el FCR que maximiza ROI.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="costo_alimento_kg" class="form-label">Costo Alimento (kg/$)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                <input type="number" step="0.01" class="form-control" id="costo_alimento_kg" name="costo_alimento_kg" placeholder="0.10" required>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" id="calculateBtn">
                                <i class="fas fa-calculator me-2"></i>RETORNO INVERSION
                            </button>
                            <button type="button" class="btn btn-secondary" id="clearBtn">
                                <i class="fas fa-eraser me-2"></i>Limpiar Formulario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Display -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Resultados del Análisis</h5>
                </div>
                <div class="card-body" id="resultsContainer">
                    <div class="text-center text-muted" id="noResultsMessage">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <p>Complete el formulario y presione "Calcular ROI" para ver los resultados del análisis financiero.</p>
                    </div>
                    
                    <div id="calculationResults" style="display: none;">
                        <!-- Step-by-step calculation results will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Calculate button click handler
    $('#calculateBtn').click(function() {
        calculatePezROI();
    });
    
    // Clear button click handler
    $('#clearBtn').click(function() {
        $('#pezCalculatorForm')[0].reset();
        $('#fcr_ajustable').val('1.8'); // Reset FCR to default for poultry
        $('#calculationResults').hide();
        $('#noResultsMessage').show();
    });
    
    // Bird type change handler - Adjust FCR based on selected bird type
    $('#tipo_pez').change(function() {
        const tipoAve = $(this).val();
        const fcrInput = $('#fcr_ajustable');
        const tipoPez = $(this).val();
        
        if (tipoPez === 'Camarones') {
            // Broiler chickens: FCR 1.4-2.2, optimal around 1.7
            fcrInput.val('1.7');
            fcrInput.attr('min', '1.4');
            fcrInput.attr('max', '2.2');
            fcrInput.attr('placeholder', '1.7');
            
            // Update the label and help text
            $('label[for="fcr_ajustable"]').text('FCR (Factor de Conversión) - Rango: 1.4 - 2.2');
            $('#fcr_ajustable').siblings('small').text('FCR típico - Camarones: 1.4-2.2 (óptimo: 1.6-1.8). Menor FCR = más eficiente. Use "Óptimo" para calcular el FCR que maximiza ROI.');
            
        } else if (tipoPez === 'Tilapias') {
            // Tilapias: FCR 2.0-2.8, optimal around 2.3
            fcrInput.val('2.3');
            fcrInput.attr('min', '2.0');
            fcrInput.attr('max', '2.8');
            fcrInput.attr('placeholder', '2.3');
            
            // Update the label and help text
            $('label[for="fcr_ajustable"]').text('FCR (Factor de Conversión) - Rango: 2.0 - 2.8');
            $('#fcr_ajustable').siblings('small').text('FCR típico - Tilapias: 2.0-2.8 (óptimo: 2.2-2.5). Menor FCR = más eficiente. Use "Óptimo" para calcular el FCR que maximiza ROI.');
            
        } else {
            // Default/no selection
            fcrInput.val('1.8');
            fcrInput.attr('min', '1.4');
            fcrInput.attr('max', '2.8');
            fcrInput.attr('placeholder', '1.8');
            
            // Reset to general label
            $('label[for="fcr_ajustable"]').text('FCR (Factor de Conversión) - Rango: 1.4 - 2.8');
            $('#fcr_ajustable').siblings('small').text('FCR típico - Camarones: 1.4-2.2, Tilapias: 2.0-2.8. Menor FCR = más eficiente. Use "Óptimo" para calcular el FCR que maximiza ROI.');
        }
        
        // Trigger calculation if form is complete
        if (isFormComplete()) {
            calculatePezROI();
        }
    });
    
    // Optimize FCR button click handler
    $('#optimizeFcrBtn').click(function() {
        const tipoPez = $('#tipo_pez').val();
        const pesoInicial = parseFloat($('#peso_inicial').val()) || 0;
        const precioKgInicial = parseFloat($('#precio_kg_inicial').val()) || 0;
        const pesoFinal = parseFloat($('#peso_final').val()) || 0;
        const precioKgFinal = parseFloat($('#precio_kg_final').val()) || 0;
        const costoAlimentoKg = parseFloat($('#costo_alimento_kg').val()) || 0;
        
        // Check if bird type is selected
        if (!tipoPez) {
            Swal.fire({
                title: 'Seleccione Tipo de Pez',
                text: 'Debe seleccionar el tipo de pez antes de optimizar el FCR.',
                icon: 'warning',
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        
        // Check if we have enough data to optimize
        if (pesoInicial === 0 || pesoFinal === 0 || precioKgFinal === 0 || costoAlimentoKg === 0) {
            Swal.fire({
                title: 'Datos Insuficientes',
                text: 'Complete peso inicial, peso final, precio final y costo del alimento para calcular el FCR óptimo.',
                icon: 'warning',
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        
        const kgGanados = pesoFinal - pesoInicial;
        if (kgGanados <= 0) {
            Swal.fire({
                title: 'Error de Datos',
                text: 'El peso final debe ser mayor al peso inicial.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Set FCR ranges based on bird type
        let minFcr, maxFcr, bestFcr;
        if (tipoPez === 'Camarones') {
            minFcr = 1.4;
            maxFcr = 2.2;
            bestFcr = 2.2;
        } else if (tipoPez === 'Tilapias') {
            minFcr = 2.0;
            maxFcr = 2.8;
            bestFcr = 2.8;
        } else {
            minFcr = 1.4;
            maxFcr = 2.8;
            bestFcr = 2.8;
        }
        
        // Calculate optimal FCR (minimize cost while maximizing gain)
        // The optimal FCR is the one that maximizes ROI
        let bestRoi = -1000;
        
        for (let testFcr = minFcr; testFcr <= maxFcr; testFcr += 0.1) {
            const alimentoConsumido = kgGanados * testFcr;
            const costoTotalAlimento = alimentoConsumido * costoAlimentoKg;
            const costoTotalCompra = pesoInicial * precioKgInicial;
            const costoTotal = costoTotalCompra + costoTotalAlimento;
            const ingresoVenta = pesoFinal * precioKgFinal;
            const roi = costoTotal > 0 ? ((ingresoVenta - costoTotal) / costoTotal * 100) : -1000;
            
            if (roi > bestRoi) {
                bestRoi = roi;
                bestFcr = testFcr;
            }
        }
        
        $('#fcr_ajustable').val(bestFcr.toFixed(1));
        
        const birdTypeName = tipoPez === 'Camarones' ? 'Camarones' : 'Tilapias';
        
        Swal.fire({
            title: 'FCR Óptimo Calculado',
            text: `FCR óptimo para ${birdTypeName}: ${bestFcr.toFixed(1)} (ROI estimado: ${bestRoi.toFixed(2)}%)`,
            icon: 'success',
            confirmButtonColor: '#28a745'
        });
        
        // Trigger calculation if form is complete
        if (isFormComplete()) {
            calculatePezROI();
        }
    });
    
    // FCR input change handler
    $('#fcr_ajustable').on('input', function() {
        // Trigger calculation if form is complete
        if (isFormComplete()) {
            calculatePezROI();
        }
    });
    
    // Real-time calculation on input change
    $('#pezCalculatorForm input, #pezCalculatorForm select').on('input change', function() {
        if (isFormComplete()) {
            calculatePezROI();
        }
    });
    
    function isFormComplete() {
        let complete = true;
        
        // Check all required fields
        $('#pezCalculatorForm input[required], #pezCalculatorForm select[required]').each(function() {
            if ($(this).val() === '') {
                complete = false;
                return false;
            }
        });
        
        return complete;
    }
    
    function calculatePezROI() {
        // Validate form
        const form = document.getElementById('pezCalculatorForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get input values
        const tipoPez = $('#tipo_pez').val();
        const pesoInicial = parseFloat($('#peso_inicial').val()) || 0;
        const precioKgInicial = parseFloat($('#precio_kg_inicial').val()) || 0;
        const pesoFinal = parseFloat($('#peso_final').val()) || 0;
        const precioKgFinal = parseFloat($('#precio_kg_final').val()) || 0;
        const costoAlimentoKg = parseFloat($('#costo_alimento_kg').val()) || 0;
        const duracionDias = parseInt($('#duracion_dias').val()) || 0;
        
        // Get FCR for poultry (adjusted for type)
        let fcr = parseFloat($('#fcr_ajustable').val()) || 1.8;
        
        // Set FCR recommendations based on poultry type
        let fcrRecomendado = '';
        if (tipoPez === 'Camarones') {
            fcrRecomendado = 'Camarones: 1.4-2.2 (óptimo: 1.6-1.8)';
        } else if (tipoPez === 'Tilapias') {
            fcrRecomendado = 'Gallinas ponedoras: 2.0-2.8 (óptimo: 2.2-2.5)';
        }
        
        // For poultry, we assume 100% concentrate feed (no separate forraje calculation)
        const porcentajeConcentrado = 100;
        const porcentajeForraje = 0;
        
        // Calculate derived values
        const kgGanados = pesoFinal - pesoInicial;
        const gananciaDiaria = duracionDias > 0 ? (kgGanados / duracionDias) : 0;
        
        // Calculate food consumption using user-defined FCR
        const alimentoTotalConsumido = kgGanados * fcr;
        
        // For poultry, all feed is concentrate (no separate forraje)
        const forrajeConsumido = 0;
        const concentradoConsumido = alimentoTotalConsumido;
        
        // Calculate daily rations (this is the key result!)
        const racionDiariaTotal = duracionDias > 0 ? (alimentoTotalConsumido / duracionDias) : 0;
        const racionDiariaForraje = duracionDias > 0 ? (forrajeConsumido / duracionDias) : 0;
        const racionDiariaConcentrado = duracionDias > 0 ? (concentradoConsumido / duracionDias) : 0;
        
        // Calculate break-even point for feed cost
        const costoTotalCompra = pesoInicial * precioKgInicial;
        const ingresoVenta = pesoFinal * precioKgFinal;
        const margenDisponible = ingresoVenta - costoTotalCompra;
        const precioAlimentoEquilibrio = concentradoConsumido > 0 ? (margenDisponible / concentradoConsumido) : 0;
        
        // Perform financial calculations (all feed is concentrate for pigs)
        const costoTotalAlimento = concentradoConsumido * costoAlimentoKg;
        const costoTotal = costoTotalCompra + costoTotalAlimento;
        const roi = costoTotal > 0 ? ((ingresoVenta - costoTotal) / costoTotal * 100) : 0;
        const ganancia = ingresoVenta - costoTotal;
        
        // Format numbers for display
        const formatCurrency = (value) => '$' + value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const formatNumber = (value) => value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const formatPercent = (value) => value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '%';
        
        // Determine ROI status and color
        let roiStatus = '';
        let roiColor = '';
        if (roi > 20) {
            roiStatus = 'Excelente';
            roiColor = 'text-success';
        } else if (roi > 10) {
            roiStatus = 'Bueno';
            roiColor = 'text-info';
        } else if (roi > 0) {
            roiStatus = 'Aceptable';
            roiColor = 'text-warning';
        } else {
            roiStatus = 'Pérdida';
            roiColor = 'text-danger';
        }
        
        // Display results
        const resultsHtml = `
            <div class="calculation-steps">
                <h6 class="text-primary mb-3"><i class="fas fa-list-ol me-2"></i>Cálculos Paso a Paso:</h6>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-primary me-2">1</span>
                        <strong>Kilogramos Ganados</strong>
                    </div>
                    <div class="step-calculation">
                        <code>kg_ganados = peso_final - peso_inicial</code>
                        <div class="step-result">
                            ${formatNumber(pesoFinal)} kg - ${formatNumber(pesoInicial)} kg = <strong>${formatNumber(kgGanados)} kg</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-primary me-2">2</span>
                        <strong>Ganancia Diaria</strong>
                    </div>
                    <div class="step-calculation">
                        <code>ganancia_diaria = kg_ganados ÷ duración_días</code>
                        <div class="step-result">
                            ${formatNumber(kgGanados)} kg ÷ ${duracionDias} días = <strong>${formatNumber(gananciaDiaria)} kg/día</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-primary me-2">3</span>
                        <strong>Factor de Conversión Alimenticia (FCR)</strong>
                    </div>
                    <div class="step-calculation">
                        <code>FCR para ${tipoPez === 'Camarones' ? 'Camarones' : 'Tilapias'}: ${formatNumber(fcr)} kg alimento/kg ganancia</code>
                        <div class="step-result">
                            <strong>Tipo de Pez:</strong> ${tipoPez === 'Camarones' ? '🐓 Camarones' : '🥚 Tilapias'}<br>
                            <strong>Tipo de Alimentación:</strong> 100% Concentrado (típico para peces)
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-primary me-2">4</span>
                        <strong>Alimento Total Consumido</strong>
                    </div>
                    <div class="step-calculation">
                        <code>alimento_total = kg_ganados × FCR</code>
                        <div class="step-result">
                            ${formatNumber(kgGanados)} kg × ${formatNumber(fcr)} = <strong>${formatNumber(alimentoTotalConsumido)} kg</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-info me-2">5</span>
                        <strong>Consumo Total de Concentrado</strong>
                    </div>
                    <div class="step-calculation">
                        <code>concentrado_total = alimento_total (100% concentrado para peces)</code>
                        <div class="step-result">
                            <strong>Concentrado Total:</strong> ${formatNumber(concentradoConsumido)} kg
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-4 border border-success rounded p-3" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                    <div class="step-header">
                        <span class="badge bg-success me-2" style="font-size: 1.1em;">⭐</span>
                        <strong style="color: #155724; font-size: 1.2em;">RACIÓN DIARIA RECOMENDADA PARA ${tipoPez === 'Camarones' ? 'CAMARONES' : 'TILAPIAS'}</strong>
                    </div>
                    <div class="step-calculation" style="background-color: #f8fff9; border: 2px solid #28a745;">
                        <code style="color: #155724; font-weight: bold;">ración_diaria = alimento_total ÷ duración_días</code>
                        <div class="step-result text-center">
                            <span style="font-size: 2.0em; color: #155724; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
                                ${tipoPez === 'Camarones' ? '🐓' : '🥚'} ${formatNumber(racionDiariaTotal)} kg/día
                            </span>
                            <div style="font-size: 1.1em; color: #155724; margin-top: 10px;">
                                <strong>Concentrado para ${tipoPez === 'Camarones' ? 'Camarones' : 'Tilapias'}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small style="color: #155724; font-weight: 500;">
                            💡 Los peces requieren alimentación 100% con concentrado balanceado para un ${tipoPez === 'Camarones' ? 'crecimiento óptimo' : 'rendimiento productivo óptimo'}.
                        </small>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-info me-2">7</span>
                        <strong>Punto de Equilibrio - Precio Concentrado</strong>
                    </div>
                    <div class="step-calculation">
                        <code>precio_equilibrio = (ingreso_venta - costo_compra) ÷ concentrado_consumido</code>
                        <div class="step-result">
                            (${formatCurrency(ingresoVenta)} - ${formatCurrency(costoTotalCompra)}) ÷ ${formatNumber(concentradoConsumido)} kg = <strong class="${precioAlimentoEquilibrio > costoAlimentoKg ? 'text-success' : 'text-danger'}">${formatCurrency(precioAlimentoEquilibrio)}/kg</strong>
                        </div>
                        <small class="text-muted">
                            ${precioAlimentoEquilibrio > costoAlimentoKg ? 
                                '✅ El precio actual del concentrado está por debajo del punto de equilibrio' : 
                                '⚠️ El precio actual del concentrado supera el punto de equilibrio'}
                        </small>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-warning me-2">8</span>
                        <strong>Costo Total del Concentrado</strong>
                    </div>
                    <div class="step-calculation">
                        <code>costo_total_concentrado = concentrado_consumido × costo_concentrado_kg</code>
                        <div class="step-result">
                            ${formatNumber(concentradoConsumido)} kg × ${formatCurrency(costoAlimentoKg)}/kg = <strong>${formatCurrency(costoTotalAlimento)}</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-warning me-2">9</span>
                        <strong>Costo Total de Compra</strong>
                    </div>
                    <div class="step-calculation">
                        <code>costo_total_compra = peso_inicial × precio_kg_inicial</code>
                        <div class="step-result">
                            ${formatNumber(pesoInicial)} kg × ${formatCurrency(precioKgInicial)}/kg = <strong>${formatCurrency(costoTotalCompra)}</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-danger me-2">10</span>
                        <strong>Costo Total</strong>
                    </div>
                    <div class="step-calculation">
                        <code>costo_total = costo_total_compra + costo_total_concentrado</code>
                        <div class="step-result">
                            ${formatCurrency(costoTotalCompra)} + ${formatCurrency(costoTotalAlimento)} = <strong>${formatCurrency(costoTotal)}</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-3">
                    <div class="step-header">
                        <span class="badge bg-success me-2">11</span>
                        <strong>Ingreso por Venta</strong>
                    </div>
                    <div class="step-calculation">
                        <code>ingreso_venta = peso_final × precio_kg_final</code>
                        <div class="step-result">
                            ${formatNumber(pesoFinal)} kg × ${formatCurrency(precioKgFinal)}/kg = <strong>${formatCurrency(ingresoVenta)}</strong>
                        </div>
                    </div>
                </div>
                
                <div class="step-item mb-4">
                    <div class="step-header">
                        <span class="badge bg-info me-2">12</span>
                        <strong>ROI (Retorno de Inversión)</strong>
                    </div>
                    <div class="step-calculation">
                        <code>ROI = (ingreso_venta - costo_total) / costo_total × 100</code>
                        <div class="step-result">
                            (${formatCurrency(ingresoVenta)} - ${formatCurrency(costoTotal)}) / ${formatCurrency(costoTotal)} × 100 = <strong class="${roiColor}">${formatPercent(roi)}</strong>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="summary-section">
                    <!-- Destacar Raciones Diarias en el resumen -->
                    <div class="alert alert-success text-center mb-4" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 3px solid #28a745;">
                        <h4 class="alert-heading text-success mb-3">
                            <i class="fas fa-utensils me-2"></i>RACIÓN DIARIA PARA ${tipoPez === 'Camarones' ? 'CAMARONES' : 'TILAPIAS'}
                        </h4>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div style="font-size: 2.5em; color: #155724; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
                                    ${tipoPez === 'Camarones' ? '🐓' : '🥚'} ${formatNumber(racionDiariaTotal)} kg/día
                                </div>
                                <small style="color: #155724; font-weight: 600; font-size: 1.1em;">Concentrado Balanceado</small>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="text-success mb-3"><i class="fas fa-chart-pie me-2"></i>Resumen Financiero:</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-item">
                                <span class="summary-label">Inversión Total:</span>
                                <span class="summary-value text-danger">${formatCurrency(costoTotal)}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Ingreso por Venta:</span>
                                <span class="summary-value text-success">${formatCurrency(ingresoVenta)}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Ganancia/Pérdida:</span>
                                <span class="summary-value ${ganancia >= 0 ? 'text-success' : 'text-danger'}">${formatCurrency(ganancia)}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="roi-display text-center">
                                <div class="roi-value ${roiColor}" style="font-size: 2.5em; font-weight: bold;">
                                    ${formatPercent(roi)}
                                </div>
                                <div class="roi-status">
                                    <span class="badge ${roi > 0 ? 'bg-success' : 'bg-danger'} fs-6">${roiStatus}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert ${precioAlimentoEquilibrio > costoAlimentoKg ? 'alert-success' : 'alert-warning'} mb-2">
                            <h6 class="alert-heading">
                                <i class="fas fa-balance-scale me-1"></i>Análisis de Punto de Equilibrio
                            </h6>
                            <p class="mb-1">
                                <strong>Precio máximo alimento para rentabilidad:</strong> ${formatCurrency(precioAlimentoEquilibrio)}/kg
                            </p>
                            <p class="mb-1">
                                <strong>Precio actual del alimento:</strong> ${formatCurrency(costoAlimentoKg)}/kg
                            </p>
                            <p class="mb-0">
                                <strong>Margen de seguridad:</strong> 
                                <span class="${precioAlimentoEquilibrio > costoAlimentoKg ? 'text-success' : 'text-danger'}">
                                    ${formatCurrency(precioAlimentoEquilibrio - costoAlimentoKg)}/kg
                                    ${precioAlimentoEquilibrio > costoAlimentoKg ? '(Rentable)' : '(No Rentable)'}
                                </span>
                            </p>
                        </div>
                        
                        <div class="alert alert-info mb-2">
                            <h6 class="alert-heading">
                                <i class="fas fa-magic me-1"></i>Optimización FCR para ${tipoPez === 'Camarones' ? 'Camarones' : 'Tilapias'}
                            </h6>
                            <p class="mb-1">
                                <strong>FCR actual:</strong> ${formatNumber(fcr)} 
                                <small class="text-muted">(${
                                    tipoPez === 'Camarones' 
                                        ? (fcr <= 1.6 ? 'Excelente' : fcr <= 1.8 ? 'Bueno' : fcr <= 2.0 ? 'Aceptable' : 'Mejorable')
                                        : (fcr <= 2.2 ? 'Excelente' : fcr <= 2.5 ? 'Bueno' : fcr <= 2.8 ? 'Aceptable' : 'Mejorable')
                                })</small>
                            </p>
                            <p class="mb-1">
                                <strong>Rango recomendado:</strong> ${fcrRecomendado}
                            </p>
                            <p class="mb-0">
                                <small>💡 Use el botón "Óptimo" para calcular el FCR que maximiza el ROI con los precios actuales.</small>
                            </p>
                        </div>
                        
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            En peces, el concentrado representa el 100% de la alimentación y es el principal costo de producción.
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        $('#noResultsMessage').hide();
        $('#calculationResults').html(resultsHtml).show();
    }
});
</script>

<style>
.calculation-steps .step-item {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    margin-left: 15px;
}

.step-header {
    font-weight: 600;
    margin-bottom: 8px;
}

.step-calculation {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    font-family: 'Courier New', monospace;
}

.step-calculation code {
    background-color: #e9ecef;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 0.9em;
    display: block;
    margin-bottom: 8px;
}

.step-result {
    font-family: inherit;
    color: #495057;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
    border-bottom: 1px solid #dee2e6;
}

.summary-label {
    font-weight: 500;
}

.summary-value {
    font-weight: bold;
}

.roi-display {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 2px solid #dee2e6;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

/* Professional Calculator Buttons Styling */
#pezCalculatorForm .d-grid {
    gap: 12px !important;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    padding: 0 20px;
}

#calculateBtn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 12px;
    padding: 17px 40px;
    font-weight: 600;
    font-size: 1.1em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
}

#calculateBtn:hover {
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    transform: translateY(-2px);
}

#calculateBtn:active {
    transform: translateY(0px);
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
}

#calculateBtn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

#calculateBtn:hover::before {
    left: 100%;
}

#clearBtn {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    border-radius: 12px;
    padding: 15px 40px;
    font-weight: 500;
    font-size: 1em;
    color: white;
    box-shadow: 0 3px 12px rgba(108, 117, 125, 0.25);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
}

#clearBtn:hover {
    background: linear-gradient(135deg, #5a6268 0%, #343a40 100%);
    box-shadow: 0 5px 18px rgba(108, 117, 125, 0.35);
    transform: translateY(-1px);
    color: white;
}

#clearBtn:active {
    transform: translateY(0px);
    box-shadow: 0 2px 8px rgba(108, 117, 125, 0.25);
}

#clearBtn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    transition: left 0.5s;
}

#clearBtn:hover::before {
    left: 100%;
}

/* Button Icons Animation */
#calculateBtn i, #clearBtn i {
    transition: transform 0.3s ease;
}

#calculateBtn:hover i {
    transform: scale(1.1) rotate(5deg);
}

#clearBtn:hover i {
    transform: scale(1.1) rotate(-5deg);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #calculateBtn, #clearBtn {
        padding: 15px 20px;
        font-size: 1em;
    }
    
    #calculateBtn {
        font-size: 1.05em;
    }
}
</style>

</body>
</html>