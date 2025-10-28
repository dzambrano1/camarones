<?php
require_once './pdo_conexion.php';

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Fetch data for Monthly Purchase Value Chart ---
$monthlyValueLabels = [];
$monthlyValueData = [];
try {
    $monthlyQuery = "SELECT DATE_FORMAT(fecha_compra, '%Y-%m') as month_year,
                           SUM(monto_compra) as total_value
                     FROM camarones
                     WHERE fecha_compra IS NOT NULL AND monto_compra IS NOT NULL AND monto_compra > 0
                     GROUP BY month_year
                     ORDER BY month_year ASC";
    $monthlyStmt = $conn->prepare($monthlyQuery);
    $monthlyStmt->execute();
    $monthlyResults = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($monthlyResults as $row) {
        $monthlyValueLabels[] = $row['month_year'];
        $monthlyValueData[] = (float)$row['total_value'];
    }
} catch (PDOException $e) {
    error_log("Error fetching monthly purchase value data: " . $e->getMessage());
}
$monthlyValueLabelsJson = json_encode($monthlyValueLabels);
$monthlyValueDataJson = json_encode($monthlyValueData);

// --- Fetch data for Cumulative Investment Chart ---
$cumulativeLabels = [];
$cumulativeData = [];
$monthlyTotals = []; // Temporary array to store monthly totals

try {
    // Fetch all purchases ordered by date from camarones table
    // Note: monto_compra is already the total amount for each tagid (poblacion group)
    $cumulativeQuery = "SELECT fecha_compra, monto_compra as purchase_value
                        FROM camarones
                        WHERE fecha_compra IS NOT NULL AND monto_compra IS NOT NULL AND monto_compra > 0
                        ORDER BY fecha_compra ASC";
    $cumulativeStmt = $conn->prepare($cumulativeQuery);
    $cumulativeStmt->execute();
    $allPurchases = $cumulativeStmt->fetchAll(PDO::FETCH_ASSOC);

    $currentCumulativeTotal = 0;
    
    // Aggregate totals per month
    foreach ($allPurchases as $purchase) {
        $monthYear = date('Y-m', strtotime($purchase['fecha_compra']));
        $value = (float)$purchase['purchase_value'];
        if (!isset($monthlyTotals[$monthYear])) {
            $monthlyTotals[$monthYear] = 0;
        }
        $monthlyTotals[$monthYear] += $value;
    }

    // Calculate cumulative sum month by month
    ksort($monthlyTotals); // Ensure months are in chronological order

    foreach ($monthlyTotals as $monthYear => $monthlyTotal) {
        $currentCumulativeTotal += $monthlyTotal;
        $cumulativeLabels[] = $monthYear;
        $cumulativeData[] = round($currentCumulativeTotal, 2); // Store cumulative total
    }

} catch (PDOException $e) {
    error_log("Error fetching cumulative investment data: " . $e->getMessage());
}
$cumulativeLabelsJson = json_encode($cumulativeLabels);
$cumulativeDataJson = json_encode($cumulativeData);
// --- End chart data fetching ---

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Camarones Register Compras</title>
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
  REGISTROS DE COMPRAS
  </h3>
  
  <div class="container mt-3 mb-4 text-center">
    <button type="button" class="btn btn-add-animal" data-bs-toggle="modal" data-bs-target="#newEntryModal">
        <i class="fas fa-plus-circle me-2"></i>Registrar
    </button>
</div>
  
