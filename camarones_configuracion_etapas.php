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
<title>Camarones Configuracion Etapas</title>
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

<style>
/* Modern Modal Styles */
.modern-modal {
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.modern-header {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
    padding: 2rem;
    position: relative;
}

.modern-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    z-index: 2;
}

.icon-container {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.icon-container i {
    font-size: 1.5rem;
    color: white;
}

.title-section {
    flex: 1;
}

.modal-title {
    color: white;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.modal-subtitle {
    color: rgba(255, 255, 255, 0.9);
    margin: 0.5rem 0 0 0;
    font-size: 0.95rem;
    font-weight: 400;
}

.modern-close {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.modern-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.modern-close i {
    color: white;
    font-size: 1rem;
}

.modern-body {
    padding: 2.5rem;
    background: #fafbfc;
}

.modern-form {
    max-width: 100%;
}

.modern-form-group {
    margin-bottom: 2rem;
}

.input-container {
    position: relative;
    background: white;
    border-radius: 16px;
    padding: 0.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.input-container:focus-within {
    border-color: #48bb78;
    box-shadow: 0 8px 30px rgba(72, 187, 120, 0.15);
    transform: translateY(-2px);
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #48bb78;
    z-index: 2;
    width: 20px;
    text-align: center;
}

.input-wrapper {
    position: relative;
    margin-left: 2.5rem;
}

.modern-input {
    border: none;
    background: transparent;
    padding: 1rem 0.5rem;
    font-size: 1rem;
    width: 100%;
    outline: none;
    color: #2d3748;
    font-weight: 500;
}

.modern-input::placeholder {
    color: #a0aec0;
    font-weight: 400;
}

.floating-label {
    position: absolute;
    top: 1rem;
    left: 0.5rem;
    color: #a0aec0;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    pointer-events: none;
    background: white;
    padding: 0 0.25rem;
}

.modern-input:focus + .floating-label,
.modern-input:not(:placeholder-shown) + .floating-label {
    top: 0.25rem;
    font-size: 0.75rem;
    color: #48bb78;
    font-weight: 600;
}

.input-help {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(72, 187, 120, 0.05);
    border-radius: 12px;
    border-left: 4px solid #48bb78;
}

.input-help i {
    color: #48bb78;
    font-size: 0.9rem;
}

.input-help span {
    color: #4a5568;
    font-size: 0.85rem;
    font-weight: 500;
}

.form-preview {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f7fafc;
}

.preview-header i {
    color: #48bb78;
    font-size: 1rem;
}

.preview-header span {
    color: #2d3748;
    font-weight: 600;
    font-size: 0.95rem;
}

.preview-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.preview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f7fafc;
    border-radius: 12px;
}

.preview-label {
    color: #4a5568;
    font-weight: 500;
    font-size: 0.9rem;
}

.preview-value {
    color: #2d3748;
    font-weight: 600;
    font-size: 0.9rem;
    background: #48bb78;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.modern-footer {
    background: white;
    border-top: 1px solid #e2e8f0;
    padding: 1.5rem 2.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.modern-btn {
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    border: 2px solid #e2e8f0;
    background: white;
    color: #4a5568;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 120px;
    justify-content: center;
}

.modern-btn:hover {
    border-color: #cbd5e0;
    background: #f7fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.modern-btn-primary {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
    color: white;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 140px;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
}

.modern-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4);
}

.modern-btn-primary:active {
    transform: translateY(0);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .modern-header {
        padding: 1.5rem;
    }
    
    .modern-body {
        padding: 1.5rem;
    }
    
    .modern-footer {
        padding: 1rem 1.5rem;
        flex-direction: column;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .icon-container {
        width: 50px;
        height: 50px;
    }
    
    .modal-title {
        font-size: 1.5rem;
    }
}

/* Animation for modal entrance */
.modal.fade .modal-dialog {
    transform: scale(0.8) translateY(-50px);
    transition: all 0.3s ease;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
}

/* Focus states */
.modern-input:focus {
    box-shadow: none;
}

/* Custom scrollbar for modal body */
.modern-body::-webkit-scrollbar {
    width: 6px;
}

.modern-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.modern-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.modern-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Enhanced input states */
.input-container.focused {
    border-color: #667eea;
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.input-container.has-value {
    border-color: #48bb78;
    box-shadow: 0 4px 20px rgba(72, 187, 120, 0.15);
}

.input-container.has-value .input-icon {
    color: #48bb78;
}

/* Preview value enhancements */
.preview-value.has-value {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(72, 187, 120, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(72, 187, 120, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(72, 187, 120, 0);
    }
}

/* Button loading states */
.modern-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.modern-btn-primary:disabled {
    background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
    box-shadow: none;
}

/* Enhanced modal animations */
.modal.fade .modal-dialog {
    transform: scale(0.8) translateY(-50px) rotateX(10deg);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0) rotateX(0deg);
}

/* Input focus animations */
.modern-input:focus + .floating-label {
    animation: labelFloat 0.3s ease-out;
}

@keyframes labelFloat {
    0% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-2px) scale(1.05);
    }
    100% {
        transform: translateY(0) scale(1);
    }
}

/* Hover effects for interactive elements */
.input-container:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
}

.form-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transition: all 0.3s ease;
}

