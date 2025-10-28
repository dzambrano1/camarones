<?php
require_once './pdo_conexion.php';  

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Calculate and update biomasa for all peso records at page load
try {
    $updateBiomasaQuery = "
        UPDATE cah_peso p
        INNER JOIN camarones c ON p.cah_peso_tagid = c.tagid
        SET p.cah_peso_biomasa = CAST((p.cah_peso_promedio * c.poblacion_actual / 1000) AS DECIMAL(10,2))
        WHERE p.cah_peso_promedio > 0 AND c.poblacion_actual > 0
    ";
    $stmt = $conn->prepare($updateBiomasaQuery);
    $stmt->execute();
    
    // Log the number of records updated (optional)
    $updatedRows = $stmt->rowCount();
    if ($updatedRows > 0) {
        error_log("Updated biomasa for $updatedRows peso records");
    }
} catch (PDOException $e) {
    error_log("Error updating biomasa: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Camarones Register Peso</title>
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
  REGISTROS DE BIOMASA
  </h3>

  <!-- New Entry Modal -->
  <div class="modal fade" id="newPesoModal" tabindex="-1" aria-labelledby="newPesoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newPesoModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Peso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newPesoForm">
                    <div class="mb-3 text-start">
                        <label for="new_fecha" class="form-label text-start d-block">
                            <i class="fas fa-calendar me-2"></i>Fecha
                        </label>
                        <input type="date" class="form-control" id="new_fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="new_tagid" class="form-label text-start d-block">
                            <i class="fas fa-tag me-2"></i>Tag ID
                        </label>
                        <input type="text" class="form-control" id="new_tagid" name="tagid" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="new_peso_promedio" class="form-label text-start d-block">
                            <i class="fa-solid fa-weight me-2"></i>Peso Camaron Promedio (gramos)
                        </label>
                        <input type="number" step="0.001" class="form-control" id="new_peso_promedio" name="peso_promedio" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewPeso">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DataTable for peso records -->
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="pesoTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">Acciones</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Estanque</th>
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Peso promedio (gramos)</th>
                      <th class="text-center">Biomasa Estanque (Kg)</th>
                      <th class="text-center">Estatus</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      // Query to get all Animals and ALL their peso records (if any)
                        $pesoQuery = "SELECT
                                            a.id AS camarones_id,
                                            COALESCE(a.tagid, p.cah_peso_tagid) as tagid,
                                            a.nombre,
                                            a.poblacion_actual,
                                            p.cah_peso_fecha,
                                            p.id AS peso_record_id,
                                            p.cah_peso_promedio,
                                            p.cah_peso_precio,
                                            p.cah_peso_biomasa as biomasa,
                                            -- Flag to easily check if there's a peso record
                                            CASE WHEN p.id IS NOT NULL THEN 1 ELSE 0 END AS in_peso_history
                                        FROM
                                            cah_peso p
                                        LEFT JOIN
                                            camarones a ON p.cah_peso_tagid = a.tagid
                                        UNION ALL
                                        SELECT
                                            a.id AS camarones_id,
                                            a.tagid,
                                            a.nombre,
                                            a.poblacion_actual,
                                            NULL as cah_peso_fecha,
                                            NULL AS peso_record_id,
                                            NULL as cah_peso_promedio,
                                            NULL as cah_peso_precio,
                                            NULL as biomasa,
                                            0 AS in_peso_history
                                        FROM
                                            camarones a
                                        WHERE
                                            a.tagid NOT IN (SELECT DISTINCT cah_peso_tagid FROM cah_peso WHERE cah_peso_tagid IS NOT NULL)
                                        
                                        ORDER BY
                                            in_peso_history DESC, cah_peso_fecha DESC, nombre ASC";

                        $stmt = $conn->prepare($pesoQuery);
                        $stmt->execute();
                        $pesosData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // If no data, display a message
                      if (empty($pesosData)) {
                          echo "<tr><td colspan='7' class='text-center'>No hay animales registrados</td></tr>"; // Updated message
                      } else {
                          // Get vigencia setting for peso records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT ca_vencimiento_pesaje_animal FROM ca_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              
                              // Explicitly use PDO fetch method
                              $row = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row && isset($row['ca_vencimiento_pesaje_animal'])) {
                                  $vigencia = intval($row['ca_vencimiento_pesaje_animal']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                              // Continue with default value
                          }
                          
                          $currentDate = new DateTime();
                          
                          foreach ($pesosData as $row) {
                              echo "<tr>";
                              
                              // Actions column - Modified to always show Add button
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              
                              // Always show Add (+) button
                              echo '        <button class="btn btn-success btn-sm"
                                              data-bs-toggle="modal"
                                              data-bs-target="#newPesoModal"
                                              data-tagid-prefill="'.htmlspecialchars($row['tagid'] ?? '').'"
                                              title="Registrar Nuevo Peso">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              // Conditionally show Edit/Delete buttons if a record exists
                              if ($row['in_peso_history'] == 1) {
                                  echo '        <button class="btn btn-warning btn-sm edit-peso"
                                                  data-id="'.htmlspecialchars($row['peso_record_id'] ?? '').'"
                                                  data-tagid="'.htmlspecialchars($row['tagid'] ?? '').'"
                                                  data-peso="'.htmlspecialchars($row['cah_peso_promedio'] ?? '').'"
                                                  data-fecha="'.htmlspecialchars($row['cah_peso_fecha'] ?? '').'"
                                                  title="Editar Factores Biomasa">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                                  echo '        <button class="btn btn-danger btn-sm delete-peso"
                                                  data-id="'.htmlspecialchars($row['peso_record_id'] ?? '').'"
                                                  data-tagid="'.htmlspecialchars($row['tagid'] ?? '').'"
                                                  title="Eliminar Factores Biomasa">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              }
                              echo '    </div>';
                              echo '</td>';
                              
                              // Column 1: Fecha
                              echo "<td>" . htmlspecialchars($row['cah_peso_fecha'] ?? '') . "</td>";
                              // Column 2: Estanque
                              echo "<td>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                              // Column 3: Tag ID Animal
                              echo "<td>" . htmlspecialchars($row['tagid'] ?? 'N/A') . "</td>";

                              if ($row['in_peso_history'] == 1) {
                                  // Camarones has peso history
                                  // Column 4: Peso Promedio (Kg/pls)
                                  echo "<td>" . htmlspecialchars($row['cah_peso_promedio'] ?? 'N/A') . "</td>";
                                  // Column 5: Biomasa (Kg)
                                  echo "<td>" . htmlspecialchars($row['biomasa'] ?? 'N/A') . "</td>";
                                  // Calculate due date and determine status
                                  try {
                                      if (!empty($row['cah_peso_fecha'])) {
                                          $pesoDate = new DateTime($row['cah_peso_fecha']);
                                          $dueDate = clone $pesoDate;
                                          $dueDate->modify("+{$vigencia} days");

                                          if ($currentDate > $dueDate) {
                                              echo '<td class="text-center"><span class="badge bg-danger">VENCIDO</span></td>';
                                          } else {
                                              echo '<td class="text-center"><span class="badge bg-success">VIGENTE</span></td>';
                                          }
                                      } else {
                                           echo '<td class="text-center"><span class="badge bg-secondary">Sin Fecha</span></td>'; // Case where history exists but date is null
                                      }
                                  } catch (Exception $e) {
                                      error_log("Date error: " . $e->getMessage() . " for date: " . $row['cah_peso_fecha']);
                                      echo '<td class="text-center"><span class="badge bg-warning">ERROR FECHA</span></td>';
                                  }
                              } else {
                                  // Camarones has no peso history - output remaining columns (after Actions column)
                                  echo "<td><em>No Registrado</em></td>"; // Peso promedio (Kg/pls)
                                  echo "<td><em>No Registrado</em></td>"; // Biomasa (Kg)
                                  echo '<td class="text-center"><span class="badge bg-secondary">NO REGISTRADO</span></td>'; // Estatus
                              }
                              
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in peso table: " . $e->getMessage());
                      echo "<tr><td colspan='7' class='text-center'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Initialize DataTable for VH Peso -->
<script>
$(document).ready(function() {
    $('#pesoTable').DataTable({
        // Set initial page length
        pageLength: 10,
        
        // Configure length menu options
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Todos"]
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
                targets: [4, 5], // Peso Promedio, Biomasa columns
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data === 'N/A') return data;
                        const number = parseFloat(data);
                        if (!isNaN(number)) {
                            return number.toLocaleString('es-ES', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                targets: [1], // Fecha column
                type: 'date-eu',
                render: function(data, type, row) {
                     if (type === 'display') {
                        if (data === 'N/A') return data; // Pass through 'N/A'
                        // Date is already formatted DD/MM/YYYY in PHP
                        return data; 
                    }
                    // For sorting/filtering, convert DD/MM/YYYY back to YYYY-MM-DD
                    if (type === 'sort' || type === 'filter') {
                         if (data === 'N/A') return null; 
                         const parts = data.split('/');
                         if (parts.length === 3) {
                            return parts[2] + '-' + parts[1] + '-' + parts[0];
                         }
                         return null; // Fallback
                    }
                    return data;
                }
            },
            {
                targets: [6], // Status column
                orderable: true,
                searchable: true
            }
        ]
    });
});
</script>

<!-- JavaScript for Modal Pre-fill -->
<script>
$(document).ready(function() {
    var newPesoModalEl = document.getElementById('newPesoModal');
    if (newPesoModalEl) {
        var tagIdInput = newPesoModalEl.querySelector('#new_tagid');
        newPesoModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var tagIdToPrefill = button ? button.getAttribute('data-tagid-prefill') : null;
            
            if (tagIdInput && tagIdToPrefill) {
                tagIdInput.value = tagIdToPrefill;
            } else if (tagIdInput) {
                tagIdInput.value = ''; // Clear if no prefill info (e.g., modal opened via different button)
            }
            // Optionally reset other fields
            // newPesoModalEl.querySelector('#new_peso').value = '';
            // newPesoModalEl.querySelector('#new_precio').value = '';
            // newPesoModalEl.querySelector('#new_fecha').value = '<?php echo date('Y-m-d'); ?>';
        });
    }
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // Handle new entry form submission
    $('#saveNewPeso').click(function() {
        // Validate the form
        var form = document.getElementById('newPesoForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            peso_promedio: $('#new_peso_promedio').val(),
            fecha: $('#new_fecha').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar los datos de biomasa para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_weight.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        peso_promedio: formData.peso_promedio,
                        fecha: formData.fecha
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newPesoModal'));
                        modal.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de peso ha sido guardado correctamente',
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

    // Handle edit button click
    
    //  Edit Peso Modal
    $('.edit-peso').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var peso = $(this).data('peso');
        var fecha = $(this).data('fecha');
        
        var modalHtml = `
        <div class="modal fade" id="editPesoModal" tabindex="-1" aria-labelledby="editPesoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPesoModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Pesaje
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPesoForm">
                            <input type="hidden" id="edit_id" value="${id}">                            
                            <div class="mb-3">
                                <label for="edit_fecha" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Fecha
                                </label>
                                <input type="date" class="form-control" id="edit_fecha" value="${fecha}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_tagid" class="form-label">
                                    <i class="fas fa-tag me-2"></i>Tag ID
                                </label>
                                <input type="text" class="form-control" id="edit_tagid" value="${tagid}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit_peso_promedio" class="form-label">
                                    <i class="fa-solid fa-weight me-2"></i>Peso Camaron Promedio (gramos)
                                </label>
                                <input type="number" step="0.001" class="form-control" id="edit_peso_promedio" value="${peso}" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditPeso">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editPesoModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editPesoModal'));
        editModal.show();
        
        // Handle save button click
        $('#saveEditPeso').click(function() {
            var formData = {
                id: $('#edit_id').val(),
                tagid: $('#edit_tagid').val(),
                peso_promedio: $('#edit_peso_promedio').val(),
                fecha: $('#edit_fecha').val()
            };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar el registro de peso para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_weight.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            tagid: formData.tagid,
                            peso_promedio: formData.peso_promedio,
                            fecha: formData.fecha
                        },
                        success: function(response) {
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
    $('.delete-peso').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        
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
                    url: 'process_weight.php',
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

<!-- Weight Line Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Evolución de Peso</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <select id="animalFilter" class="form-select">
                        <option value="all">Todos los Estanques</option>
                        <!-- Animal options will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="dataRangeFilter" class="form-select">
                        <option value="20">Últimos 20 registros</option>
                        <option value="50">Últimos 50 registros</option>
                        <option value="100">Últimos 100 registros</option>
                        <option value="all">Todos los registros</option>
                    </select>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height:50vh; width:100%">
                <canvas id="weightChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Weight Line Chart -->
<script>
$(document).ready(function() {
    let allWeightData = [];
    let weightChart = null;
    
    // Fetch weight data and create the chart
    $.ajax({
        url: 'get_weight_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            allWeightData = data;
            populateAnimalFilter(data);
            createWeightChart(data);
            
            // Add event listeners for filters
            $('#animalFilter, #dataRangeFilter').on('change', function() {
                updateChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching weight data:', error);
        }
    });
    
    // --- Linear Regression Function ---
    function linearRegression(x, y) {
        const n = x.length;
        if (n < 2) {
            // Not enough data points for regression
            return { slope: 0, intercept: 0, r2: 0 };
        }

        let sumX = 0;
        let sumY = 0;
        let sumXY = 0;
        let sumXX = 0;
        // Removed sumYY calculation as it's only needed for R-squared

        for (let i = 0; i < n; i++) {
            sumX += x[i];
            sumY += y[i];
            sumXY += x[i] * y[i];
            sumXX += x[i] * x[i];
        }

        const denominator = (n * sumXX - sumX * sumX);
        if (denominator === 0) {
            // Avoid division by zero (vertical line or single x-value)
            return { slope: 0, intercept: sumY / n, r2: 0 }; // Return average Y as intercept
        }

        const slope = (n * sumXY - sumX * sumY) / denominator;
        const intercept = (sumY - slope * sumX) / n;

        // R-squared calculation (optional, can add later if needed)
        // let ssr = 0;
        // let sst = 0;
        // const meanY = sumY / n;
        // for (let i = 0; i < n; i++) {
        //     const predictedY = slope * x[i] + intercept;
        //     ssr += (predictedY - meanY) ** 2;
        //     sst += (y[i] - meanY) ** 2;
        // }
        // const r2 = (sst === 0) ? 1 : ssr / sst; // Handle case where all Y are the same

        return { slope: slope, intercept: intercept }; // Simplified return
    }
    // ---------------------------------
    
    function populateAnimalFilter(data) {
        // Get unique animals from the data
        const animals = [];
        const uniqueTagIds = new Set();
        
        data.forEach(item => {
            if (item.tagid && !uniqueTagIds.has(item.tagid)) {
                uniqueTagIds.add(item.tagid);
                animals.push({
                    tagid: item.tagid,
                    nombre: item.animal_nombre || 'Sin nombre'
                });
            }
        });
        
        // Sort animals by name
        animals.sort((a, b) => a.nombre.localeCompare(b.nombre));
        
        // Add options to the dropdown
        const animalFilter = $('#animalFilter');
        animals.forEach(animal => {
            animalFilter.append(`<option value="${animal.tagid}">${animal.nombre} (${animal.tagid})</option>`);
        });
    }
    
    function updateChart() {
        const selectedAnimal = $('#animalFilter').val();
        const selectedRange = $('#dataRangeFilter').val();
        
        let filteredData = [...allWeightData];
        
        // Filter by animal if not "all"
        if (selectedAnimal !== 'all') {
            filteredData = filteredData.filter(item => item.tagid === selectedAnimal);
        }
        
        // Sort data by date
        filteredData.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
        
        // Apply range filter
        if (selectedRange !== 'all' && filteredData.length > parseInt(selectedRange)) {
            filteredData = filteredData.slice(filteredData.length - parseInt(selectedRange));
        }
        
        // Update chart with filtered data
        updateChartData(filteredData);
    }
    
    function updateChartData(data) {
        if (weightChart) {
            weightChart.destroy();
        }
        createWeightChart(data);
    }
    
    function createWeightChart(data) {
        var ctx = document.getElementById('weightChart').getContext('2d');
        
        // Format the data for the main chart labels and values
        var labels = data.map(function(item) {
            // Format the date for display
            var date = new Date(item.timestamp_fecha * 1000); // Use timestamp
            return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            // Keep original formatting code as fallback if needed
            // var parts = item.fecha.split('-');
            // if (parts.length === 3) {
            //     return parts[2] + '/' + parts[1] + '/' + parts[0];
            // }
            // return item.fecha;
        });
        
        var weights = data.map(function(item) {
            return parseFloat(item.peso) || 0; // peso is already in grams
        });

        // --- Trendline Calculation ---
        var xValues = data.map(item => item.timestamp_fecha); // Numeric timestamps for x
        var yValues = weights; // Numeric weights for y
        
        // Normalize X values to avoid numerical issues with large timestamps
        const minX = Math.min(...xValues);
        const normalizedXValues = xValues.map(x => x - minX);
        
        const regression = linearRegression(normalizedXValues, yValues);
        const trendlineYValues = normalizedXValues.map(x => regression.slope * x + regression.intercept);
        
        // --- Calculate Average Monthly Slope ---
        const secondsPerDay = 24 * 60 * 60;
        const approxDaysPerMonth = 365.25 / 12;
        const secondsPerMonth = secondsPerDay * approxDaysPerMonth;
        const monthlySlopeGrams = regression.slope * secondsPerMonth; // Already in grams since weights are in grams
        
        // Debug logging
        console.log('=== REGRESSION DEBUG ===');
        console.log('Original X Values (timestamps):', xValues);
        console.log('Normalized X Values:', normalizedXValues);
        console.log('Y Values (weights in grams):', yValues);
        console.log('Regression slope:', regression.slope);
        console.log('Regression intercept:', regression.intercept);
        console.log('Monthly slope (grams/month):', monthlySlopeGrams);
        console.log('Trendline Y Values:', trendlineYValues);
        console.log('Min trendline value:', Math.min(...trendlineYValues));
        console.log('Max trendline value:', Math.max(...trendlineYValues));
        console.log('Data points:', data.length);
        // ------------------------------------
        
        // Destroy existing chart instance if it exists
        if (weightChart) {
            weightChart.destroy();
        }
        
        weightChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                { // Original Weight Data
                    label: 'Peso (g)',
                    data: weights,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3
                },
                { // Trendline Data
                    label: 'Tendencia Lineal',
                    data: trendlineYValues, // Y values calculated from regression
                    borderColor: 'rgba(255, 99, 132, 0.8)', // Reddish color for trend
                    borderWidth: 2,
                    borderDash: [5, 5], // Dashed line style
                    type: 'line', // Explicitly line type
                    pointRadius: 4, // Changed from 0 to show points
                    pointBackgroundColor: 'rgba(255, 99, 132, 0.8)', // Match line color
                    pointBorderColor: '#fff', // White border for points
                    pointHoverRadius: 6, // Slightly larger on hover
                    fill: false,
                    tension: 0, // Straight line
                    yAxisID: 'y1' // Use secondary Y-axis
                }
            ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Peso (g)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(40, 167, 69, 1)' // Match peso data color
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 1
                                }) + ' g';
                            },
                            color: 'rgba(40, 167, 69, 1)' // Match peso data color
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Tendencia (g)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(255, 99, 132, 0.8)' // Match trendline color
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 1
                                }) + ' g';
                            },
                            color: 'rgba(255, 99, 132, 0.8)' // Match trendline color
                        },
                        // Grid lines only for primary axis to avoid clutter
                        grid: {
                            drawOnChartArea: false
                        },
                        // Ensure independent scaling
                        min: function(context) {
                            const trendData = context.chart.data.datasets[1].data;
                            if (trendData && trendData.length > 0) {
                                return Math.min(...trendData) * 0.95; // 5% padding below
                            }
                            return undefined;
                        },
                        max: function(context) {
                            const trendData = context.chart.data.datasets[1].data;
                            if (trendData && trendData.length > 0) {
                                return Math.max(...trendData) * 1.05; // 5% padding above
                            }
                            return undefined;
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var index = context.dataIndex;
                                var datasetIndex = context.datasetIndex;
                                var dataPoint = data[index]; // Original data point info
                                var value = context.parsed.y;

                                let tooltipText = [];

                                // Check which dataset is being hovered
                                if (datasetIndex === 0) { // Original weight data
                                    tooltipText.push('Peso: ' + value.toLocaleString('es-ES', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 1
                                    }) + ' g');
                                    if (dataPoint && dataPoint.animal_nombre) {
                                        tooltipText.unshift('Animal: ' + dataPoint.animal_nombre);
                                    }
                                } else if (datasetIndex === 1) { // Trendline data
                                    tooltipText.push('Tendencia: ' + value.toLocaleString('es-ES', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 1
                                    }) + ' g');
                                    // Optionally add animal name here too if hovering over the trendline point makes sense
                                    // if (dataPoint && dataPoint.animal_nombre && $('#animalFilter').val() !== 'all') {
                                    //     tooltipText.unshift('Animal: ' + dataPoint.animal_nombre); 
                                    // }
                                } else {
                                    // Fallback for any other datasets
                                    tooltipText.push(context.dataset.label + ': ' + value.toLocaleString('es-ES'));
                                }

                                return tooltipText;
                            },
                            // Optional: Customize title if needed, e.g., show date
                            title: function(tooltipItems) {
                                // Assuming labels are formatted dates
                                return 'Fecha: ' + tooltipItems[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            const selectedAnimal = $('#animalFilter').val();
                            let baseTitle = 'Evolución de Peso';
                            if (selectedAnimal !== 'all') {
                                const animalName = $('#animalFilter option:selected').text();
                                baseTitle = 'Evolución de Peso - ' + animalName;
                            }
                            
                            // Append monthly slope if calculated and meaningful
                            if (Math.abs(monthlySlopeGrams) > 1 && data.length > 1) { 
                                // Use appropriate decimal places for gram values
                                const absSlope = Math.abs(monthlySlopeGrams);
                                let decimals = 1;
                                if (absSlope >= 100) decimals = 0;
                                else if (absSlope >= 10) decimals = 1;
                                
                                const formattedSlope = monthlySlopeGrams.toLocaleString('es-ES', {
                                    minimumFractionDigits: decimals,
                                    maximumFractionDigits: decimals,
                                    signDisplay: 'always' // Show + or -
                                });
                                return baseTitle + ` | Variación Mensual Prom.: ${formattedSlope} g`;
                            }
                            
                            return baseTitle; // Return base title if no slope
                        },
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    }
});
</script>