<!-- New Entry Modal -->
<div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Registro de Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newEntryForm" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column - Images and Video -->
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <!-- Image slider for previews -->
                                <div id="newImagePreviewCarousel" class="carousel slide carousel-fade mb-2" data-bs-ride="carousel" data-bs-interval="5200">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img id="newImagePreview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="newImage2Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="newImage3Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        <div class="carousel-item">
                                            <video id="newVideoPreview" class="img-thumbnail" controls 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#newImagePreviewCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#newImagePreviewCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <!-- Upload buttons -->
                                <div class="d-flex flex-wrap justify-content-center">
                                    <div class="m-1">
                                        <label for="newImageUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 1
                                        </label>
                                        <input type="file" class="d-none" id="newImageUpload" name="image"
                                               accept="image/*" onchange="previewImage(event, 'newImagePreview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="newImage2Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 2
                                        </label>
                                        <input type="file" class="d-none" id="newImage2Upload" name="image2"
                                               accept="image/*" onchange="previewImage(event, 'newImage2Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="newImage3Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 3
                                        </label>
                                        <input type="file" class="d-none" id="newImage3Upload" name="image3"
                                               accept="image/*" onchange="previewImage(event, 'newImage3Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="newVideoUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-video me-1"></i>Video
                                        </label>
                                        <input type="file" class="d-none" id="newVideoUpload" name="video"
                                               accept="video/*" onchange="previewVideo(event, 'newVideoPreview')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Form Fields -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <!-- Tag ID -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="tagid" id="newTagid" required>
                                        <label for="newTagid">Tag ID</label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un Tag ID válido.
                                        </div>
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nombre" id="newNombre" required>
                                        <label for="newNombre">Nombre Estanque</label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un nombre.
                                        </div>
                                    </div>
                                </div>

                                <!-- Fecha Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_nacimiento" id="newFechaNacimiento" required>
                                        <label for="newFechaNacimiento">Fecha de Nacimiento</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione una fecha de nacimiento.
                                        </div>
                                    </div>
                                </div>

                                <!-- Fecha Compra -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_compra" id="newFechaCompra">
                                        <label for="newFechaCompra">Fecha de Compra</label>
                                    </div>
                                </div>

                                <!-- Etapa -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="etapa" id="newEtapa" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_etapa = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_etapa = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre";
                                            $result_etapa = $conn_etapa->query($sql_etapa);
                                            while ($row_etapa = $result_etapa->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_etapa['cac_etapas_nombre']) . '">' 
                                                    . htmlspecialchars($row_etapa['cac_etapas_nombre']) . '</option>';
                                            }
                                            $conn_etapa->close();
                                            ?>
                                        </select>
                                        <label for="newEtapa">Etapa</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione una etapa.
                                        </div>
                                    </div>
                                </div>

                                <!-- Estatus -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="estatus" id="newEstatus" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_estatus = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_estatus = "SELECT DISTINCT cac_estatus_nombre FROM cac_estatus ORDER BY cac_estatus_nombre";
                                            $result_estatus = $conn_estatus->query($sql_estatus);
                                            while ($row_estatus = $result_estatus->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_estatus['cac_estatus_nombre']) . '">' 
                                                    . htmlspecialchars($row_estatus['cac_estatus_nombre']) . '</option>';
                                            }
                                            $conn_estatus->close();
                                            ?>
                                        </select>
                                        <label for="newEstatus">Estatus</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione un estatus.
                                        </div>
                                    </div>
                                </div>
                                <!-- Poblacion -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="poblacion" id="newPoblacion">
                                        <label for="newPoblacion">Cantidad Comprada</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Peso -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" name="peso" id="newPeso" required>
                                <label for="newPeso">Peso Unitario Promedio</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese un peso.
                                </div>
                            </div>
                        </div>
                        <!-- Precio -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" name="precio" id="newPrecio" required>
                                <label for="newPrecio">Precio Unitario</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese un precio.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-success" form="newEntryForm">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to preview image
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            if (output) {
                output.src = reader.result;
            }            
            // Show the correct carousel item
            const carouselItems = document.querySelectorAll('#newImagePreviewCarousel .carousel-item');
            carouselItems.forEach((item, index) => {
                if (item.querySelector('img') && item.querySelector('img').id === previewId) {
                    const carousel = bootstrap.Carousel.getInstance(document.getElementById('newImagePreviewCarousel'));
                    if (carousel) {
                        carousel.to(index);
                    }
                }
            });
        };
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Function to preview video
    function previewVideo(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            if (output) {
                const source = output.querySelector('source');
                if (source) {
                    source.src = reader.result;
                    output.load();
                }
                
                // Show video carousel item (last item)
                const carousel = bootstrap.Carousel.getInstance(document.getElementById('newImagePreviewCarousel'));
                if (carousel) {
                    const carouselItems = document.querySelectorAll('#newImagePreviewCarousel .carousel-item');
                    carousel.to(carouselItems.length - 1);
                }
            }
        };
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Initialize NewEntryModal form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Get form element
        const createEntryForm = document.getElementById('newEntryForm');
        const newEntryModal = document.getElementById('newEntryModal');

        if (createEntryForm) {
            // Handle form submission
            createEntryForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission
                
                // Check form validation
                if (!createEntryForm.checkValidity()) {
                    event.stopPropagation();
                    createEntryForm.classList.add('was-validated');
                    return;
                }

                // Create a FormData object from the form
                const formData = new FormData(createEntryForm);
                
                // Add the action parameter
                formData.append('action', 'insert');

                // Show loading state
                const submitButton = document.querySelector('#newEntryModal .btn-success');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                submitButton.disabled = true;

                // Send the form data using fetch
                fetch('camarones_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Nuevo animal agregado exitosamente.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Reset form and close modal
                            createEntryForm.reset();
                            createEntryForm.classList.remove('was-validated');
                            
                            // Reset image previews
                            document.getElementById('newImagePreview').src = './images/default_image.png';
                            document.getElementById('newImage2Preview').src = './images/default_image.png';
                            document.getElementById('newImage3Preview').src = './images/default_image.png';
                            const videoPreview = document.getElementById('newVideoPreview');
                            if (videoPreview && videoPreview.querySelector('source')) {
                                videoPreview.querySelector('source').src = '';
                                videoPreview.load();
                            }
                            
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(newEntryModal);
                            if (modal) {
                                modal.hide();
                            }
                            
                            // Reload page to show new entry
                            location.reload();
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al agregar el nuevo animal.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.'
                    });
                })
                .finally(() => {
                    // Restore button state
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            });
        }

        // Initialize carousel when modal is shown
        newEntryModal.addEventListener('shown.bs.modal', function() {
            new bootstrap.Carousel(document.getElementById('newImagePreviewCarousel'), {
                interval: 5200
            });
        });
    });
