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
<title>Camarones Register Decesos</title>
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
  REGISTROS DE DECESOS
  </h3>
    
  <!-- New Deceso Entry Modal -->
  <div class="modal fade" id="newDecesoModal" tabindex="-1" aria-labelledby="newDecesoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newDecesoModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Deceso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newDecesoForm">
                <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                                <label for="new_fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="new_fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-tag"></i>
                                <label for="new_tagid" class="form-label">Tag ID</label>
                                <input type="text" class="form-control" id="new_tagid" name="tagid" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-skull-crossbones"></i>
                                <label for="new_causa" class="form-label">Causa</label>
                                <input type="text" class="form-control" id="new_causa" name="causa" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-dollar-sign"></i>
                                <label for="new_precio" class="form-label">Precio Unitario</label>
                                <input type="number" step="0.01" class="form-control" id="new_precio" name="precio" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-weight-scale"></i>
                                <label for="new_peso" class="form-label">Peso Unitario</label>
                                <input type="number" step="0.1" class="form-control" id="new_peso" name="peso" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-feather-alt"></i>
                                <label for="new_cantidad" class="form-label">Camarones Muertos</label>
                                <input type="number" class="form-control" id="new_cantidad" name="cantidad" required>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewDeceso">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
  <!-- DataTable for Camarones Muertos (Estatus = 'Muerto') -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="decesoTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">Imagen</th>
                      <th class="text-center">Acciones</th>
                      <th class="text-center">Estanque</th>                      
                      <th class="text-center">Fecha</th>                  
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Causa</th>
                      <th class="text-center">Precio ($/Kg)</th>
                      <th class="text-center">Peso (Kg)</th>
                      <th class="text-center">Camarones Muertos</th>
                      <th class="text-center">Población</th>
                  </tr>
              </thead>
              <tbody>
                  <?php                    
                    $decesoQuery = "SELECT 
                                d.id,
                                d.cah_decesos_tagid as tagid,
                                d.cah_decesos_fecha as deceso_fecha,
                                d.cah_decesos_causa as deceso_causa,
                                d.cah_decesos_precio as deceso_precio,
                                d.cah_decesos_peso as deceso_peso,
                                d.cah_decesos_cantidad as deceso_cantidad,
                                d.cah_decesos_supervivencia as deceso_supervivencia,
                                c.nombre,
                                c.image,
                                c.poblacion
                            FROM cah_decesos d
                            INNER JOIN camarones c ON d.cah_decesos_tagid = c.tagid
                            ORDER BY d.cah_decesos_fecha DESC";
                              
                    $stmt = $conn->prepare($decesoQuery);
                    $stmt->execute();
                    $decesoData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // If no data, display a message with exactly 6 columns (no colspan)
                    if (empty($decesoData)) {
                        echo "<tr>";
                        echo "<td class='text-center'>-</td>"; // Imagen
                        echo "<td class='text-center'>-</td>"; // Acciones
                        echo "<td class='text-center'><em>No hay registros de decesos disponibles</em></td>"; // Nombre
                        echo "<td class='text-center'>-</td>"; // Fecha
                        echo "<td class='text-center'>-</td>"; // Tag ID
                        echo "<td class='text-center'>-</td>"; // Causa
                        echo "<td class='text-center'>-</td>"; // Precio Unitario
                        echo "<td class='text-center'>-</td>"; // Peso Unitario
                        echo "<td class='text-center'>-</td>"; // Camarones Muertos
                        echo "<td class='text-center'>-</td>"; // Población
                        echo "</tr>";
                    } else {
                        foreach ($decesoData as $row) {
                            echo "<tr>";
                            
                            // Add image column as the first column
                            echo '<td class="text-center">';
                            // Check si el estanque tiene una imagen
                            if (!empty($row['image'])) {
                                echo '<img src="' . htmlspecialchars($row['image']) . '" alt="Imagen del estanque" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                            } else {
                                echo '<img src="images/camarones-poblacion.png" alt="Imagen por defecto" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                            }
                            echo '</td>';
                            
                            // Add action buttons (edit and delete)
                            echo '<td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm edit-deceso" 
                                        data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                        data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                        data-causa="' . htmlspecialchars($row['deceso_causa'] ?? '') . '"
                                        data-fecha="' . htmlspecialchars($row['deceso_fecha'] ?? '') . '"
                                        data-precio="' . htmlspecialchars($row['deceso_precio'] ?? '') . '"
                                        data-peso="' . htmlspecialchars($row['deceso_peso'] ?? '') . '"
                                        data-cantidad="' . htmlspecialchars($row['deceso_cantidad'] ?? '') . '"
                                        data-supervivencia="' . htmlspecialchars($row['deceso_supervivencia'] ?? '') . '"
                                        data-poblacion="' . htmlspecialchars($row['poblacion'] ?? '') . '">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-dark btn-sm add-deceso" 
                                        data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                        data-poblacion="' . htmlspecialchars($row['poblacion'] ?? '') . '"
                                        title="Agregar más decesos">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-deceso" 
                                        data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                        data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>';
                            
                            echo "<td class='text-center'>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['deceso_fecha'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['tagid'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['deceso_causa'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['deceso_precio'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['deceso_peso'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['deceso_cantidad'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['poblacion'] ?? '') . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['deceso_supervivencia'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                    }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Available for Death Registration DataTable -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-skull-crossbones me-2"></i>Estanques Sin Registros Decesos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="availableDecesoTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Imagen</th>
                                    <th class="text-center">Acción</th>
                                    <th class="text-center">Estatus</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Tag ID</th>
                                    <th class="text-center">Población</th>
                                    <th class="text-center">Etapa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    // Query para obtener los estanques disponibles para registro de decesos (estatus != 'Muerto')
                                    $availableDecesoQuery = "SELECT 
                                                        id,
                                                        tagid,
                                                        nombre,
                                                        estatus,
                                                        image,
                                                        poblacion,
                                                        etapa
                                                    FROM camarones 
                                                    WHERE estatus != 'Vendido'
                                                      AND poblacion > 0
                                                    ORDER BY tagid ASC";
                                                    
                                    $stmt = $conn->prepare($availableDecesoQuery);  
                                    $stmt->execute();
                                    $availableDecesoData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    // If no data, display a message with exactly 8 columns (no colspan)
                                    if (empty($availableDecesoData)) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>-</td>"; // Imagen
                                        echo "<td class='text-center'>-</td>"; // Acción
                                        echo "<td class='text-center'>-</td>"; // Estatus
                                        echo "<td class='text-center'><em>No hay Estanques para registro decesos</em></td>"; // Nombre
                                        echo "<td class='text-center'>-</td>"; // Tag ID
                                        echo "<td class='text-center'>-</td>"; // Población
                                        echo "<td class='text-center'>-</td>"; // Etapa
                                        echo "</tr>";
                                    } else {
                                        foreach ($availableDecesoData as $row) {
                                            echo "<tr>";
                                            echo '<td class="text-center">';
                                            // Check si el estanque tiene una imagen
                                            if (!empty($row['image'])) {
                                                echo '<img src="' . htmlspecialchars($row['image']) . '" alt="Imagen del Estanque" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                                            } else {
                                                echo '<img src="images/camarones-poblacion.png" alt="Imagen por defecto" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                                            }
                                            echo '</td>';
                                            
                                            // Add death registration action button
                                            echo '<td class="text-center">';
                                            echo '<button class="btn btn-dark btn-sm register-dead" 
                                                    data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                                    title="Registrar Deceso">
                                                    <i class="fas fa-plus"></i>
                                                </button>';
                                            echo '</td>';

                                            echo "<td class='text-center'>" . htmlspecialchars($row['estatus'] ?? '') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['tagid'] ?? '') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['poblacion'] ?? '0') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['etapa'] ?? '') . "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error in available deceso table: " . $e->getMessage());
                                    echo "<tr>";
                                    echo "<td class='text-center'>-</td>"; // Imagen
                                    echo "<td class='text-center'>-</td>"; // Acción
                                    echo "<td class='text-center'>-</td>"; // Estatus
                                    echo "<td class='text-center'><em>Error al cargar los datos</em></td>"; // Nombre
                                    echo "<td class='text-center'>-</td>"; // Tag ID
                                    echo "<td class='text-center'>-</td>"; // Población
                                    echo "<td class='text-center'>-</td>"; // Etapa
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Inicializacion de la tabla de Camarones Muertos -->
<script>
$(document).ready(function() {
    $('#decesoTable').DataTable({
        // Set initial page length
        pageLength: 10,
        
        // Configure length menu options
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Todos"]
        ],
        
        // Order by fecha (date) column descending
        order: [[3, 'desc']],
        
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
                targets: [3], // Fecha column
                render: function(data, type, row) {
                    if (type === 'display') {
                        // Parse the date parts manually to avoid timezone issues
                        if (data) {
                            // Split the date string (format: YYYY-MM-DD)
                            var parts = data.split('-');
                            // Create date string in local format (DD/MM/YYYY)
                            if (parts.length === 3) {
                                return parts[2] + '/' + parts[1] + '/' + parts[0];
                            }
                        }
                        return data; // Return original if parsing fails
                    }
                    return data;
                }
            },
            {
                targets: [6], // Precio Unitario column - currency formatting
                render: function(data, type, row) {
                    if (type === 'display') {
                        const value = parseFloat(data);
                        if (isNaN(value) || value === 0) {
                            return '$0.00';
                        }
                        return '$' + value.toLocaleString('es-ES', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    return data;
                }
            },
            {
                targets: [7], // Peso Unitario column - weight formatting
                render: function(data, type, row) {
                    if (type === 'display') {
                        const value = parseFloat(data);
                        if (isNaN(value) || value === 0) {
                            return '0.00 kg';
                        }
                        return value.toLocaleString('es-ES', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' kg';
                    }
                    return data;
                }
            },
            {
                targets: [8], // Camarones Muertos column - number formatting
                render: function(data, type, row) {
                    if (type === 'display') {
                        const value = parseInt(data);
                        if (isNaN(value) || value === 0) {
                            return '0';
                        }
                        return value.toLocaleString('es-ES');
                    }
                    return data;
                }
            },
            {
                targets: [9], // Población column - number formatting
                render: function(data, type, row) {
                    if (type === 'display') {
                        return parseInt(data || 0).toLocaleString('es-ES');
                    }
                    return data;
                }
            },
            {
                targets: [1], // Actions column
                orderable: false,
                searchable: false,
                width: '120px'
            }
        ]
    });

    // Inicializacion de tabla de Estanques sin registros de muertes
    $('#availableDecesoTable').DataTable({
        // Set initial page length
        pageLength: 25,
        
        // Configure length menu options
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
        ],
        
        // Order by tagid ascending
        order: [[4, 'asc']], // Tag ID column
        
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
                targets: [0], // Imagen column
                orderable: false,
                searchable: false
            },
            {
                targets: [1], // Actions column
                orderable: false,
                searchable: false
            },
            {
                targets: [5], // Población column - number formatting
                render: function(data, type, row) {
                    if (type === 'display') {
                        return parseInt(data || 0).toLocaleString('es-ES');
                    }
                    return data;
                }
            }
        ]
    });
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // Handle new entry form submission
    $('#saveNewDeceso').click(function() {
        // Validate the form
        var form = document.getElementById('newDecesoForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            causa: $('#new_causa').val(),
            fecha: $('#new_fecha').val(),
            precio: $('#new_precio').val(),
            peso: $('#new_peso').val(),
            cantidad: $('#new_cantidad').val()
        };
        
        // Get current population for validation
        var currentPopulation = parseInt($('#newDecesoModal').data('current-population')) || 0;
        var requestedDeaths = parseInt(formData.cantidad) || 0;
        
        // Check if requested deaths exceed available population
        if (requestedDeaths > currentPopulation) {
            Swal.fire({
                title: 'Error de validación',
                text: `No puede registrar ${requestedDeaths} decesos. La población disponible es ${currentPopulation}.`,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar ${requestedDeaths} decesos para el estanque con ID ${formData.tagid}? Población disponible: ${currentPopulation}.`,
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
                
                // Send AJAX request to insert the death record
                $.ajax({
                    url: 'process_deceso.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        causa: formData.causa,
                        fecha: formData.fecha,
                        precio: formData.precio,
                        peso: formData.peso,
                        cantidad: formData.cantidad
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newDecesoModal'));
                        if (modal) {
                            modal.hide();
                        }
                        
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: '¡Registro exitoso!',
                                text: 'El registro de deceso ha sido guardado correctamente',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to show updated data
                                location.reload();
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Ha ocurrido un error al registrar el deceso',
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

    // Add handler for register-dead button
    $('.register-dead').click(function() {
        var tagid = $(this).data('tagid');
        
        // Populate the tagid field in the newDecesoModal
        $('#new_tagid').val(tagid);
        
        // Show the modal
        var newDecesoModal = new bootstrap.Modal(document.getElementById('newDecesoModal'));
        newDecesoModal.show();
    });

    // Add handler for add-deceso button (add more deaths to existing record)
    $(document).on('click', '.add-deceso', function() {
        var tagid = $(this).data('tagid');
        var poblacion = $(this).data('poblacion');
        
        // Populate the tagid field in the newDecesoModal
        $('#new_tagid').val(tagid);
        
        // Store the current population for validation
        $('#newDecesoModal').data('current-population', poblacion);
        
        // Show the modal
        var newDecesoModal = new bootstrap.Modal(document.getElementById('newDecesoModal'));
        newDecesoModal.show();
    });

    // Handle edit button click
    $('.edit-deceso').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var causa = $(this).data('causa');
        var fecha = $(this).data('fecha');
        var precio = $(this).data('precio');
        var peso = $(this).data('peso');
        var cantidad = $(this).data('cantidad');
        var poblacion = $(this).data('poblacion'); // Get the new population column
        
        // Edit Deceso Modal dialog for editing

        var modalHtml = `
        <div class="modal fade" id="editDecesoModal" tabindex="-1" aria-labelledby="editDecesoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDecesoModalLabel">
                            <i class="fas fa-edit me-2"></i>Editar Deceso
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDecesoForm">
                            <div class="mb-2">                                
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                            <label for="edit_fecha" class="form-label">Fecha</label>
                                            <input type="date" class="form-control" id="edit_fecha" value="${fecha}" required>
                                        </span>
                                    </div>
                                </div>                            
                            <div class="mb-2">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-tag"></i>
                                        <label for="edit_tagid" class="form-label"> Tag ID </label>
                                        <input type="text" class="form-control" id="edit_tagid" value="${tagid}" readonly>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="mb-2">                            
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-skull-crossbones"></i>
                                        <label for="edit_causa" class="form-label">Causa</label>                                    
                                        <input type="text" class="form-control" id="edit_causa" value="${causa}" required>
                                    </span>                                    
                                </div>
                            </div>                                                 
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-dollar-sign"></i>
                                        <label for="edit_precio" class="form-label">Precio Unitario</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_precio" value="${precio}" step="0.01" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-weight"></i>
                                        <label for="edit_peso" class="form-label">Peso Unitario</label>
                                        <input type="number" step="0.1" class="form-control" id="edit_peso" value="${peso}" min="0.01" step="0.01" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-feather-alt"></i>
                                        <label for="edit_cantidad" class="form-label">Camarones Muertos</label>
                                        <input type="number" class="form-control" id="edit_cantidad" value="${cantidad}" min="1" step="1" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-users"></i>
                                        <label for="edit_poblacion" class="form-label">Población</label>
                                        <input type="number" class="form-control" id="edit_poblacion" value="${poblacion}" min="1" step="1" required>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditDeceso">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editDecesoModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editDecesoModal'));
        editModal.show();
        
        // Handle save button click
        $('#saveEditDeceso').click(function() {
            var formData = {
                id: id, // Pass the ID for the update action
                tagid: $('#edit_tagid').val(),
                causa: $('#edit_causa').val(),
                fecha: $('#edit_fecha').val(),
                precio: $('#edit_precio').val(),
                peso: $('#edit_peso').val(),
                cantidad: $('#edit_cantidad').val(),
                poblacion: $('#edit_poblacion').val() // Get the new population value
            };
            
            // Validate that deaths don't exceed population
            var requestedDeaths = parseInt(formData.cantidad) || 0;
            var newPopulation = parseInt(formData.poblacion) || 0;
            
            if (requestedDeaths > newPopulation) {
                Swal.fire({
                    title: 'Error de validación',
                    text: `No puede registrar ${requestedDeaths} decesos. La población disponible es ${newPopulation}.`,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar la información del deceso en el estanque con ID ${formData.tagid}?`,
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
                    
                    // Send AJAX request to update the death record
                    $.ajax({
                        url: 'process_deceso.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id, // Pass the ID for the update action
                            tagid: formData.tagid,
                            causa: formData.causa,
                            fecha: formData.fecha,
                            precio: formData.precio,
                            peso: formData.peso,
                            cantidad: formData.cantidad,
                            poblacion: formData.poblacion // Pass the new population value
                        },
                        success: function(response) {
                            // Close the modal
                            editModal.hide();
                            
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    title: '¡Actualización exitosa!',
                                    text: `La información del deceso para el estanque con ID ${formData.tagid} ha sido actualizada correctamente`,
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Reload the page to show updated data
                                    location.reload();
                                });
                            } else {
                                // Show error message
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Ha ocurrido un error al actualizar la información',
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
    $('.delete-deceso').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro de deceso?',
            text: `¿Está seguro de que desea eliminar el registro de deceso para el estanque con ID ${tagid}? El estatus de los camarones volverá a "Activo".`,
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

                // Send AJAX request to delete the death record
                $.ajax({
                    url: 'process_deceso.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: `El registro de deceso para el estanque ID ${tagid} ha sido eliminado correctamente`,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to show updated data
                                location.reload();
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Ha ocurrido un error al eliminar el registro',
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
</script>

<!-- Deceso Line Chart Section -->

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Evolución de Decesos</h5>
        </div>
        <div class="card-body">
            <!-- Using data directly from camarones table instead of bh_deceso -->
            <div class="row mb-4">
                <div class="col-md-4"></div>
                <div class="col-md-4 text-center">
                    <label for="dataRangeFilter" class="form-label">Período de Tiempo:</label>
                    <select id="dataRangeFilter" class="form-select">
                        <option value="all">Todos los meses</option>
                        <option value="12" selected>Últimos 12 meses</option>
                        <option value="6">Últimos 6 meses</option>
                        <option value="3">Últimos 3 meses</option>
                    </select>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="chart-container" style="position: relative; height:50vh; width:100%">
                <canvas id="decesoChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Deceso Line Chart -->
<script>
$(document).ready(function() {
    let allDecesoData = [];
    let decesoChart = null;
    
    // Fetch DECESO data and create the chart
    $.ajax({
        url: 'get_decesos_monthly_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Server error:', response.message);
                $('#decesoChart').after('<div class="alert alert-danger">Error al cargar datos: ' + response.message + '</div>');
                return;
            }
            
            // Debug data received from server
            console.log('Monthly Deceso data received:', response);
            
            if (!response.data || response.data.length === 0) {
                console.warn('No deceso data received from server');
                $('#decesoChart').after('<div class="alert alert-warning">No hay datos de decesos disponibles.</div>');
                return;
            }
            
            // Log data structure to help with debugging
            if (response.data.length > 0) {
                console.log('Sample month data:', response.data[0]);
            }
            
            allDecesoData = response.data;
            createDecesoChart(response.data);

            // since we're showing aggregated data by month
            
            // Add event listener for the data range filter
            $('#dataRangeFilter').on('change', function() {
                updateChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching deceso data:', error);
            $('#decesoChart').after('<div class="alert alert-danger">Error al cargar datos de decesos: ' + error + '</div>');
        }
    });
    
    function updateChart() {
        const selectedRange = $('#dataRangeFilter').val();
        
        let filteredData = [...allDecesoData];
        
        // Sort data by date (though it should already be sorted)
        filteredData.sort((a, b) => a.month_year.localeCompare(b.month_year));
        
        // Apply range filter to months
        if (selectedRange !== 'all' && filteredData.length > parseInt(selectedRange)) {
            // Keep only the most recent X months
            filteredData = filteredData.slice(-parseInt(selectedRange));
        }
        
        // Check if we have data after filtering
        if (filteredData.length === 0) {
            if (decesoChart) {
                decesoChart.destroy();
                decesoChart = null;
            }
            $('.alert').remove();
            $('#decesoChart').after('<div class="alert alert-warning">No hay datos para el período seleccionado.</div>');
            return;
        }
        
        // Update chart with filtered data
        updateChartData(filteredData);
    }
    
    function updateChartData(data) {
        if (decesoChart) {
            decesoChart.destroy();
        }
        $('.alert').remove(); // Remove any previous alert messages
        createDecesoChart(data);
    }
    
    function createDecesoChart(data) {
        var ctx = document.getElementById('decesoChart').getContext('2d');
        
        // Extract the data for the chart
        var months = data.map(item => item.display_date);
        var deathCounts = data.map(item => item.death_count);
        
        // Create the chart
        decesoChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Número de Camarones Muertos por Mes',
                    data: deathCounts,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Camarones Muertos',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            stepSize: 1 // Ensure we only show whole numbers
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mes/Año',
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
                                const index = context.dataIndex;
                                const monthData = data[index];
                                
                                if (!monthData) return [];
                                
                                const tooltipLines = [
                                    'Total Camarones Muertos: ' + monthData.death_count
                                ];
                                
                                // Add cause breakdown to tooltip
                                if (monthData.cause_breakdown && monthData.cause_breakdown.length > 0) {
                                    tooltipLines.push('─────────────');
                                    monthData.cause_breakdown.forEach(function(causeItem) {
                                        tooltipLines.push(causeItem.cause + ': ' + causeItem.count + ' cantidad');
                                    });
                                }
                                
                                return tooltipLines;
                            },
                            title: function(context) {
                                return 'Mes: ' + context[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Mortalidad de Camarones por Mes (Número de Muertes)',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    }
});
</script>