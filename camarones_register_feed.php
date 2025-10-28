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
<title>Camarones Alimento Concentrado</title>
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

<!-- Daily Feeding Estimation Table -->
<div class="container mt-4 mb-4">
    <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">

            <div class="position-relative">
                <h5 class="mb-0 text-center fw-bold" style="font-size: 1.3rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    <i class="fas fa-box me-3" style="font-size: 1.5rem;"></i>📦 Estimación de ración diaria por millón de postlarvas (PL10–PL30)
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background: linear-gradient(135deg, #343a40, #495057);">
                        <tr>
                            <th class="text-center text-white fw-bold py-3" style="border: none; font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="fas fa-calendar-alt me-2"></i>Día del ciclo
                            </th>
                            <th class="text-center text-white fw-bold py-3" style="border: none; font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="fas fa-fish me-2"></i>PL aprox.
                            </th>
                            <th class="text-center text-white fw-bold py-3" style="border: none; font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="fas fa-weight me-2"></i>Ración estimada
                            </th>
                            <th class="text-center text-white fw-bold py-3" style="border: none; font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="fas fa-balance-scale me-2"></i>Peso total/día
                            </th>
                            <th class="text-center text-white fw-bold py-3" style="border: none; font-size: 0.95rem; letter-spacing: 0.5px;">
                                <i class="fas fa-dollar-sign me-2"></i>Precio estimado/día
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="transition: all 0.3s ease; background: linear-gradient(135deg, #f8f9fa, #ffffff);" onmouseover="this.style.background='linear-gradient(135deg, #e3f2fd, #f0f7ff)'; this.style.transform='scale(1.02)'" onmouseout="this.style.background='linear-gradient(135deg, #f8f9fa, #ffffff)'; this.style.transform='scale(1)'">
                            <td class="text-center fw-bold py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #007bff, #0056b3); font-size: 0.9rem; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">
                                        Día 1–7
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(40,167,69,0.3);">
                                    PL10–PL15
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #17a2b8, #117a8b); color: white; font-size: 0.85rem; box-shadow: 0 2px 6px rgba(23,162,184,0.3);">
                                        0.02 g/PL/día
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #6f42c1, #5a2d91); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(111,66,193,0.3);">
                                    20 kg
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #fd7e14, #e55100); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(253,126,20,0.3);">
                                    USD $60–$80
                                </span>
                            </td>
                        </tr>
                        <tr style="transition: all 0.3s ease; background: linear-gradient(135deg, #ffffff, #f8f9fa);" onmouseover="this.style.background='linear-gradient(135deg, #e8f5e8, #f0f8f0)'; this.style.transform='scale(1.02)'" onmouseout="this.style.background='linear-gradient(135deg, #ffffff, #f8f9fa)'; this.style.transform='scale(1)'">
                            <td class="text-center fw-bold py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #007bff, #0056b3); font-size: 0.9rem; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">
                                        Día 8–14
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(40,167,69,0.3);">
                                    PL15–PL20
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #17a2b8, #117a8b); color: white; font-size: 0.85rem; box-shadow: 0 2px 6px rgba(23,162,184,0.3);">
                                        0.015 g/PL/día
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #6f42c1, #5a2d91); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(111,66,193,0.3);">
                                    15 kg
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #fd7e14, #e55100); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(253,126,20,0.3);">
                                    USD $45–$60
                                </span>
                            </td>
                        </tr>
                        <tr style="transition: all 0.3s ease; background: linear-gradient(135deg, #f8f9fa, #ffffff);" onmouseover="this.style.background='linear-gradient(135deg, #fff3cd, #ffeaa7)'; this.style.transform='scale(1.02)'" onmouseout="this.style.background='linear-gradient(135deg, #f8f9fa, #ffffff)'; this.style.transform='scale(1)'">
                            <td class="text-center fw-bold py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #007bff, #0056b3); font-size: 0.9rem; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">
                                        Día 15–21
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(40,167,69,0.3);">
                                    PL20–PL25
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #17a2b8, #117a8b); color: white; font-size: 0.85rem; box-shadow: 0 2px 6px rgba(23,162,184,0.3);">
                                        0.01 g/PL/día
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #6f42c1, #5a2d91); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(111,66,193,0.3);">
                                    10 kg
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #fd7e14, #e55100); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(253,126,20,0.3);">
                                    USD $30–$50
                                </span>
                            </td>
                        </tr>
                        <tr style="transition: all 0.3s ease; background: linear-gradient(135deg, #ffffff, #f8f9fa);" onmouseover="this.style.background='linear-gradient(135deg, #f8d7da, #f5c6cb)'; this.style.transform='scale(1.02)'" onmouseout="this.style.background='linear-gradient(135deg, #ffffff, #f8f9fa)'; this.style.transform='scale(1)'">
                            <td class="text-center fw-bold py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #007bff, #0056b3); font-size: 0.9rem; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">
                                        Día 22–30
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(40,167,69,0.3);">
                                    PL25–PL30
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #17a2b8, #117a8b); color: white; font-size: 0.85rem; box-shadow: 0 2px 6px rgba(23,162,184,0.3);">
                                        0.008 g/PL/día
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #6f42c1, #5a2d91); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(111,66,193,0.3);">
                                    8 kg
                                </span>
                            </td>
                            <td class="text-center py-3" style="border: none; vertical-align: middle;">
                                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #fd7e14, #e55100); color: white; font-size: 0.9rem; font-weight: bold; box-shadow: 0 2px 8px rgba(253,126,20,0.3);">
                                    USD $25–$40
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-top: 3px solid #28a745; padding: 1.5rem;">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(135deg, #d1ecf1, #bee5eb); border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lightbulb text-info me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="text-info mb-1 fw-bold">💡 Precio estimado por kilo</h6>
                                <p class="text-info mb-0 small">
                                    <strong>Acuimpo Feed® postlarva:</strong> entre USD $3.00 y $4.00/kg, dependiendo del distribuidor y volumen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 h-100" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); border-radius: 10px; box-shadow: 0 4px 15px rgba(255,193,7,0.2);">
                        <div class="card-body p-3">
                            <h6 class="card-title text-warning-emphasis fw-bold">
                                <i class="fas fa-flask me-2"></i>🧪 Consideraciones
                            </h6>
                            <p class="card-text small text-warning-emphasis mb-2">
                                <i class="fas fa-check-circle me-1"></i>
                                Estas cifras son para <strong>1 millón de postlarvas</strong> en condiciones semi-intensivas.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 h-100" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border-radius: 10px; box-shadow: 0 4px 15px rgba(40,167,69,0.2);">
                        <div class="card-body p-3">
                            <h6 class="card-title text-success-emphasis fw-bold">
                                <i class="fas fa-box me-2"></i>📦 Presentación comercial
                            </h6>
                            <p class="card-text small text-success-emphasis mb-2">
                                <i class="fas fa-check-circle me-1"></i>
                                El alimento suele venir en <strong>sacos de 20 kg</strong>, y el costo por saco ronda los <strong>USD $60–$80</strong>.
                            </p>
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
  <h3  class="container mt-4 text-white" class="collapse" id="concentrado">
  REGISTROS DE ALIMENTO CONCENTRADO
  </h3>
    
  <!-- New Concentrado Entry Modal -->
  
  <div class="modal fade" id="newConcentradoModal" tabindex="-1" aria-labelledby="newConcentradoModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newConcentradoModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Concentrado
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
                                    <i class="fa-solid fa-syringe me-2"></i>Concentrado
                                </label>
                                <select class="form-select" id="new_alimento" name="alimento" required>
                                    <option value="">Productos</option>
                                    <?php
                                    try {
                                        $sql_alimentos = "SELECT DISTINCT cac_concentrado_nombre FROM cac_concentrado
                                         ORDER BY cac_concentrado_nombre ASC";
                                        $stmt_alimentos = $conn->prepare($sql_alimentos);
                                        $stmt_alimentos->execute();
                                        $alimentos = $stmt_alimentos->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($alimentos as $alimento_row) {
                                            echo '<option value="' . htmlspecialchars($alimento_row['cac_concentrado_nombre']) . '">' . htmlspecialchars($alimento_row['cac_concentrado_nombre']) . '</option>';
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
  
  <!-- DataTable for cah_concentrado records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="concentradoTable" class="table table-striped table-bordered">
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
                      // Query to get ALL concentrado records AND animals without concentrado records
                        $concentradoQuery = "
                            -- First: All existing concentrado records with animal info
                            SELECT 
                                a.id AS camarones_id,
                                a.tagid,
                                a.nombre,
                                c.cah_concentrado_fecha_inicio,
                                c.cah_concentrado_fecha_fin,
                                c.id AS concentrado_record_id,
                                c.cah_concentrado_etapa,
                                c.cah_concentrado_producto,
                                c.cah_concentrado_racion,
                                c.cah_concentrado_costo,
                                CAST((c.cah_concentrado_racion * c.cah_concentrado_costo) AS DECIMAL(10,2)) as total_value,
                                1 AS in_concentrado_history,
                                1 AS sort_order
                            FROM 
                                cah_concentrado c
                            INNER JOIN 
                                camarones a ON c.cah_concentrado_tagid = a.tagid
                            
                            UNION ALL
                            
                            -- Second: Animals without any concentrado records
                            SELECT 
                                a.id AS camarones_id,
                                a.tagid,
                                a.nombre,
                                NULL as cah_concentrado_fecha_inicio,
                                NULL as cah_concentrado_fecha_fin,
                                NULL as concentrado_record_id,
                                NULL as cah_concentrado_etapa,
                                NULL as cah_concentrado_producto,
                                NULL as cah_concentrado_racion,
                                NULL as cah_concentrado_costo,
                                NULL as total_value,
                                0 AS in_concentrado_history,
                                2 AS sort_order
                            FROM 
                                camarones a
                            WHERE 
                                a.tagid NOT IN (SELECT DISTINCT cah_concentrado_tagid FROM cah_concentrado WHERE cah_concentrado_tagid IS NOT NULL)
                            
                            ORDER BY 
                                sort_order ASC, 
                                CASE WHEN sort_order = 1 THEN cah_concentrado_fecha_inicio END DESC,
                                CASE WHEN sort_order = 1 THEN concentrado_record_id END DESC,
                                CASE WHEN sort_order = 2 THEN nombre END ASC";

                        $stmt = $conn->prepare($concentradoQuery);
                        $stmt->execute();
                        $concentradoData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // If no data, display a message
                      if (empty($concentradoData)) {
                          echo "<tr><td colspan='10' class='text-center'>No hay animales ni registros de concentrado</td></tr>";
                      } else {
                          // Get vigencia setting for concentrado records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT ca_vencimiento_concentrado FROM ca_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              
                              // Explicitly use PDO fetch method
                              $row = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row && isset($row['ca_vencimiento_concentrado'])) {
                                  $vigencia = intval($row['ca_vencimiento_concentrado']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                              // Continue with default value
                          }
                          
                          $currentDate = new DateTime();
                          
                          foreach ($concentradoData as $row) {
                              echo "<tr>";
                              
                              // Actions column - Different buttons based on whether record has concentrado history
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              
                              // Always show Add (+) button
                              echo '        <button class="btn btn-success btn-sm" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#newConcentradoModal" 
                                              data-tagid-prefill="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                              title="Registrar Nuevo Concentrado">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              // Only show Edit and Delete buttons for records with concentrado history
                              if ($row['in_concentrado_history'] == 1) {
                                  // Edit button
                                  echo '        <button class="btn btn-warning btn-sm edit-concentrado" 
                                                  data-id="'.htmlspecialchars($row['concentrado_record_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                                  data-etapa="'.htmlspecialchars($row['cah_concentrado_etapa'] ?? '').'" 
                                                  data-producto="'.htmlspecialchars($row['cah_concentrado_producto'] ?? '').'" 
                                                  data-racion="'.htmlspecialchars($row['cah_concentrado_racion'] ?? '').'" 
                                                  data-costo="'.htmlspecialchars($row['cah_concentrado_costo'] ?? '').'" 
                                                  data-fecha_inicio="'.htmlspecialchars($row['cah_concentrado_fecha_inicio'] ?? '').'" 
                                                  data-fecha_fin="'.htmlspecialchars($row['cah_concentrado_fecha_fin'] ?? '').'" 
                                                  title="Editar Registro">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                                  
                                  // Delete button
                                  echo '        <button class="btn btn-danger btn-sm delete-concentrado" 
                                                  data-id="'.htmlspecialchars($row['concentrado_record_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                                  title="Eliminar Registro">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              }
                              
                              echo '    </div>';
                              echo '</td>';

                              // Column 1: Fecha Inicio
                              echo "<td>" . htmlspecialchars($row['cah_concentrado_fecha_inicio'] ?? '') . "</td>";
                              // Column 2: Fecha Fin
                              echo "<td>" . htmlspecialchars($row['cah_concentrado_fecha_fin'] ?? '') . "</td>";
                              // Column 3: Nombre Animal
                              echo "<td>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                              // Column 4: Tag ID Animal
                              echo "<td>" . htmlspecialchars($row['tagid'] ?? 'N/A') . "</td>";

                              if ($row['in_concentrado_history'] == 1) {
                                  // Record has concentrado data
                                  // Column 5: Etapa
                                  echo "<td>" . htmlspecialchars($row['cah_concentrado_etapa'] ?? 'N/A') . "</td>";
                                  // Column 6: Producto
                                  echo "<td>" . htmlspecialchars($row['cah_concentrado_producto'] ?? 'N/A') . "</td>";
                                  // Column 7: Racion
                                  echo "<td>" . htmlspecialchars($row['cah_concentrado_racion'] ?? 'N/A') . "</td>";
                                  // Column 8: Costo
                                  echo "<td>" . htmlspecialchars($row['cah_concentrado_costo'] ?? 'N/A') . "</td>";
                                  // Column 9: Total Value
                                  echo "<td>" . htmlspecialchars($row['total_value'] ?? 'N/A') . "</td>";

                                  // Column 10: Status - Calculate due date and determine status
                                  try {
                                      if (!empty($row['cah_concentrado_fecha_inicio'])) {
                                          $concentradoDate = new DateTime($row['cah_concentrado_fecha_inicio']);
                                          $dueDate = clone $concentradoDate;
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
                                      error_log("Date error: " . $e->getMessage() . " for date: " . $row['cah_concentrado_fecha_inicio']);
                                      echo '<td class="text-center"><span class="badge bg-secondary">ERROR FECHA</span></td>';
                                  }
                              } else {
                                  // Animal has no concentrado history - show placeholder data
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
                      error_log("Error in concentrado table: " . $e->getMessage());
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
    $('#concentradoTable').DataTable({
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
            text: `¿Desea registrar el registro de alimento concentrado ${formData.racion} kg para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_concentrado.php',
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
                                text: 'El registro de alimento concentrado ha sido guardado correctamente',
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
    $('.edit-concentrado').click(function() {
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
                                                $sql_productos = "SELECT DISTINCT cac_concentrado_nombre FROM cac_concentrado ORDER BY cac_concentrado_nombre ASC";
                                                $stmt_productos = $conn->prepare($sql_productos);
                                                $stmt_productos->execute();
                                                $productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($productos as $producto_row) {
                                                    echo '<option value="' . htmlspecialchars($producto_row['cac_concentrado_nombre']) . '">' . htmlspecialchars($producto_row['cac_concentrado_nombre']) . '</option>';
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
                text: `¿Desea actualizar el registro de alimento concentrado para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_concentrado.php',
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
    $('.delete-concentrado').click(function() {
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
                    url: 'process_concentrado.php',
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
                    <select id="concentradoTagidFilter" class="form-select">
                        <option value="all">Todos los Estanques</option>
                        <!-- Tagids will be populated dynamically -->
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
                <canvas id="concentradoExpenseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Monthly Concentrado Expense Chart -->
<script>
$(document).ready(function() {
    let concentradoExpenseChart = null;
    let allConcentradoData = [];
    
    // Global helper function to convert YYYY-MM to readable month name
    function getMonthName(monthString) {
        const [year, month] = monthString.split('-');
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return `${monthNames[parseInt(month) - 1]} ${year}`;
    }
    
    // Fetch monthly concentrado expense data from server
    $.ajax({
        url: 'get_monthly_concentrado_expense_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            console.log('Concentrado expense data received:', data);
            
            if (!data.data || data.data.length === 0) {
                console.log('No concentrado expense data available');
                createConcentradoExpenseChart([]);
                return;
            }
            
            // Process the data to add readable month names
            const processedData = data.data.map(item => ({
                ...item,
                monthName: getMonthName(item.month)
            }));
            
            allConcentradoData = processedData;
            createConcentradoExpenseChart(processedData);
            populateConcentradoTagidFilter(data.tagids);
            
            // Add event listeners for filters
            $('#concentradoTagidFilter').on('change', function() {
                updateConcentradoChart();
            });
            
            $('#showCumulativeToggle').on('change', function() {
                updateConcentradoChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching concentrado expense data:', error);
            createConcentradoExpenseChart([]);
        }
    });
    
    function populateConcentradoTagidFilter(tagids) {
        const tagidFilter = $('#concentradoTagidFilter');
        tagidFilter.empty().append('<option value="all">Todos los Estanques</option>');
        tagids.forEach(item => {
            tagidFilter.append(`<option value="${item.tagid}">${item.nombre} (${item.tagid})</option>`);
        });
    }
    
    function updateConcentradoChart() {
        const selectedTagid = $('#concentradoTagidFilter').val();
        
        if (selectedTagid !== 'all') {
            $.ajax({
                url: 'get_monthly_concentrado_expense_data.php',
                type: 'GET',
                data: { tagid: selectedTagid },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        console.error('Server error:', data.error);
                        return;
                    }
                    
                    if (!data.data || data.data.length === 0) {
                        createConcentradoExpenseChart([]);
                        return;
                    }
                    
                    const processedData = data.data.map(item => ({
                        ...item,
                        monthName: getMonthName(item.month)
                    }));
                    
                    createConcentradoExpenseChart(processedData);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching filtered concentrado data:', error);
                    createConcentradoExpenseChart([]);
                }
            });
        } else {
            createConcentradoExpenseChart(allConcentradoData);
        }
    }
    
    function createConcentradoExpenseChart(data) {
        // Destroy existing chart if it exists
        if (concentradoExpenseChart) {
            concentradoExpenseChart.destroy();
        }
        
        const ctx = document.getElementById('concentradoExpenseChart').getContext('2d');
        
        // Prepare data arrays
        const monthLabels = [];
        const expenseData = [];
        const cumulativeData = [];
        const showCumulative = $('#showCumulativeToggle').is(':checked');
        
        console.log('Creating concentrado expense chart with data:', data);
        
        if (data.length === 0) {
            // Show "No data" message
            monthLabels.push('Sin Datos');
            expenseData.push(0);
            cumulativeData.push(0);
        } else {
            // Sort data by month (YYYY-MM format)
            data.sort((a, b) => a.month.localeCompare(b.month));
            
            // Prepare data for chart
            data.forEach(item => {
                monthLabels.push(item.monthName);
                
                // Monthly expense data
                const monthlyExpense = parseFloat(item.monthly_expense) || 0;
                expenseData.push(monthlyExpense);
                
                // Cumulative expense data
                const cumulativeExpense = parseFloat(item.cumulative_expense) || 0;
                cumulativeData.push(cumulativeExpense);
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
        concentradoExpenseChart = new Chart(ctx, {
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
                            const selectedTagid = $('#concentradoTagidFilter').val();
                            if (selectedTagid !== 'all') {
                                const selectedOption = $('#concentradoTagidFilter option:selected').text();
                                return `Gastos en Alimento Concentrado - ${selectedOption}`;
                            }
                            return 'Gastos Mensuales en Alimento Concentrado - Todos los Estanques';
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

<!-- Monthly FCR Chart -->
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
                    <select id="fcrTagidFilter" class="form-select">
                        <option value="all">Todos los Estanques</option>
                        <!-- Tagids will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showTargetLineToggle" checked>
                        <label class="form-check-label" for="showTargetLineToggle">
                            Mostrar línea objetivo (FCR 1.5)
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">
                        <strong>FCR:</strong> Peso concentrado / Peso animal<br>
                        <em>Valores más bajos indican mejor eficiencia</em>
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
    
    // Global helper function for month names (already defined above)
    function getMonthNameFCR(monthString) {
        const [year, month] = monthString.split('-');
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return `${monthNames[parseInt(month) - 1]} ${year}`;
    }
    
    // Fetch monthly FCR data from server
    $.ajax({
        url: 'get_monthly_fcr_data_new.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('FCR Server error:', data.error);
                return;
            }
            
            console.log('FCR data received:', data);
            
            if (!data.data || data.data.length === 0) {
                console.log('No FCR data available');
                createFCRChart([]);
                return;
            }
            
            // Process the data to add readable month names
            const processedData = data.data.map(item => ({
                ...item,
                monthName: getMonthNameFCR(item.month)
            }));
            
            allFCRData = processedData;
            createFCRChart(processedData);
            populateFCRTagidFilter(data.tagids);
            
            // Add event listeners for filters
            $('#fcrTagidFilter').on('change', function() {
                updateFCRChart();
            });
            
            $('#showTargetLineToggle').on('change', function() {
                updateFCRChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching FCR data:', error);
            createFCRChart([]);
        }
    });
    
    function populateFCRTagidFilter(tagids) {
        const tagidFilter = $('#fcrTagidFilter');
        tagidFilter.empty().append('<option value="all">Todos los Estanques</option>');
        tagids.forEach(item => {
            tagidFilter.append(`<option value="${item.tagid}">${item.nombre} (${item.tagid})</option>`);
        });
    }
    
    function updateFCRChart() {
        const selectedTagid = $('#fcrTagidFilter').val();
        
        if (selectedTagid !== 'all') {
            $.ajax({
                url: 'get_monthly_fcr_data_new.php',
                type: 'GET',
                data: { tagid: selectedTagid },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        console.error('FCR Server error:', data.error);
                        return;
                    }
                    
                    if (!data.data || data.data.length === 0) {
                        createFCRChart([]);
                        return;
                    }
                    
                    const processedData = data.data.map(item => ({
                        ...item,
                        monthName: getMonthNameFCR(item.month)
                    }));
                    
                    createFCRChart(processedData);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching filtered FCR data:', error);
                    createFCRChart([]);
                }
            });
        } else {
            createFCRChart(allFCRData);
        }
    }
    
    function createFCRChart(data) {
        // Destroy existing chart if it exists
        if (fcrChart) {
            fcrChart.destroy();
        }
        
        const ctx = document.getElementById('fcrChart').getContext('2d');
        
        // Prepare data arrays
        const monthLabels = [];
        const fcrData = [];
        const targetLine = [];
        const showTargetLine = $('#showTargetLineToggle').is(':checked');
        
        console.log('Creating FCR chart with data:', data);
        
        if (data.length === 0) {
            // Show "No data" message
            monthLabels.push('Sin Datos');
            fcrData.push(0);
            targetLine.push(1.5);
        } else {
            // Sort data by year and month
            data.sort((a, b) => {
                if (a.year !== b.year) {
                    return a.year - b.year;
                }
                return a.month_num - b.month_num;
            });
            
            // Prepare data for chart
            data.forEach(item => {
                monthLabels.push(item.monthName);
                fcrData.push(parseFloat(item.fcr_value) || 0);
                targetLine.push(1.5); // Target FCR line (good FCR for shrimp)
            });
        }
        
        console.log('FCR Chart data - Labels:', monthLabels, 'FCR Values:', fcrData);
        
        // Prepare datasets
        const datasets = [
            {
                label: 'FCR (Concentrado/Animal)',
                data: fcrData,
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 3,
                pointBackgroundColor: function(context) {
                    const value = context.parsed.y;
                    if (value <= 1.5) return 'rgba(40, 167, 69, 1)'; // Green - Excellent
                    if (value <= 2.0) return 'rgba(23, 162, 184, 1)'; // Blue - Good
                    if (value <= 2.5) return 'rgba(255, 193, 7, 1)'; // Yellow - Regular
                    return 'rgba(220, 53, 69, 1)'; // Red - Needs improvement
                },
                pointBorderColor: '#fff',
                pointRadius: 6,
                pointHoverRadius: 8,
                tension: 0.3,
                type: 'line'
            }
        ];
        
        // Add target line if toggle is checked
        if (showTargetLine) {
            datasets.push({
                label: 'FCR Objetivo (1.5)',
                data: targetLine,
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 0,
                tension: 0,
                type: 'line',
                borderDash: [10, 5],
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
                                    return 'FCR Objetivo: 1.5';
                                }
                                
                                if (monthData) {
                                    const tooltipLines = [
                                        'FCR: ' + context.parsed.y.toFixed(3),
                                        'Total Alimento: ' + (monthData.total_feed_kg || 0).toFixed(2) + ' kg',
                                        'Ganancia Biomasa: ' + (monthData.total_biomass_gain_kg || 0).toFixed(2) + ' kg',
                                        'Biomasa Inicial: ' + (monthData.initial_biomass_kg || 0).toFixed(2) + ' kg',
                                        'Biomasa Final: ' + (monthData.final_biomass_kg || 0).toFixed(2) + ' kg',
                                        'Tanques: ' + monthData.tank_count,
                                        'Registros Concentrado: ' + monthData.concentrado_records,
                                        'Registros Peso: ' + monthData.peso_records
                                    ];
                                    return tooltipLines;
                                }
                                
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(3);
                            },
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            const selectedTagid = $('#fcrTagidFilter').val();
                            if (selectedTagid !== 'all') {
                                const selectedOption = $('#fcrTagidFilter option:selected').text();
                                return `FCR Mensual - ${selectedOption}`;
                            }
                            return 'Índice de Conversión Alimenticia Mensual - Todos los Estanques';
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
                            text: 'FCR (kg alimento / kg ganancia)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: 'rgba(23, 162, 184, 1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2);
                            },
                            color: 'rgba(23, 162, 184, 1)'
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(23, 162, 184, 0.1)'
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