</script>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="updateModalLabel">
                    <i class="fas fa-edit me-2"></i>Actualizar Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" class="needs-validation" novalidate enctype="multipart/form-data">
                    <!-- Hidden field for animal ID -->
                    <input type="hidden" name="id" id="updateAnimalId">
                    <!-- Hidden field for purchase record ID -->
                    <input type="hidden" name="compra_id" id="updateCompraId">
                    <div class="row">
                        <!-- Left Column - Images and Video -->
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <!-- Image slider for previews -->
                                <div id="updateImagePreviewCarousel" class="carousel slide carousel-fade mb-2" data-bs-ride="carousel" data-bs-interval="5200">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img id="updateImagePreview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreen(this.src)">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="updateImage2Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreen(this.src)">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="updateImage3Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreen(this.src)">
                                        </div>
                                        <div class="carousel-item">
                                            <video id="updateVideoPreview" class="img-thumbnail" controls 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreenVideo(this)">
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#updateImagePreviewCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#updateImagePreviewCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <!-- Upload buttons -->
                                <div class="d-flex flex-wrap justify-content-center">
                                    <div class="m-1">
                                        <label for="updateImageUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 1
                                        </label>
                                        <input type="file" class="d-none" id="updateImageUpload" 
                                               accept="image/*" onchange="previewImage(event, 'updateImagePreview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="updateImage2Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 2
                                        </label>
                                        <input type="file" class="d-none" id="updateImage2Upload" 
                                               accept="image/*" onchange="previewImage(event, 'updateImage2Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="updateImage3Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 3
                                        </label>
                                        <input type="file" class="d-none" id="updateImage3Upload" 
                                               accept="image/*" onchange="previewImage(event, 'updateImage3Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="updateVideoUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-video me-1"></i>Video
                                        </label>
                                        <input type="file" class="d-none" id="updateVideoUpload" 
                                               accept="video/*" onchange="previewVideo(event, 'updateVideoPreview')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Form Fields -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <!-- Tag ID -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="tagid" id="updateTagid" required readonly>
                                        <label for="updateTagid">Tag ID</label>
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nombre" id="updateNombre" required>
                                        <label for="updateNombre">Nombre Estanque</label>
                                    </div>
                                </div>

                                <!-- Fecha Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_nacimiento" id="updateFechaNacimiento" required>
                                        <label for="updateFechaNacimiento">Fecha de Nacimiento</label>
                                    </div>
                                </div>

                                <!-- Fecha Compra -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_compra" id="updateFechaCompra">
                                        <label for="updateFechaCompra">Fecha de Compra</label>
                                    </div>
                                </div>
                                <!-- Etapa -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="etapa" id="updateEtapa" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_etapa = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_etapa = "SELECT DISTINCT ac_etapas_nombre FROM ac_etapas ORDER BY ac_etapas_nombre";
                                            $result_etapa = $conn_etapa->query($sql_etapa);
                                            while ($row_etapa = $result_etapa->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_etapa['ac_etapas_nombre']) . '">' 
                                                    . htmlspecialchars($row_etapa['ac_etapas_nombre']) . '</option>';
                                            }
                                            $conn_etapa->close();
                                            ?>
                                        </select>
                                        <label for="updateEtapa">Etapa</label>
                                    </div>
                                </div>
                                <!-- Estatus -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="estatus" id="updateEstatus" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_estatus = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_estatus = "SELECT DISTINCT ac_estatus_nombre FROM ac_estatus ORDER BY ac_estatus_nombre";
                                            $result_estatus = $conn_estatus->query($sql_estatus);
                                            while ($row_estatus = $result_estatus->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_estatus['ac_estatus_nombre']) . '">' 
                                                    . htmlspecialchars($row_estatus['ac_estatus_nombre']) . '</option>';
                                            }
                                            $conn_estatus->close();
                                            ?>
                                        </select>
                                        <label for="updateEstatus">Estatus</label>
                                    </div>
                                </div>
                                
                                <!-- Cantidad -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="cantidad" id="updateCantidad">
                                        <label for="updateCantidad">Cantidad (PLs)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Peso -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" name="peso" id="updatePeso" required>
                                <label for="updatePeso">Peso Unitario Promedio</label>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" name="precio" id="updatePrecio" required>
                                <label for="updatePrecio">Precio Unitario</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-outline-success" onclick="saveUpdates()">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Purchase Modal (Simplified) -->