/* Success state animations */
.modern-btn-primary:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.3);
}

/* Error state styling */
.input-container.error {
    border-color: #e53e3e;
    box-shadow: 0 4px 20px rgba(229, 62, 62, 0.15);
}

.input-container.error .input-icon {
    color: #e53e3e;
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spinner {
    animation: spin 1s linear infinite;
}

/* Modern Add Button */
.modern-add-btn {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
    border-radius: 15px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.modern-add-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.modern-add-btn:hover::before {
    left: 100%;
}

.modern-add-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(72, 187, 120, 0.4);
}

.modern-add-btn:active {
    transform: translateY(-1px);
}

/* Enhanced table styling */
.table-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
}

/* DataTable customization */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.5rem;
    font-size: 0.9rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #48bb78 !important;
    color: white !important;
    border-color: #48bb78;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #48bb78 !important;
    color: white !important;
    border-color: #48bb78;
}

/* Button group styling */
.btn-group .btn {
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

<!-- Add back button before the header container -->
<a href="./camarones_configuracion.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="container text-center">
  <h3  class="container mt-4 text-white" class="collapse" id="section-configuracion-etapas-camarones">
  CONFIGURACION ETAPAS CAMARONES
  </h3>
</div> 
<!-- New Entry Modal Configuracion Etapas -->

<!-- Add New Etapa Button -->
<div class="container my-3 text-center">
  <button type="button" class="btn btn-success text-center modern-add-btn" data-bs-toggle="modal" data-bs-target="#newEntryModal">
    <i class="fas fa-plus-circle me-2"></i>Nueva Etapa
  </button>
</div>

<div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content modern-modal">
          <div class="modal-header modern-header">
              <div class="header-content">
                  <div class="icon-container">
                      <i class="fas fa-layer-group"></i>
                  </div>
                  <div class="title-section">
                      <h5 class="modal-title" id="newEntryModalLabel">Nueva Etapa Camarones</h5>
                      <p class="modal-subtitle">Configure una nueva etapa para el sistema camarones</p>
                  </div>
              </div>
              <button type="button" class="btn-close modern-close" data-bs-dismiss="modal" aria-label="Close">
                  <i class="fas fa-times"></i>
              </button>
          </div>
          <div class="modal-body modern-body">
              <form id="newEtapaForm" class="modern-form">
                  <input type="hidden" id="new_id" name="id" value="">
                  
                  <div class="form-group modern-form-group">
                      <div class="input-container">
                          <div class="input-icon">
                              <i class="fas fa-layer-group"></i>
                          </div>
                          <div class="input-wrapper">
                              <input type="text" class="form-control modern-input" id="new_etapa" name="etapa" placeholder="Ej: CRECIMIENTO, ENGORDE, etc." required>
                              <label for="new_etapa" class="floating-label">Nombre de la Etapa Camarones</label>
                          </div>
                      </div>
                      <div class="input-help">
                          <i class="fas fa-info-circle"></i>
                          <span>Ingrese un nombre descriptivo para la nueva etapa camarones</span>
                      </div>
                  </div>
                  
                  <div class="form-preview">
                      <div class="preview-header">
                          <i class="fas fa-eye"></i>
                          <span>Vista Previa</span>
                      </div>
                      <div class="preview-content">
                          <div class="preview-item">
                              <span class="preview-label">Etapa:</span>
                              <span class="preview-value" id="preview-etapa">-</span>
                          </div>
                      </div>
                  </div>
              </form>
          </div>
          <div class="modal-footer modern-footer">
              <button type="button" class="btn btn-outline-secondary modern-btn" data-bs-dismiss="modal">
                  <i class="fas fa-times"></i>
                  <span>Cancelar</span>
              </button>
              <button type="button" class="btn btn-primary modern-btn-primary" id="saveNewEtapa">
                  <i class="fas fa-save"></i>
                  <span>Guardar Etapa</span>
              </button>
          </div>
      </div>
  </div>
</div>
  
  <!-- DataTable for cac_etapas records -->
  
<div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="etapaTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                    <th class="text-center">Acciones</th>
                    <th class="text-center">Etapas Camarones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                      $etapaQuery = "SELECT * FROM cac_etapas";

                      $stmt = $conn->prepare($etapaQuery);
                      $stmt->execute();
                      $etapasData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      if (empty($etapasData)) {
                          echo "<tr><td colspan='5' class='text-center'>No hay registros disponibles</td></tr>";
                      } else {
                          foreach ($etapasData as $row) {
                              echo "<tr>";
                              
                              // Column 0: Actions
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              
                              echo '        <button class="btn btn-danger btn-sm delete-etapa" 
                                              data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                              data-etapa="' . htmlspecialchars($row['cac_etapas_nombre'] ?? '') . '"
                                              title="Eliminar Configuracion Etapa">
                                              <i class="fas fa-trash"></i>
                                          </button>';
                              echo '    </div>';
                              echo '</td>';
                              
                              // Column 1: Etapas Camarones
                              echo "<td>" . htmlspecialchars($row['cac_etapas_nombre'] ?? '') . "</td>";

                              echo "</tr>";
                          }
                      }
                  ?>
              </tbody>
          </table>
      </div>
</div>


<!-- Initialize DataTable for Camarones Etapas -->
<script>
$(document).ready(function() {
    $('#etapaTable').DataTable({
        // Set initial page length
        pageLength: 15,
        
        // Configure length menu options
        lengthMenu: [
            [15, 25, 50, 100, -1],
            [15, 25, 50, 100, "Todos"]
        ],
        
        // Order by Etapas Camarones column ascending (column index 1)
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
        
        // Column specific settings - Updated indices
        columnDefs: [
             {
                 targets: [0], // Acciones column
                 orderable: false,
                 searchable: false
             },
            {
                targets: [1], // Etapas Camarones column
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
    
    console.log('Modal element:', newEntryModalElement);
    console.log('Modal instance:', newEntryModalInstance);
    
    // Initialize preview functionality
    initializePreview();
    
    // Note: editetapasModal is created dynamically later, so no need to initialize here.

    // Handle new entry form submission
    $('#saveNewEtapa').click(function() {
        console.log('Save button clicked');
        
        // Validate the form
        var form = document.getElementById('newEtapaForm');
        console.log('Form element:', form);
        console.log('Form validity:', form.checkValidity());
        
        // Manual validation as backup
        var etapaValue = $('#new_etapa').val();
        console.log('Manual validation - etapa value:', etapaValue);
        
        if (!etapaValue || etapaValue.trim() === '') {
            console.log('Manual validation failed - empty value');
            Swal.fire({
                title: 'Error de validación',
                text: 'Por favor ingrese el nombre de la etapa',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        if (!form.checkValidity()) {
            console.log('Form validation failed');
            form.reportValidity();
            return;
        }
        
        console.log('Form validation passed');
        
        // Get form data
        var formData = {
            etapa: $('#new_etapa').val()
        };
        
        // Update preview
        updatePreview(formData.etapa);
        
        // Debug logging
        console.log('Form data extracted:', formData);
        console.log('Input value:', $('#new_etapa').val());
        console.log('Input element:', $('#new_etapa')[0]);
        console.log('Form element:', $('#newEtapaForm')[0]);
        console.log('Form elements:', $('#newEtapaForm input, #newEtapaForm select, #newEtapaForm textarea'));
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar la etapa ${formData.etapa}?`,
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
                        var ajaxData = {
                            action: 'insert',
                            etapas: formData.etapa
                        };
                        
                        console.log('Sending AJAX request with data:', ajaxData);
                        console.log('AJAX URL:', 'process_configuracion_etapas_camarones.php');
                        console.log('Form data object:', formData);
                        console.log('Etapa value from formData:', formData.etapa);
                        console.log('Etapa value directly from input:', $('#new_etapa').val());
                        
                        $.ajax({
                            url: 'process_configuracion_etapas_camarones.php',
                            type: 'POST',
                            data: ajaxData,
                    success: function(response) {
                        console.log('Success response:', response);
                        
                        // Reset the form
                        $('#newEtapaForm')[0].reset();
                        
                        // Close the modal
                        newEntryModalInstance.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de la etapa ha sido guardado correctamente',
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
                            action: 'insert',
                            etapa: formData.etapa
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
    $('.edit-etapa').click(function() {
        var id = $(this).data('id');
        var etapa = $(this).data('etapa');

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

        // Edit Configuracion Etapa Modal dialog for editing
        var modalHtml = `
        <div class="modal fade" id="editEtapaModal" tabindex="-1" aria-labelledby="editEtapaModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEtapaModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Etapa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editEtapaForm">
                            <input type="hidden" id="edit_id" name="id" value="${id}">
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-syringe"></i>
                                        <label for="edit_etapa" class="form-label">Etapa</label>
                                        <select class="form-select" id="edit_etapa" name="etapa" required>
                                            <option value="">Seleccionar etapa</option>
                                            <?php
                                            // Fetch distinct names from the database
                                            $sql_names = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre ASC";
                                            $stmt_names = $conn->prepare($sql_names);
                                            $stmt_names->execute();
                                            $names = $stmt_names->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($names as $name_row) {
                                                echo '<option value="' . htmlspecialchars($name_row['cac_etapas_nombre']) . '">' . htmlspecialchars($name_row['cac_etapas_nombre']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditEtapa">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editEtapaModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editEtapaModal'));
        editModal.show();
        
        // Handle save button click
        $('#saveEditEtapa').click(function() {
            // Create a form object to properly validate
            var form = document.getElementById('editEtapaForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            var formData = {
                id: $('#edit_id').val(),
                etapa: $('#edit_etapa').val()
            };
            
            console.log('Save changes clicked. Form Data being sent:', formData); // Debug log 2
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar la configuracion de la etapa ${formData.etapa}?`,
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
                        url: 'process_configuracion_etapas_camarones.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            etapa: formData.etapa
                        },
                        success: function(response) {
                            console.log('Update success response:', response);
                            // Close the modal
                            editModal.hide();
                            
                            // Show success message
                            Swal.fire({
                                title: '¡Actualización exitosa!',
                                text: 'La configuracion de la etapa ha sido actualizada correctamente',
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
                                etapa: formData.etapa
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
    $('.delete-etapa').click(function() {
        var id = $(this).data('id');
        var etapa = $(this).data('etapa');
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `¿Está seguro de que desea eliminar la etapa "${etapa}"? Esta acción no se puede deshacer.`,
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
                    url: 'process_configuracion_etapas_camarones.php',
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
                            text: 'La configuracion de la etapa ha sido eliminada correctamente',
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
    
    // Preview functionality
    function initializePreview() {
        // Add input event listener for real-time preview
        $('#new_etapa').on('input', function() {
            var value = $(this).val();
            updatePreview(value);
        });
        
        // Initialize preview on modal show
        $('#newEntryModal').on('shown.bs.modal', function() {
            updatePreview('');
        });
    }
    
    function updatePreview(etapaValue) {
        if (etapaValue && etapaValue.trim() !== '') {
            $('#preview-etapa').text(etapaValue.trim().toUpperCase());
            $('#preview-etapa').addClass('has-value');
        } else {
            $('#preview-etapa').text('-');
            $('#preview-etapa').removeClass('has-value');
        }
    }
    
    // Enhanced form validation with visual feedback
    $('#new_etapa').on('focus', function() {
        $(this).closest('.input-container').addClass('focused');
    });
    
    $('#new_etapa').on('blur', function() {
        $(this).closest('.input-container').removeClass('focused');
        if ($(this).val().trim() !== '') {
            $(this).closest('.input-container').addClass('has-value');
        } else {
            $(this).closest('.input-container').removeClass('has-value');
        }
    });
    
    // Add loading states to buttons
    $('#saveNewEtapa').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.find('span').text();
        var originalIcon = $btn.find('i').attr('class');
        
        $btn.prop('disabled', true);
        $btn.find('span').text('Guardando...');
        $btn.find('i').attr('class', 'fas fa-spinner fa-spin');
        
        // Re-enable button after a delay (in case of validation failure)
        setTimeout(function() {
            $btn.prop('disabled', false);
            $btn.find('span').text(originalText);
            $btn.find('i').attr('class', originalIcon);
        }, 3000);
    });
});
</script>
</body>
</html>