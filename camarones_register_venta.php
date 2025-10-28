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
<title>Camarones Register Ventas</title>
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
  REGISTROS DE VENTAS
  </h3>
  
  <!-- Add New Sale Button -->
  <div class="text-center mb-4">
      <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#newVentaModal">
          <i class="fas fa-plus-circle me-2"></i>Nuevo Registro
      </button>
  </div>
    
  <!-- New Venta Entry Modal -->
  
  <div class="modal fade" id="newVentaModal" tabindex="-1" aria-labelledby="newVentaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newVentaModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newVentaForm">
                <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                                <label for="new_fecha" class="form-label">Fecha Venta</label>
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
                        <small id="poblacion-info" class="form-text text-muted mt-1" style="display: none;">
                            <i class="fas fa-info-circle me-1"></i>
                            Población disponible: <span id="poblacion-count">0</span> camarones
                        </small>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-feather-alt"></i>
                                <label for="new_camarones" class="form-label">Camarones Vendidos</label>
                                <input type="number" class="form-control" id="new_camarones" name="camarones" min="1" step="1" required>
                            </span>
                        </div>
                        <small id="camarones-validation" class="form-text text-danger mt-1" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            La cantidad vendida supera la existencia actual
                        </small>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-list"></i>
                                <label for="new_presentacion" class="form-label">Presentación</label>
                            </span>
                            <select class="form-control" id="new_presentacion" name="presentacion" required>
                                <option value="">Presentaciones</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-weight-scale"></i>
                                <label for="new_peso" class="form-label">Peso (kg) Venta </label>
                                <input type="number" class="form-control" id="new_peso" name="peso" min="0.1" step="0.1" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <label for="form-label">Precio ($/Kg) Venta </label>
                                <input type="number" class="form-control" id="new_precio" name="precio" min="0.01" step="0.01" required>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewVenta">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
  
  <!-- DataTable for vh_venta records -->
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="ventaTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                    <th class="text-center">Acciones</th>
                    <th class="text-center">Estatus</th>
                    <th class="text-center">Estanque</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Tag ID</th>
                    <th class="text-center">Precio ($/Kg) Venta</th>
                    <th class="text-center">Peso (Kg) Venta</th>
                    <th class="text-center">Presentación</th>
                    <th class="text-center">Total Venta</th>
                    <th class="text-center">Camarones Vendidos</th>
                    <th class="text-center">Población</th>
                  </tr>
              </thead>
              <tbody>
              <?php
                  try {
                      // Query to get all sales records from cah_ventas table
                        $ventaQuery = "SELECT 
                            ventas.id,
                            ventas.cah_ventas_tagid as tagid,
                            ventas.cah_ventas_fecha as fecha,
                            ventas.cah_ventas_precio as precio_unitario,
                            ventas.cah_ventas_peso as peso_unitario,
                            ventas.cah_ventas_cantidad as cantidad,
                            ventas.cah_ventas_presentacion as presentacion,
                            c.nombre,
                            c.estatus,
                            c.poblacion
                        FROM cah_ventas ventas
                        INNER JOIN camarones c ON ventas.cah_ventas_tagid = c.tagid
                        ORDER BY ventas.cah_ventas_fecha DESC";
                      $stmt = $conn->prepare($ventaQuery);
                      $stmt->execute();
                      $ventaData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // If no data, display a message with exactly 10 columns (no colspan)
                      if (empty($ventaData)) {
                          echo "<tr>";
                          echo "<td class='text-center'>-</td>"; // Acciones
                          echo "<td class='text-center'>-</td>"; // Estatus
                          echo "<td class='text-center'><em>No hay registros de ventas disponibles</em></td>"; // Nombre
                          echo "<td class='text-center'>-</td>"; // Fecha
                          echo "<td class='text-center'>-</td>"; // Tag ID
                          echo "<td class='text-center'>-</td>"; // Precio Unitario
                          echo "<td class='text-center'>-</td>"; // Peso Unitario
                          echo "<td class='text-center'>-</td>"; // Presentación
                          echo "<td class='text-center'>-</td>"; // Total
                          echo "<td class='text-center'>-</td>"; // Camarones Vendidos
                          echo "<td class='text-center'>-</td>"; // Población
                          echo "</tr>";
                      } else {
                          foreach ($ventaData as $row) {
                              echo "<tr>";
                              // Add action buttons (edit, add more sales, and delete) - moved to first column, no image
                              echo '<td class="text-center">
                                  <div class="btn-group" role="group">
                                      <button class="btn btn-warning btn-sm edit-venta" 
                                          data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                          data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                          data-fecha="' . htmlspecialchars($row['fecha'] ?? '') . '"
                                          data-precio="' . htmlspecialchars($row['precio_unitario'] ?? '') . '"
                                          data-peso="' . htmlspecialchars($row['peso_unitario'] ?? '') . '"
                                          data-Camarones="' . htmlspecialchars($row['cantidad'] ?? '') . '"
                                          data-presentacion="' . htmlspecialchars($row['presentacion'] ?? '') . '"
                                          data-poblacion="' . htmlspecialchars($row['poblacion'] ?? '') . '">
                                          <i class="fas fa-edit"></i>
                                      </button>
                                      <button class="btn btn-dark btn-sm add-venta" 
                                          data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                          data-poblacion="' . htmlspecialchars($row['poblacion'] ?? '') . '"
                                          title="Vender más Camarones">
                                          <i class="fas fa-plus"></i>
                                      </button>
                                      <button class="btn btn-danger btn-sm delete-venta" 
                                          data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                          data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '">
                                          <i class="fas fa-trash"></i>
                                      </button>
                                  </div>
                              </td>';

                              echo "<td class='text-center'>" . htmlspecialchars($row['estatus'] ?? 'Vendido') . "</td>";
                              echo "<td class='text-center'>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                              echo "<td class='text-center'>" . htmlspecialchars($row['fecha'] ?? '') . "</td>";
                              echo "<td class='text-center'>" . htmlspecialchars($row['tagid'] ?? '') . "</td>";
                              echo "<td class='text-center'>" . ($row['precio_unitario'] ?? 0) . "</td>";
                              echo "<td class='text-center'>" . ($row['peso_unitario'] ?? 0) . "</td>";
                              echo "<td class='text-center'>" . htmlspecialchars($row['presentacion'] ?? 'N/A') . "</td>";
                              // Calculate total: (precio_unitario * peso_unitario) * cantidad
                              $total = ($row['precio_unitario'] ?? 0) * ($row['peso_unitario'] ?? 0);
                              echo "<td class='text-center'>" . $total . "</td>";
                              echo "<td class='text-center'>" . htmlspecialchars($row['cantidad'] ?? '') . "</td>";
                              echo "<td class='text-center'>" . htmlspecialchars($row['poblacion'] ?? '') . "</td>";
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in venta table: " . $e->getMessage());
                      echo "<tr>";
                      echo "<td class='text-center'>-</td>"; // Acciones
                      echo "<td class='text-center'>-</td>"; // Estatus
                      echo "<td class='text-center'><em>Error al cargar los datos</em></td>"; // Nombre
                      echo "<td class='text-center'>-</td>"; // Fecha
                      echo "<td class='text-center'>-</td>"; // Tag ID
                      echo "<td class='text-center'>-</td>"; // Precio Unitario
                      echo "<td class='text-center'>-</td>"; // Peso Unitario
                      echo "<td class='text-center'>-</td>"; // Presentación
                      echo "<td class='text-center'>-</td>"; // Total
                      echo "<td class='text-center'>-</td>"; // Camarones Vendidos
                      echo "<td class='text-center'>-</td>"; // Población
                      echo "</tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Available for Sale DataTable -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Animales Disponibles para Venta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="availableTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Imagen</th>
                                    <th class="text-center">Acción</th>
                                    <th class="text-center">Estatus</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Tag ID</th>
                                    <th class="text-center">Población</th>
                                    <th class="text-center">Raza</th>
                                    <th class="text-center">Etapa</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                try {
                                    // Query to get all animals from camarones table
                                    $availableQuery = "SELECT 
                                                        id,
                                                        tagid,
                                                        nombre,
                                                        estatus,
                                                        image,
                                                        poblacion,
                                                        etapa
                                                    FROM camarones 
                                                    ORDER BY tagid ASC";
                                                    
                                    $stmt = $conn->prepare($availableQuery);  
                                    $stmt->execute();
                                    $availableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    // If no data, display a message with exactly 8 columns (no colspan)
                                    if (empty($availableData)) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>-</td>"; // Imagen
                                        echo "<td class='text-center'>-</td>"; // Acción
                                        echo "<td class='text-center'>-</td>"; // Estatus
                                        echo "<td class='text-center'><em>No hay animales disponibles</em></td>"; // Nombre
                                        echo "<td class='text-center'>-</td>"; // Tag ID
                                        echo "<td class='text-center'>-</td>"; // Población
                                        echo "<td class='text-center'>-</td>"; // Raza
                                        echo "<td class='text-center'>-</td>"; // Etapa
                                        echo "</tr>";
                                    } else {
                                        foreach ($availableData as $row) {
                                            echo "<tr>";
                                            echo '<td class="text-center">';
                                            // Check if animal has an image
                                            if (!empty($row['image'])) {
                                                echo '<img src="' . htmlspecialchars($row['image']) . '" alt="Imagen del animal" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                                            } else {
                                                echo '<img src="images/camarones-poblacion.png" alt="Imagen por defecto" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                                            }
                                            echo '</td>';
                                            
                                            // Add sell action button (+ button in black)
                                            echo '<td class="text-center">';
                                            echo '<button class="btn btn-dark btn-sm sell-animal" 
                                                    data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                                    data-poblacion="' . htmlspecialchars($row['poblacion'] ?? '0') . '"
                                                    title="Registrar Venta">
                                                    <i class="fas fa-plus"></i>
                                                </button>';
                                            echo '</td>';

                                            echo "<td class='text-center'>" . htmlspecialchars($row['estatus'] ?? '') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['tagid'] ?? '') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['poblacion'] ?? '0') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['raza'] ?? '') . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['etapa'] ?? '') . "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error in available animals table: " . $e->getMessage());
                                    echo "<tr>";
                                    echo "<td class='text-center'>-</td>"; // Imagen
                                    echo "<td class='text-center'>-</td>"; // Acción
                                    echo "<td class='text-center'>-</td>"; // Estatus
                                    echo "<td class='text-center'><em>Error al cargar los datos</em></td>"; // Nombre
                                    echo "<td class='text-center'>-</td>"; // Tag ID
                                    echo "<td class='text-center'>-</td>"; // Población
                                    echo "<td class='text-center'>-</td>"; // Raza
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

<!-- Initialize DataTable for VH venta -->
<script>
$(document).ready(function() {
    // Debug: Check table structure before DataTables initialization
    console.log('Table header columns:', $('#ventaTable thead tr th').length);
    console.log('First data row columns:', $('#ventaTable tbody tr:first td').length);
    
    // Check if we have consistent column counts (should be 10 columns)
    var headerCols = $('#ventaTable thead tr th').length;
    var firstRowCols = $('#ventaTable tbody tr:first td').length;
    
    if (headerCols !== firstRowCols) {
        console.error('Column count mismatch! Header:', headerCols, 'First row:', firstRowCols);
        // Log all row column counts
        $('#ventaTable tbody tr').each(function(index) {
            console.log('Row', index, 'columns:', $(this).find('td').length);
        });
    }
    
    // Expected columns: Imagen, Acciones, Estatus, Nombre, Fecha, Tag ID, Precio Unitario, Peso Unitario, Presentación, Total
    console.log('Expected column count: 10');
    
    $('#ventaTable').DataTable({
        // Set initial page length
        pageLength: 25,
        
        // Configure length menu options
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
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
                targets: [5], // Precio Unitario column - currency formatting
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
                targets: [6], // Peso Unitario column - weight formatting
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
                targets: [7], // Presentación column - text formatting
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data || 'N/A';
                    }
                    return data;
                }
            },
            {
                targets: [8], // Total column - currency formatting
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
                targets: [0], // Imagen column
                orderable: false,
                searchable: false
            },
            {
                targets: [1], // Actions column
                orderable: false,
                searchable: false
            }
        ]
    });

    // Initialize Available Animals DataTable
    $('#availableTable').DataTable({
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
<!-- Add handler for sell-animal button (from available animals table) -->
<script>
    $('.sell-animal').click(function() {
        var tagid = $(this).data('tagid');
        var poblacion = $(this).data('poblacion');
        
        // Populate the tagid field in the newVentaModal
        $('#new_tagid').val(tagid);
        
        // Store the current population for validation
        $('#newVentaModal').data('current-population', poblacion);
        
        // Trigger the population count display
        $('#new_tagid').trigger('blur');
        
        // Show the modal
        var newVentaModal = new bootstrap.Modal(document.getElementById('newVentaModal'));
        newVentaModal.show();
    });

    // Add handler for add-venta button (add more sales to existing record)
    $(document).on('click', '.add-venta', function() {
        var tagid = $(this).data('tagid');
        var poblacion = $(this).data('poblacion');
        
        // Populate the tagid field in the newVentaModal
        $('#new_tagid').val(tagid);
        
        // Store the current population for validation
        $('#newVentaModal').data('current-population', poblacion);
        
        // Trigger the population count display
        $('#new_tagid').trigger('blur');
        
        // Show the modal
        var newVentaModal = new bootstrap.Modal(document.getElementById('newVentaModal'));
        newVentaModal.show();
    });

    // Handle new entry form submission with population validation
    $('#saveNewVenta').click(function() {
        // Validate the form
        var form = document.getElementById('newVentaForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get current population for validation
        var currentPopulation = parseInt($('#newVentaModal').data('current-population')) || 0;
        var camaronesVendidos = parseInt($('#new_camarones').val()) || 0;
        
        // Check if requested sales exceed available population
        if (camaronesVendidos > currentPopulation) {
            Swal.fire({
                title: 'Error de validación',
                text: `No puedes vender ${camaronesVendidos} camarones. Solo hay ${currentPopulation} camarones disponibles.`,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            precio: $('#new_precio').val(),
            peso: $('#new_peso').val(),
            camarones: $('#new_camarones').val(),
            presentacion: $('#new_presentacion').val(),
            fecha: $('#new_fecha').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar la venta de ${formData.camarones} camarones del Tag ID ${formData.tagid}? Población disponible: ${currentPopulation}.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, registrar venta',
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
                    url: 'process_venta.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        precio: formData.precio,
                        peso: formData.peso,
                        camarones: formData.camarones,
                        presentacion: formData.presentacion,
                        fecha: formData.fecha
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newVentaModal'));
                        if(modal) {
                            modal.hide();
                        }
                        
                        if(response.success) {
                            // Show success message
                            Swal.fire({
                                title: '¡Registro exitoso!',
                                text: 'El registro de venta ha sido guardado correctamente',
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
                                text: response.message || 'Ha ocurrido un error al registrar la venta',
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
</script>
<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // Load presentaciones options for new entry form
    $.ajax({
        url: 'get_presentaciones.php',
        type: 'GET',
        dataType: 'json',
        success: function(presentaciones) {
            var select = $('#new_presentacion');
            select.empty();
            select.append('<option value="">Seleccionar presentación...</option>');
            
            presentaciones.forEach(function(pres) {
                select.append(`<option value="${pres.nombre}">${pres.nombre}</option>`);
            });
        },
        error: function() {
            console.error('Error loading presentaciones for new entry form');
        }
    });

    // Handle tagid input change to show population count
    $('#new_tagid').on('input blur', function() {
        var tagid = $(this).val().trim();
        
        if (tagid === '') {
            $('#poblacion-info').hide();
            return;
        }
        
        // Fetch population count for this tagid
        $.ajax({
            url: 'get_poblacion_count.php',
            type: 'GET',
            data: { tagid: tagid },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#poblacion-count').text(response.poblacion || 0);
                    $('#poblacion-info').show();
                    
                    // Change color based on population
                    if (response.poblacion > 0) {
                        $('#poblacion-info').removeClass('text-danger').addClass('text-muted');
                        // Set max value for camarones input
                        $('#new_camarones').attr('max', response.poblacion);
                    } else {
                        $('#poblacion-info').removeClass('text-muted').addClass('text-danger');
                        $('#poblacion-count').text('0 (No disponible)');
                        // Disable camarones input if no population
                        $('#new_camarones').attr('max', 0);
                    }
                } else {
                    $('#poblacion-info').hide();
                }
            },
            error: function() {
                $('#poblacion-info').hide();
            }
        });
    });

    // Handle camarones Vendidos input validation
    $('#new_camarones').on('input change', function() {
        var camaronesVendidos = parseInt($(this).val()) || 0;
        var poblacionMax = parseInt($('#new_camarones').attr('max')) || 0;
        
        if (camaronesVendidos > poblacionMax) {
            $('#camarones-validation').show();
            $(this).addClass('is-invalid');
        } else {
            $('#camarones-validation').hide();
            $(this).removeClass('is-invalid');
        }
    });

    // Handle new entry form submission
    $('#saveNewVenta').click(function() {
        // Validate the form
        var form = document.getElementById('newVentaForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Additional validation for camarones Vendidos
        var camaronesVendidos = parseInt($('#new_camarones').val()) || 0;
        var poblacionMax = parseInt($('#new_camarones').attr('max')) || 0;
        
        if (camaronesVendidos > poblacionMax) {
            $('#camarones-validation').show();
            $('#new_camarones').addClass('is-invalid');
            Swal.fire({
                title: 'Error de validación',
                text: `No puedes vender ${camaronesVendidos} camarones. Solo hay ${poblacionMax} camarones disponibles.`,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            precio: $('#new_precio').val(),
            peso: $('#new_peso').val(),
            camarones: $('#new_camarones').val(),
            presentacion: $('#new_presentacion').val(),
            fecha: $('#new_fecha').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar la venta de ${formData.camarones} camarones del Tag ID ${formData.tagid}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, registrar venta',
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
                
                // Send AJAX request to update camarones record with sale information
                $.ajax({
                    url: 'process_venta.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        precio: formData.precio,
                        peso: formData.peso,
                        camarones: formData.camarones,
                        presentacion: formData.presentacion,
                        fecha: formData.fecha
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newVentaModal'));
                        if(modal) {
                            modal.hide();
                        }
                        
                        if(response.success) {
                            // Show success message
                            Swal.fire({
                                title: '¡Registro exitoso!',
                                text: 'El registro de venta ha sido guardado correctamente',
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
                                text: response.message || 'Ha ocurrido un error al registrar la venta',
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

    // Add handler for sell-animal button
    $('.sell-animal').click(function() {
        var tagid = $(this).data('tagid');
        
        // Populate the tagid field in the newVentaModal
        $('#new_tagid').val(tagid);
        
        // Trigger the population count display
        $('#new_tagid').trigger('blur');
        
        // Show the modal
        var newVentaModal = new bootstrap.Modal(document.getElementById('newVentaModal'));
        newVentaModal.show();
    });

         // Handle edit button click
     $('.edit-venta').click(function() {
         var id = $(this).data('id');
         var tagid = $(this).data('tagid');
         var precio = $(this).data('precio');
         var peso = $(this).data('peso');
         var camarones = $(this).data('camarones');
         var presentacion = $(this).data('presentacion');
         var fecha = $(this).data('fecha');
         var poblacion = $(this).data('poblacion');
        
        // Edit Venta Modal dialog for editing

        var modalHtml = `
        <div class="modal fade" id="editVentaModal" tabindex="-1" aria-labelledby="editVentaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVentaModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Registro de Venta
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editVentaForm">
                            <div class="mb-2">  
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-tag"></i>
                                                <label for="edit_tagid" class="form-label"> Estanque ID </label>
                                                <input type="text" class="form-control" id="edit_tagid" value="${tagid}" readonly>
                                            </span>
                                        </div>
                                    </div>
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
                                        <i class="fas fa-dollar-sign"></i>
                                        <label for="edit_precio" class="form-label">Precio Camarones Vendidos ($/Kg)</label>
                                        <input type="number" class="form-control" id="edit_precio" value="${precio}" step="0.01" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-weight-scale"></i>
                                    </span>
                                    <input type="number" class="form-control" id="edit_peso" value="${peso}" min="0.1" step="0.1" placeholder="Peso Camarones Vendidos (kg)" required>
                                </div>
                            </div>
                            <div class="mb-2">
                                 <div class="input-group">
                                     <span class="input-group-text">
                                         <i class="fas fa-fish"></i>
                                     </span>
                                     <input type="number" class="form-control" id="edit_camarones" value="${camarones}" min="1" step="1" placeholder="Cantidad de Camarones" data-current-population="${poblacion}" required>
                                 </div>
                                 <small id="edit-camarones-validation" class="form-text text-danger mt-1" style="display: none;">
                                     <i class="fas fa-fish-fins me-1"></i>
                                     La cantidad no puede exceder la población disponible
                                 </small>
                             </div>
                             <div class="mb-2">
                                 <div class="input-group">
                                     <span class="input-group-text">
                                         <i class="fas fa-list"></i>
                                     </span>
                                     <select class="form-control" id="edit_presentacion" required>
                                         <option value="">Seleccionar presentación...</option>
                                     </select>
                                 </div>
                             </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditVenta">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editVentaModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
                 // Show the modal
         var editModal = new bootstrap.Modal(document.getElementById('editVentaModal'));
         editModal.show();
         
         // Load presentaciones options
         $.ajax({
             url: 'get_presentaciones.php',
             type: 'GET',
             dataType: 'json',
             success: function(presentaciones) {
                 var select = $('#edit_presentacion');
                 select.empty();
                 select.append('<option value="">Seleccionar presentación...</option>');
                 
                 presentaciones.forEach(function(pres) {
                     var selected = (pres.nombre === presentacion) ? 'selected' : '';
                     select.append(`<option value="${pres.nombre}" ${selected}>${pres.nombre}</option>`);
                 });
             },
             error: function() {
                 console.error('Error loading presentaciones');
             }
         });
         
         // Add validation for camarones input in edit form
         $('#edit_camarones').on('input change', function() {
             var newcamarones = parseInt($(this).val()) || 0;
             var oldcamarones = parseInt(camarones) || 0;
             var currentPopulation = parseInt($('#edit_camarones').attr('data-current-population')) || 0;
             
             // Calculate if this would exceed available population
             var populationChange = oldcamarones - newcamarones; // Positive if reducing, negative if increasing
             var newPopulation = currentPopulation + populationChange;
             
             if (newPopulation < 0) {
                 $('#edit-camarones-validation').show();
                 $(this).addClass('is-invalid');
             } else {
                 $('#edit-camarones-validation').hide();
                 $(this).removeClass('is-invalid');
             }
         });
         
                  // Handle save button click
         $('#saveEditVenta').click(function() {
             // Check if there are validation errors
             if ($('#edit_camarones').hasClass('is-invalid')) {
                 Swal.fire({
                     title: 'Error de validación',
                     text: 'Por favor corrija los errores antes de guardar',
                     icon: 'error',
                     confirmButtonColor: '#dc3545'
                 });
                 return;
             }
             
             var formData = {
                 tagid: $('#edit_tagid').val(),
                 precio: $('#edit_precio').val(),
                 peso: $('#edit_peso').val(),
                 camarones: $('#edit_camarones').val(),
                 presentacion: $('#edit_presentacion').val(),
                 fecha: $('#edit_fecha').val()
             };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar la información de venta para el animal con Tag ID ${formData.tagid}?`,
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
                         url: 'camarones_venta_update.php',
                         type: 'POST',
                         data: {
                             action: 'update',
                             tagid: formData.tagid,
                             precio_venta: formData.precio,
                             peso_venta: formData.peso,
                             camarones_Vendidos: formData.camarones,
                             presentacion: formData.presentacion,
                             fecha_venta: formData.fecha,
                             estatus: 'Vendido'
                         },
                        success: function(response) {
                            // Close the modal
                            editModal.hide();
                            
                                                         if(response.success) {
                                 // Show success message with population adjustment info
                                 let message = `La información de venta para el animal con Tag ID ${formData.tagid} ha sido actualizada correctamente.`;
                                 
                                 if (response.population_adjustment !== undefined) {
                                     if (response.population_adjustment > 0) {
                                         message += `\n\nSe han devuelto ${response.population_adjustment} camarones a la población disponible.`;
                                     } else if (response.population_adjustment < 0) {
                                         message += `\n\nSe han vendido ${Math.abs(response.population_adjustment)} camarones adicionales.`;
                                     }
                                     message += `\nNueva población disponible: ${response.new_population} camarones.`;
                                 }
                                 
                                 Swal.fire({
                                     title: '¡Actualización exitosa!',
                                     text: message,
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
    $('.delete-venta').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        
        // Debug: Check if tagid is being retrieved correctly
        console.log('Delete button clicked - ID:', id, 'TagID:', tagid);
        
        if (!tagid) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo obtener el Tag ID del animal',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro de venta?',
            text: `¿Está seguro de que desea eliminar este registro de venta específico (ID: ${id}) del estanque ${tagid}? Los camarones se devolverán a la población disponible.`,
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
                    url: 'process_venta.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        if(response.success) {
                            // Show success message with population info
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: response.message || `El registro de venta (ID: ${id}) ha sido eliminado correctamente y se restauraron los camarones a la población.`,
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

<!-- Sales Revenue Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Ingresos por Ventas - Mensual y Acumulado</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="yearFilter" class="form-label">Año</label>
                    <select id="yearFilter" class="form-select">
                        <option value="all">Todos los años</option>
                        <!-- Years will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tankFilter" class="form-label">Estanque</label>
                    <select id="tankFilter" class="form-select">
                        <option value="all">Todos los estanques</option>
                        <!-- Tank IDs will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="showCumulativeToggle" checked>
                        <label class="form-check-label" for="showCumulativeToggle">
                            Mostrar línea acumulativa
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Filtros se aplican en tiempo real
                        </small>
                    </div>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height:60vh; width:100%">
                <canvas id="ventaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Monthly Sales Revenue Chart -->
<script>
$(document).ready(function() {
    let salesChart = null;
    let allSalesData = [];
    
    // Fetch monthly sales data from server
    $.ajax({
        url: 'get_monthly_sales.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            console.log('Sales data received:', data);
            allSalesData = data;
            
            if (data.length === 0) {
                console.log('No sales data available');
                // Still create chart with empty data to show "No data" message
                createSalesChart([]);
                return;
            }
            
            createSalesChart(data);
            populateYearFilter(data);
            populateTankFilter(data);
            
            // Add event listeners for filters
            $('#yearFilter').on('change', function() {
                updateSalesChart();
            });
            
            $('#tankFilter').on('change', function() {
                updateSalesChart();
            });
            
            $('#showCumulativeToggle').on('change', function() {
                updateSalesChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching monthly sales data:', error);
            // Fallback to generate the chart with empty data
            createSalesChart([]);
        }
    });
    
    function populateYearFilter(data) {
        // Extract unique years from the data
        const years = [...new Set(data.map(item => item.year))];
        years.sort(); // Sort years chronologically
        
        // Add options to the dropdown
        const yearFilter = $('#yearFilter');
        yearFilter.empty().append('<option value="all">Todos los años</option>');
        years.forEach(year => {
            yearFilter.append(`<option value="${year}">${year}</option>`);
        });
    }
    
    function populateTankFilter(data) {
        // Extract unique tank IDs from the data
        const tankIds = [...new Set(data.map(item => item.tagid))];
        tankIds.sort((a, b) => parseInt(a) - parseInt(b)); // Sort tank IDs numerically
        
        // Add options to the dropdown
        const tankFilter = $('#tankFilter');
        tankFilter.empty().append('<option value="all">Todos los estanques</option>');
        tankIds.forEach(tankId => {
            tankFilter.append(`<option value="${tankId}">Estanque ${tankId}</option>`);
        });
    }
    
    function updateSalesChart() {
        const selectedYear = $('#yearFilter').val();
        const selectedTank = $('#tankFilter').val();
        
        let filteredData = [...allSalesData];
        
        // Filter by year if not "all"
        if (selectedYear !== 'all') {
            filteredData = filteredData.filter(item => item.year == selectedYear);
        }
        
        // Filter by tank if not "all"
        if (selectedTank !== 'all') {
            filteredData = filteredData.filter(item => item.tagid == selectedTank);
        }
        
        console.log('Filtered data:', filteredData, 'Year:', selectedYear, 'Tank:', selectedTank);
        
        // Update chart with filtered data
        if (salesChart) {
            salesChart.destroy();
        }
        createSalesChart(filteredData);
    }
    
    function createSalesChart(data) {
        const ctx = document.getElementById('ventaChart').getContext('2d');
        
        // Prepare data arrays
        const monthLabels = [];
        const revenueData = [];
        const cumulativeData = [];
        const showCumulative = $('#showCumulativeToggle').is(':checked');
        
        console.log('Creating sales chart with data:', data);
        
        if (data.length === 0) {
            // Show "No data" message
            monthLabels.push('Sin Datos');
            revenueData.push(0);
            cumulativeData.push(0);
        } else {
            // Aggregate data by month and year (in case multiple tanks have data for same month)
            const aggregatedData = {};
            
            data.forEach(item => {
                const key = `${item.year}-${item.month}`;
                if (!aggregatedData[key]) {
                    aggregatedData[key] = {
                        year: item.year,
                        month: item.month,
                        total_revenue: 0
                    };
                }
                aggregatedData[key].total_revenue += parseFloat(item.total_revenue);
            });
            
            // Convert aggregated data back to array and sort
            const processedData = Object.values(aggregatedData);
            processedData.sort((a, b) => {
                if (a.year !== b.year) {
                    return a.year - b.year;
                }
                return a.month - b.month;
            });
            
            // Calculate cumulative total
            let runningTotal = 0;
            
            // Prepare data for chart
            processedData.forEach(item => {
                // Format month name
                const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                const monthIndex = parseInt(item.month) - 1; // Convert to 0-based index
                const monthName = monthNames[monthIndex];
                
                // Create label in format "Month Year" (e.g., "Enero 2023")
                const label = `${monthName} ${item.year}`;
                monthLabels.push(label);
                
                // Monthly revenue data
                const monthlyRevenue = parseFloat(item.total_revenue);
                revenueData.push(monthlyRevenue);
                
                // Add to cumulative total
                runningTotal += monthlyRevenue;
                cumulativeData.push(runningTotal);
            });
        }
        
        console.log('Chart data - Labels:', monthLabels, 'Revenue:', revenueData, 'Cumulative:', cumulativeData);
        
        // Prepare datasets
        const datasets = [
            {
                label: 'Ingresos Mensuales ($)',
                data: revenueData,
                backgroundColor: 'rgba(40, 167, 69, 0.6)',
                borderColor: 'rgba(40, 167, 69, 1)',
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
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 3,
                pointBackgroundColor: 'rgba(13, 110, 253, 1)',
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
        salesChart = new Chart(ctx, {
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
                                    return 'Ingresos Mensuales: $' + context.parsed.y.toLocaleString('es-ES', {
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
                            const selectedYear = $('#yearFilter').val();
                            const selectedTank = $('#tankFilter').val();
                            
                            let titleText = 'Ingresos por Ventas';
                            
                            // Add tank info to title
                            if (selectedTank !== 'all') {
                                titleText += ` - Estanque ${selectedTank}`;
                            } else {
                                titleText += ' - Todos los Estanques';
                            }
                            
                            // Add year info to title
                            if (selectedYear !== 'all') {
                                titleText += ` (${selectedYear})`;
                            } else {
                                titleText += ' (Todos los Años)';
                            }
                            
                            return titleText;
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
                            text: 'Ingresos Mensuales ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(40, 167, 69, 1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            },
                            color: 'rgba(40, 167, 69, 1)'
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(40, 167, 69, 0.1)'
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
                            color: 'rgba(13, 110, 253, 1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            },
                            color: 'rgba(13, 110, 253, 1)'
                        },
                        grid: {
                            drawOnChartArea: false,
                            color: 'rgba(13, 110, 253, 0.1)'
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