<div class="modal fade" id="newPurchaseModal" tabindex="-1" aria-labelledby="newPurchaseModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newPurchaseModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newPurchaseForm" class="needs-validation" novalidate>
                    <!-- Hidden field for tagid -->
                    <input type="hidden" name="tagid" id="newPurchaseTagid">
                    
                    <!-- Animal Info (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1">Estanque Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small><strong>Tag ID:</strong> <span id="newPurchaseDisplayTagid"></span></small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Nombre:</strong> <span id="newPurchaseDisplayNombre"></span></small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small><strong>Población Actual:</strong> <span id="newPurchaseDisplayPoblacion"></span> PLs</small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Etapa:</strong> <span id="newPurchaseDisplayEtapa"></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Purchase Fields -->
                    <div class="row g-3">
                        <!-- Fecha Compra -->
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="date" class="form-control" name="fecha_compra" id="newPurchaseFechaCompra" value="<?php echo date('Y-m-d'); ?>" required>
                                <label for="newPurchaseFechaCompra">Fecha de Compra</label>
                                <div class="invalid-feedback">
                                    Por favor seleccione una fecha de compra.
                                </div>
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="cantidad" id="newPurchaseCantidad" min="1" required>
                                <label for="newPurchaseCantidad">Cantidad (PLs)</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese la cantidad.
                                </div>
                            </div>
                        </div>

                        <!-- Peso -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" name="peso" id="newPurchasePeso" min="0" required>
                                <label for="newPurchasePeso">Peso Unitario (PLs)</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese el peso.
                                </div>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" name="precio" id="newPurchasePrecio" min="0" required>
                                <label for="newPurchasePrecio">Precio Unitario</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese el precio.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="saveNewPurchase()">
                    <i class="fas fa-save me-2"></i>Guardar Compra
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Save Update Modal -->
<script>
function saveUpdates() {
    // Get the form
    const form = document.getElementById('updateForm');
    if (!form) {
        console.error('Update form not found');
            return;
        }

    // Create FormData object from the form
    const formData = new FormData(form);
    
    // Add the action parameter for purchase update
    formData.append('action', 'update_purchase');
    
    // Debug: Log all form data being sent
    console.log('Debug - Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Ensure all required fields are included
    const tagid = document.getElementById('updateTagid');
    const nombre = document.getElementById('updateNombre');
    const fechaNacimiento = document.getElementById('updateFechaNacimiento');
    const fechaCompra = document.getElementById('updateFechaCompra');
    const estatus = document.getElementById('updateEstatus');
    const compra = document.getElementById('updateCompra');
    const peso = document.getElementById('updatePeso');
    const precio = document.getElementById('updatePrecio');
    const cantidad = document.getElementById('updateCantidad');
    const compraId = document.getElementById('updateCompraId');
    
    // Add all form fields to FormData
    if (tagid) formData.append('tagid', tagid.value);
    if (nombre) formData.append('nombre', nombre.value);
    if (fechaNacimiento) formData.append('fecha_nacimiento', fechaNacimiento.value);
    if (fechaCompra) formData.append('fecha_compra', fechaCompra.value);
    if (etapa) formData.append('etapa', etapa.value);
    if (estatus) formData.append('estatus', estatus.value);
    if (compra) formData.append('compra', compra.value);
    if (peso) formData.append('peso', peso.value);
    if (precio) formData.append('precio', precio.value);
    if (cantidad) formData.append('cantidad', cantidad.value);
    if (compraId) formData.append('compra_id', compraId.value);

    // Add image files if selected
    const imageFile = document.getElementById('updateImageUpload').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    // Add image2 file if selected
    const image2File = document.getElementById('updateImage2Upload').files[0];
    if (image2File) {
        formData.append('image2', image2File);
    }
    
    // Add image3 file if selected
    const image3File = document.getElementById('updateImage3Upload').files[0];
    if (image3File) {
        formData.append('image3', image3File);
    }
    
    // Add video file if selected
    const videoFile = document.getElementById('updateVideoUpload').files[0];
    if (videoFile) {
        formData.append('video', videoFile);
    }

    // Show loading state
    const saveButton = document.querySelector('#updateModal .btn-outline-success');
    if (!saveButton) {
        console.error('Save button not found');
            return;
        }
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
    saveButton.disabled = true;

    // Send the update request
        $.ajax({
        url: 'camarones_update.php',
        type: 'POST',
        data: formData,
        processData: false,  // Important for FormData
        contentType: false,  // Important for FormData
        cache: false,        // Prevent caching
        timeout: 30000,      // Increased timeout for larger files
            success: function(response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Los datos han sido actualizados exitosamente.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Close modal and refresh page
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                        if (modal) {
                            modal.hide();
                        }
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Hubo un error al actualizar los datos.'
                    });
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al procesar la respuesta del servidor.'
                });
                }
            },
            error: function(xhr, status, error) {
            console.error('Ajax error details:');
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('HTTP Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('Ready State:', xhr.readyState);
            
            let errorMessage = 'Hubo un error al enviar los datos';
            if (status === 'timeout') {
                errorMessage = 'La solicitud tardó demasiado tiempo. Por favor, intente de nuevo.';
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                    // If JSON parsing fails, show the raw response
                    errorMessage = 'Server response: ' + xhr.responseText.substring(0, 500);
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        },
        complete: function() {
            // Restore button state
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        }
    });
}

// Save new purchase function
function saveNewPurchase() {
    const form = document.getElementById('newPurchaseForm');
    if (!form) {
        console.error('New purchase form not found');
        return;
    }

    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    // Create FormData object
    const formData = new FormData(form);
    formData.append('action', 'add_purchase');

    // Show loading state
    const saveButton = document.querySelector('#newPurchaseModal .btn-success');
    if (!saveButton) {
        console.error('Save button not found');
        return;
    }
    
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
    saveButton.disabled = true;

    // Send AJAX request
    $.ajax({
        url: 'camarones_update.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 30000,
        success: function(response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Compra Agregada!',
                        text: 'La nueva compra ha sido registrada exitosamente.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Close modal and refresh page
                        const modal = bootstrap.Modal.getInstance(document.getElementById('newPurchaseModal'));
                        if (modal) {
                            modal.hide();
                        }
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Hubo un error al registrar la compra.'
                    });
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al procesar la respuesta del servidor.'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error details:');
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('Response Text:', xhr.responseText);
            
            let errorMessage = 'Hubo un error al registrar la compra';
            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    errorMessage = 'Server response: ' + xhr.responseText.substring(0, 500);
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        },
        complete: function() {
            // Restore button state
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        }
    });
}

// Add form validation before submission
document.getElementById('updateModal').addEventListener('shown.bs.modal', function () {
    const form = this.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

  <!-- DataTable for camarones records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="compraTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">Imagen</th>
                      <th class="text-center">Acciones</th>
                      <th class="text-center">Nombre</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Cantidad</th>
                      <th class="text-center">Peso</th>
                      <th class="text-center">Precio</th>
                      <th class="text-center">Población</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      // Query to get purchase data from cah_compras table joined with camarones table
                      $compraQuery = "SELECT 
                                        c.id as compra_id,
                                        c.cah_compras_tagid,
                                        a.nombre,
                                        a.poblacion,
                                        c.cah_compras_cantidad,
                                        c.cah_compras_peso,
                                        c.cah_compras_precio,
                                        DATE_FORMAT(c.cah_compras_fecha, '%Y-%m-%d') as cah_compras_fecha_formatted,
                                        a.image as animal_image
                                    FROM cah_compras c
                                    LEFT JOIN camarones a ON c.cah_compras_tagid = a.tagid
                                    ORDER BY c.cah_compras_fecha DESC"; // Order by purchase date
                                
                      $stmt = $conn->prepare($compraQuery);  
                      $stmt->execute();
                      $compraData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      
                      // If no data, display a message
                      if (empty($compraData)) {
                          echo "<tr><td colspan='9' class='text-center'>No hay registros de compra disponibles</td></tr>"; // 9 columns total
                      } else {
                          // The foreach loop below will handle rendering the rows
                      }
                  } catch (PDOException $e) {
                      error_log("Error in compra table data fetching: " . $e->getMessage());
                      echo "<tr><td colspan='9' class='text-center'>Error al cargar los datos de compra: " . $e->getMessage() . "</td></tr>"; // 9 columns total
                  }

                  // Ensure $compraData is iterable even if the try block failed or returned empty
                  if (!isset($compraData)) {
                      $compraData = []; 
                  }

                  foreach ($compraData as $row) {
                      // Determine image path
                      $imagePath = './images/default_image.png'; // Default image
                      if (!empty($row['animal_image'])) {
                          $imagePath = './' . htmlspecialchars($row['animal_image']); // Use animal's image
                      }

                      // Render row using cah_compras table columns joined with camarones
                      echo "<tr>";
                      echo '<td class="text-center"><img src="' . $imagePath . '" alt="Animal Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>';
                      echo '<td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-success btn-sm add-purchase" 
                                    data-tagid="' . htmlspecialchars($row['cah_compras_tagid'] ?? '') . '"
                                    title="Nueva Compra">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn btn-warning btn-sm edit-compra" 
                                    data-id="' . htmlspecialchars($row['compra_id'] ?? '') . '"
                                    data-tagid="' . htmlspecialchars($row['cah_compras_tagid'] ?? '') . '"
                                    data-fecha="' . htmlspecialchars($row['cah_compras_fecha_formatted'] ?? '') . '"
                                    data-precio="' . htmlspecialchars($row['cah_compras_precio'] ?? '') . '"
                                    data-peso="' . htmlspecialchars($row['cah_compras_peso'] ?? '') . '"
                                    data-cantidad="' . htmlspecialchars($row['cah_compras_cantidad'] ?? '') . '"
                                    data-poblacion="' . htmlspecialchars($row['poblacion'] ?? '') . '"
                                    title="Editar Compra">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-compra" 
                                    data-id="' . htmlspecialchars($row['compra_id'] ?? '') . '"
                                    data-tagid="' . htmlspecialchars($row['cah_compras_tagid'] ?? '') . '"
                                    title="Eliminar Compra">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>';
                      
                      echo "<td class='text-center'>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>"; // camarones.nombre                      
                      echo "<td class='text-center'>" . htmlspecialchars($row['cah_compras_fecha_formatted'] ?? 'N/A') . "</td>"; // cah_compras.cah_compras_fecha
                      echo "<td class='text-center'>" . htmlspecialchars($row['cah_compras_tagid'] ?? '') . "</td>"; // cah_compras.cah_compras_tagid
                      echo "<td class='text-center'>" . htmlspecialchars($row['cah_compras_cantidad'] ?? '0') . "</td>"; // cah_compras.cah_compras_cantidad
                      echo "<td class='text-center'>" . htmlspecialchars($row['cah_compras_peso'] ?? '0.00') . "</td>"; // cah_compras.cah_compras_peso
                      echo "<td class='text-center'>" . htmlspecialchars($row['cah_compras_precio'] ?? '0.00') . "</td>"; // cah_compras.cah_compras_precio
                      echo "<td class='text-center'>" . htmlspecialchars($row['poblacion'] ?? '0') . "</td>"; // camarones.poblacion
                      echo "</tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Monthly Purchase Bar Chart with Cumulative Line -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Compras Mensuales y Acumulativas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="purchaseRangeFilter" class="form-label">Período de Tiempo:</label>
                            <select id="purchaseRangeFilter" class="form-select">
                                <option value="all">Todos los meses</option>
                                <option value="12" selected>Últimos 12 meses</option>
                                <option value="6">Últimos 6 meses</option>
                                <option value="3">Últimos 3 meses</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-end h-100">
                                <div class="chart-legend">
                                    <span class="legend-item">
                                        <span class="legend-color" style="background-color: rgba(54, 162, 235, 0.8);"></span>
                                        Compras Mensuales
                                    </span>
                                    <span class="legend-item ms-3">
                                        <span class="legend-color" style="background-color: rgba(255, 99, 132, 0.8);"></span>
                                        Acumulativo
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height:60vh; width:100%">
                        <canvas id="purchaseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTable for VH compra -->
<script>
$(document).ready(function() {
    $('#compraTable').DataTable({
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
        
        // Column specific settings
        columnDefs: [
            {
                targets: [0], // New Image column
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Assuming the image path is directly in the data or calculated in PHP
                    // The PHP code already renders the <img> tag, so we just return the cell content
                    return data; 
                }
            },
            {
                targets: [6, 7], // Adjusted: Peso (now 6), Precio (now 7) columns
                render: function(data, type, row) {
                    if (type === 'display') {
                        return parseFloat(data).toLocaleString('es-ES', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    return data;
                }
            },
            {
                targets: [3], // Adjusted: Fecha column (now 3)
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
                targets: [1], // Adjusted: Actions column (now 1)
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // Handle edit button click
    $('.edit-compra').click(function() {
        var button = $(this); // Store reference to the button
        var compraId = button.data('id'); // cah_compras record ID
        var tagid = button.data('tagid');
        var fechaCompra = button.data('fecha'); // Get the purchase date
        
        // Show loading indicator (optional)
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Fetch purchase details via AJAX using compra_id
        $.ajax({
            url: 'camarones_get_details.php',
            type: 'GET',
            data: { 
                tagid: tagid,
                compra_id: compraId 
            },
            dataType: 'json',
            success: function(animalData) {
                if (animalData && animalData.success) {
                    const data = animalData.data;
                    // Populate the #updateModal fields
                    // Animal details from camarones table
                    $('#updateAnimalId').val(data.id); // Hidden field for database ID
                    $('#updateTagid').val(data.tagid);
                    $('#updateNombre').val(data.nombre);
                    $('#updateFechaNacimiento').val(data.fecha_nacimiento);
                    $('#updateEtapa').val(data.etapa);
                    $('#updateEstatus').val(data.estatus);
                    $('#updatePoblacion').val(data.poblacion); // Read-only reference
                    
                    // Purchase details from cah_compras table (primary source)
                    $('#updateCompraId').val(data.compra_id); // Hidden field for cah_compras ID
                    $('#updateCantidad').val(data.cah_compras_cantidad || '');
                    $('#updatePeso').val(data.cah_compras_peso || '');
                    $('#updatePrecio').val(data.cah_compras_precio || '');
                    $('#updateFechaCompra').val(data.cah_compras_fecha || '');

                    // Reset and Populate image/video previews (adjust paths as needed)
                    const basePath = './'; // Adjust if your image/video paths are different
                    $('#updateImagePreview').attr('src', data.image ? basePath + data.image : './images/default_image.png');
                    $('#updateImage2Preview').attr('src', data.image2 ? basePath + data.image2 : './images/default_image.png');
                    $('#updateImage3Preview').attr('src', data.image3 ? basePath + data.image3 : './images/default_image.png');
                    const videoPreview = $('#updateVideoPreview');
                    const videoSource = videoPreview.find('source');
                    if (data.video) {
                        videoSource.attr('src', basePath + data.video);
                        videoPreview[0].load(); // Reload the video element
                        videoPreview.show();
                    } else {
                        videoSource.attr('src', '');
                        videoPreview[0].load();
                         // Optionally hide video preview if no video
                         // videoPreview.hide(); 
                    }
                     // Ensure carousel starts at the first image
                    $('#updateImagePreviewCarousel').carousel(0); 


                    // Get the modal instance and show it
                    var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
                    updateModal.show();

                } else {
                    Swal.fire(
                        'Error',
                        animalData.message || 'No se pudieron obtener los detalles del animal.',
                        'error'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error fetching animal details: ", status, error);
                Swal.fire(
                    'Error',
                    'Hubo un problema al conectar con el servidor para obtener los detalles.',
                    'error'
                );
            },
            complete: function() {
                 // Restore button state
                button.prop('disabled', false).html('<i class="fas fa-edit"></i>');
            }
        });
    });
    
    // Handle add purchase button click
    $('.add-purchase').click(function() {
        var button = $(this);
        var tagid = button.data('tagid');
        
        // Show loading indicator
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Fetch animal details to populate the read-only fields
        $.ajax({
            url: 'camarones_get_details.php',
            type: 'GET',
            data: { tagid: tagid },
            dataType: 'json',
            success: function(animalData) {
                if (animalData && animalData.success) {
                    const data = animalData.data;
                    
                    // Populate hidden and display fields
                    $('#newPurchaseTagid').val(data.tagid);
                    $('#newPurchaseDisplayTagid').text(data.tagid);
                    $('#newPurchaseDisplayNombre').text(data.nombre || 'N/A');
                    $('#newPurchaseDisplayPoblacion').text(data.poblacion || '0');
                    
                    // Clear purchase fields
                    $('#newPurchaseCantidad').val('');
                    $('#newPurchasePeso').val('');
                    $('#newPurchasePrecio').val('');
                    $('#newPurchaseFechaCompra').val(new Date().toISOString().split('T')[0]);
                    
                    // Remove validation classes
                    $('#newPurchaseForm').removeClass('was-validated');
                    
                    // Show the modal
                    var newPurchaseModal = new bootstrap.Modal(document.getElementById('newPurchaseModal'));
                    newPurchaseModal.show();
                } else {
                    Swal.fire(
                        'Error',
                        animalData.message || 'No se pudieron obtener los detalles del animal.',
                        'error'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error fetching animal details: ", status, error);
                Swal.fire(
                    'Error',
                    'Hubo un problema al conectar con el servidor.',
                    'error'
                );
            },
            complete: function() {
                // Restore button state
                button.prop('disabled', false).html('<i class="fas fa-plus"></i>');
            }
        });
    });
    
    // Handle delete button click
    $('.delete-compra').click(function() {
        var compraId = $(this).data('id'); // cah_compras table ID
        var tagid = $(this).data('tagid');
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro de compra?',
            text: `¿Está seguro de que desea eliminar la información de compra del animal con Tag ID ${tagid}? Esta acción no eliminará el animal del inventario.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar registro',
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

                // Send AJAX request to delete only purchase data
                $.ajax({
                    url: 'camarones_update.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        compra_id: compraId,
                        tagid: tagid
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                             // Show success message
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: response.message || 'La información de compra ha sido eliminada correctamente.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to show updated data
                                location.reload();
                            });
                        } else {
                             Swal.fire({
                                title: 'Error',
                                text: response.message || 'No se pudo eliminar la información de compra.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                       
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud de eliminación';
                        
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

<!-- Chart.js Script for Monthly Purchase Chart -->
<script>
$(document).ready(function() {
    let allPurchaseData = [];
    let purchaseChart = null;
    
    // Fetch monthly purchase data
    $.ajax({
        url: 'get_monthly_purchase_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Server error:', response.error);
                $('#purchaseChart').after('<div class="alert alert-danger">Error al cargar datos: ' + response.error + '</div>');
                return;
            }
            
            // Debug data received from server
            console.log('Monthly Purchase data received:', response);
            
            if (!response || response.length === 0) {
                console.warn('No purchase data received from server');
                $('#purchaseChart').after('<div class="alert alert-warning">No hay datos de compras disponibles.</div>');
                return;
            }
            
            // Log data structure to help with debugging
            if (response.length > 0) {
                console.log('Sample purchase data:', response[0]);
            }
            
            allPurchaseData = response;
            createPurchaseChart(response);
            
            // Add event listener for the data range filter
            $('#purchaseRangeFilter').on('change', function() {
                updatePurchaseChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching purchase data:', error);
            $('#purchaseChart').after('<div class="alert alert-danger">Error al cargar datos de compras: ' + error + '</div>');
        }
    });
    
    function updatePurchaseChart() {
        const selectedRange = $('#purchaseRangeFilter').val();
        
        let filteredData = [...allPurchaseData];
        
        // Sort data by month (though it should already be sorted)
        filteredData.sort((a, b) => a.month.localeCompare(b.month));
        
        // Apply range filter to months
        if (selectedRange !== 'all' && filteredData.length > parseInt(selectedRange)) {
            // Keep only the most recent X months
            filteredData = filteredData.slice(-parseInt(selectedRange));
        }
        
        // Check if we have data after filtering
        if (filteredData.length === 0) {
            if (purchaseChart) {
                purchaseChart.destroy();
                purchaseChart = null;
            }
            $('.purchase-alert').remove();
            $('#purchaseChart').after('<div class="alert alert-warning purchase-alert">No hay datos para el período seleccionado.</div>');
            return;
        }
        
        // Update chart with filtered data
        updatePurchaseChartData(filteredData);
    }
    
    function updatePurchaseChartData(data) {
        if (purchaseChart) {
            purchaseChart.destroy();
        }
        $('.purchase-alert').remove(); // Remove any previous alert messages
        createPurchaseChart(data);
    }
    
    function createPurchaseChart(data) {
        var ctx = document.getElementById('purchaseChart').getContext('2d');
        
        // Extract the data for the chart
        var months = data.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
        });
        var purchases = data.map(item => item.total_purchase);
        
        // Calculate cumulative purchases
        var cumulativePurchases = [];
        var runningTotal = 0;
        purchases.forEach(purchase => {
            runningTotal += purchase;
            cumulativePurchases.push(runningTotal);
        });
        
        // Create the chart
        purchaseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Compras Mensuales ($)',
                        data: purchases,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        type: 'bar',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Acumulativo ($)',
                        data: cumulativePurchases,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        fill: false,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes/Año',
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
                            text: 'Compras Mensuales ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Acumulativo ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
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
                                const datasetLabel = context.dataset.label;
                                const value = context.parsed.y;
                                
                                return datasetLabel + ': $' + value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            },
                            title: function(context) {
                                return 'Mes: ' + context[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Compras Mensuales - Mensual vs Acumulativo',
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

<style>
.chart-legend {
    font-size: 14px;
}
.legend-item {
    display: inline-flex;
    align-items: center;
}
.legend-color {
    width: 16px;
    height: 16px;
    display: inline-block;
    margin-right: 5px;
    border: 1px solid #ccc;
}
</style>

</body>
</html>
