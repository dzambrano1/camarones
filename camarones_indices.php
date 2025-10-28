<?php

require_once './pdo_conexion.php';  // Go up one directory since inventario_camarones.php is in the camarones folder
// Now you can use $conn for database queries

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Indices Camaroneros</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/Avegram_Logo.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!--Bootstrap 5 Css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">



<!-- Include Chart.js and Chart.js DataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- Add these in the <head> section, after your existing CSS/JS links -->

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

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<!-- Custom Modal Styles -->
<link rel="stylesheet" href="./camarones.css">

<style>
    /* Dashboard Grid Layout */
    .dashboard-container {
        padding: 10px;
        background: linear-gradient(135deg, #f0f8f0 0%, #c8e6c9 100%);
        min-height: 100vh;
    }
    
    .dashboard-header {
        text-align: center;
        margin-bottom: 20px;
        padding: 15px 0;
        background: linear-gradient(135deg,rgb(243, 167, 2) 0%,rgb(255, 255, 0) 100%);
        color: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(46, 125, 50, 0.3);
    }
    
    .dashboard-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .dashboard-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    /* Chart Card Styling */
    .chart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .chart-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .chart-card-header {
        padding: 20px 25px 15px;
        background: linear-gradient(135deg,rgb(243, 167, 2) 0%,rgb(255, 255, 0) 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .chart-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }
    
    .chart-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 1;
        color: white !important;
    }
    
    .chart-card-title i {
        color: white !important;
    }
    
    .chart-card-body {
        padding: 20px;
        height: 300px;
        position: relative;
    }
    
    .chart-canvas-container {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .chart-canvas-container canvas {
        width: 100% !important;
        height: 100% !important;
    }
    
    /* Action Buttons */
    .chart-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        gap: 8px;
        z-index: 10;
    }
    
    .chart-btn {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .chart-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        color: white !important;
    }
    
    .chart-btn.expand-btn {
        color: white !important;
    }
    
    .chart-btn.pdf-btn {
        color: white !important;
    }
    
    /* Full Screen Modal */
    .chart-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .chart-modal.active {
        display: flex;
        opacity: 1;
        align-items: center;
        justify-content: center;
    }
    
    .chart-modal-content {
        background: white;
        border-radius: 20px;
        width: 95vw;
        height: 90vh;
        max-width: 1200px;
        position: relative;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        transform: scale(0.8);
        transition: transform 0.3s ease;
    }
    
    .chart-modal.active .chart-modal-content {
        transform: scale(1);
    }
    
    .chart-modal-header {
        padding: 25px 30px;
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chart-modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
        color: white !important;
    }
    
    .chart-modal-title i {
        color: white !important;
    }
    
    .chart-modal-close {
        background: rgba(255,255,255,0.2);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .chart-modal-close:hover {
        background: rgba(255,255,255,0.3);
        transform: rotate(90deg);
    }
    
    .chart-modal-body {
        flex: 1;
        padding: 30px;
        position: relative;
    }
    
    .chart-modal-canvas {
        width: 100% !important;
        height: 100% !important;
    }
    
    /* Loading States */
    .chart-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #666;
    }
    
    .chart-loading .spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #2e7d32;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .dashboard-title {
            font-size: 1rem;
        }
        
        .chart-card-body {
            height: 250px;
        }
        
        .chart-modal-content {
            width: 98vw;
            height: 95vh;
            border-radius: 15px;
        }
        
        .chart-modal-header {
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        
        .chart-modal-title {
            font-size: 1.2rem;
        }
        
        .chart-modal-body {
            padding: 20px;
        }
    }
    
    @media (max-width: 480px) {
        .charts-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .chart-card-body {
            height: 200px;
        }
        
        .dashboard-container {
            padding: 15px;
        }
    }
    
    /* Animation for chart cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .chart-card {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }
    
    .chart-card:nth-child(1) { animation-delay: 0.1s; }
    .chart-card:nth-child(2) { animation-delay: 0.2s; }
    .chart-card:nth-child(3) { animation-delay: 0.3s; }
    .chart-card:nth-child(4) { animation-delay: 0.4s; }
    .chart-card:nth-child(5) { animation-delay: 0.5s; }
    .chart-card:nth-child(6) { animation-delay: 0.6s; }
    .chart-card:nth-child(7) { animation-delay: 0.7s; }
    .chart-card:nth-child(8) { animation-delay: 0.8s; }
    .chart-card:nth-child(9) { animation-delay: 0.9s; }
    .chart-card:nth-child(10) { animation-delay: 1.0s; }
    .chart-card:nth-child(11) { animation-delay: 1.1s; }
    .chart-card:nth-child(12) { animation-delay: 1.2s; }
    .chart-card:nth-child(13) { animation-delay: 1.3s; }
    .chart-card:nth-child(14) { animation-delay: 1.4s; }
    .chart-card:nth-child(15) { animation-delay: 1.5s; }
    .chart-card:nth-child(16) { animation-delay: 1.6s; }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(to right, #28a745, #20c997);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .modal-header .btn-close {
        color: white;
        opacity: 0.8;
        transition: opacity 0.3s;
        filter: brightness(0) invert(1);
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        padding: 1.75rem;
        background-color: #f8f9fa;
    }
    
    .modal-footer {
        border-top: none;
        padding: 1rem 1.75rem 1.5rem;
        background-color: #f8f9fa;
    }
    
    /* Form Elements */
    .modal .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .modal .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.75rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    .modal .form-control:hover:not(:focus) {
        border-color: #adb5bd;
    }
    
    /* Buttons */
    .modal .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s;
    }
    
    .modal .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .modal .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .modal .btn-success:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    .modal .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .modal .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }
    
    .modal .btn-secondary:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    /* Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Modal Backdrop */
    .modal-backdrop.show {
        opacity: 0.7;
        backdrop-filter: blur(3px);
    }
    
    /* Input Group */
    .input-group {
        margin-bottom: 1rem;
    }
    
    /* Input Group Text */
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #28a745;
    }
    
    /* Focused Form Group Effect */
    .modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    /* Modal Highlight Animation on Open */
    @keyframes modalHighlight {
        0% {
            box-shadow: 0 0 0 rgba(40, 167, 69, 0);
        }
        50% {
            box-shadow: 0 0 30px rgba(40, 167, 69, 0.3);
        }
        100% {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
    }
    
    .modal.show .modal-content {
        animation: modalHighlight 0.5s ease forwards;
    }
    
    /* Hover effect for input groups */
    .modal .input-group:hover .input-group-text {
        background-color: #e9ecef;
        transition: background-color 0.3s;
    }
    
    /* Readonly fields styling */
    .modal input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    
    /* Form validation styles */
    .modal .form-control:invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    
    /* Modal title icon */
    .modal-title i {
        margin-right: 8px;
    }

    /* Back to Top Button Styling */
    .back-to-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .back-to-top:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .back-to-top {
            bottom: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }

    /* Chart container responsive styling */
    .chart-container {
        position: relative;
        height: min(400px, 50vh);
        width: 100%;
        margin: auto;
    }

    /* Export button styling */
    #exportMilkRevenuePDF {
        transition: all 0.3s ease;
    }

    #exportMilkRevenuePDF:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    #exportMilkRevenuePDF:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

.button-label {
    display: block;
    text-align: center;
    font-size: 0.7rem;
    width: 100%;
}


    .error-message {
        background-color: #ffebee;
        color: #c62828;
        padding: 10px;
        margin: 10px 0;
        border-radius: 20px;
        border-bottom-left-radius: 5px;
    }

    .full-width-button {
        width: 100% !important;
        display: block !important;
        box-sizing: border-box !important;
    }

    /* Initial system message style */
</style>

</head>
<body>
<!-- Navigation Title -->

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
            <button onclick="window.location.href='./camarones_configuracion.php'" class="icon-button">
                <img src="./images/configuracion.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">CONFIG</span>
        </div>
</div>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-chart-bar me-3"></i>
            Tablero de Control Indices Camarones
        </h1>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        

        <!-- Camarones Revenue Chart Card -->
        <div class="chart-card" data-chart="camaronesRevenue">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-fish"></i>
                    Ingresos Ventas Camarones
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('camaronesRevenue')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('camaronesRevenue')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="camaronesRevenueChart"></canvas>
                    <div id="camaronesRevenueLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Concentrado Expense Chart Card -->
        <div class="chart-card" data-chart="concentradoExpense">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-seedling"></i>
                    Gastos en Concentrado
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('concentradoExpense')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('concentradoExpense')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="concentradoExpenseChart"></canvas>
                    <div id="concentradoExpenseLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Harinas Proteicas Expense Chart Card -->
        <div class="chart-card" data-chart="harinasProteicasExpense">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-tint"></i>
                    Gastos en Harinas Proteicas
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('harinasProteicasExpense')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('harinasProteicasExpense')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="harinasProteicasExpenseChart"></canvas>
                    <div id="harinasProteicasExpenseLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fermentados Expense Chart Card -->
        <div class="chart-card" data-chart="fermentadosExpense">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-fill-drip"></i>
                    Gastos en Fermentados
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('fermentadosExpense')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('fermentadosExpense')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="fermentadosExpenseChart"></canvas>
                    <div id="fermentadosExpenseLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de Temperatura Chart Card -->
        <div class="chart-card" data-chart="temperaturaRegistros">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Registros de Temperatura
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('temperaturaRegistros')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('temperaturaRegistros')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="temperaturaRegistrosChart"></canvas>
                    <div id="temperaturaRegistrosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de salinidad Chart Card -->
        <div class="chart-card" data-chart="pHGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Salinidad (NaCl)
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('salinidadGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('salinidadGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="salinidadGastosChart"></canvas>
                    <div id="salinidadGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de pH Chart Card -->
        <div class="chart-card" data-chart="pHGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    pH
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('pHGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('pHGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="pHGastosChart"></canvas>
                    <div id="pHGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de Oxigeno Chart Card -->
        <div class="chart-card" data-chart="oxigenoGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Oxigeno (O2)
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('oxigenoGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('oxigenoGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="oxigenoGastosChart"></canvas>
                    <div id="oxigenoGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de Amoniacos Chart Card -->
        <div class="chart-card" data-chart="amoniacosGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Amoniacos (NH3/NH4)
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('amoniacosGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('amoniacosGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="amoniacosGastosChart"></canvas>
                    <div id="amoniacosGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de Nitritos Chart Card -->
        <div class="chart-card" data-chart="nitritosGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Nitritos (NO2)
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('nitritosGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('nitritosGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="nitritosGastosChart"></canvas>
                    <div id="nitritosGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de Alcalinidad Chart Card -->
        <div class="chart-card" data-chart="alcalinidadGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Alcalinidad (CaCO₃)
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('alcalinidadGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('alcalinidadGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="alcalinidadGastosChart"></canvas>
                    <div id="alcalinidadGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
                <!-- Registros de Transparencia Chart Card -->
                <div class="chart-card" data-chart="transparenciaGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Transparencia
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('transparenciaGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('transparenciaGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="transparenciaGastosChart"></canvas>
                    <div id="transparenciaGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Registros de Redox Chart Card -->
        <div class="chart-card" data-chart="redoxGastos">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-water"></i>
                    Redox
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('redoxGastos')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('redoxGastos')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="redoxGastosChart"></canvas>
                    <div id="redoxGastosLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Variable Costs Chart Card -->
        <div class="chart-card" data-chart="variableCosts">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-calculator"></i>
                    Total Costos Variables
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('variableCosts')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('variableCosts')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="variableCostsChart"></canvas>
                    <div id="variableCostsLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Purchases Chart Card -->
        <div class="chart-card" data-chart="purchases">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-shopping-cart"></i>
                    Compras Mensuales de Animales
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('purchases')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('purchases')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="purchasesChart"></canvas>
                    <div id="purchasesLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Deaths Chart Card -->
        <div class="chart-card" data-chart="deaths">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-cross"></i>
                    Decesos Mensuales Camarones
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('deaths')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('deaths')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="deathsChart"></canvas>
                    <div id="deathsLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gross Profit Summary Chart Card -->
        <div class="chart-card" data-chart="grossProfitSummary">
            <div class="chart-card-header">
                <h5 class="chart-card-title">
                    <i class="fas fa-chart-bar"></i>
                    Resumen Financiero
                </h5>
                <div class="chart-actions">
                    <button class="chart-btn expand-btn" title="Expandir" onclick="expandChart('grossProfitSummary')">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="chart-btn pdf-btn" title="Exportar PDF" onclick="exportChartPDF('grossProfitSummary')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="chart-canvas-container">
                    <canvas id="grossProfitSummaryChart"></canvas>
                    <div id="grossProfitSummaryLoading" class="chart-loading">
                        <div class="spinner"></div>
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Full Screen Chart Modal -->
<div id="chartModal" class="chart-modal">
    <div class="chart-modal-content">
        <div class="chart-modal-header">
            <h4 class="chart-modal-title" id="modalChartTitle">
                <i class="fas fa-chart-bar"></i>
                Gráfico
            </h4>
            <button class="chart-modal-close" onclick="closeChartModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chart-modal-body">
            <canvas id="modalChart"></canvas>
        </div>
    </div>
</div>

<!-- Librerias -->
<!-- Bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<!-- Librerias -->
<!-- Bootstrap  -->
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Popper Js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>

<!-- Ion Icon Js -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Back to top button -->
<button id="backToTop" class="back-to-top" onclick="scrollToTop()" title="Volver arriba">
    <div class="arrow-up"><i class="fa-solid fa-arrow-up"></i></div>
</button>

<script>
// Back to top functionality
window.onscroll = function() {
    const backToTopButton = document.getElementById("backToTop");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        backToTopButton.style.display = "flex";
    } else {
        backToTopButton.style.display = "none";
    }
};

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Dashboard Chart Management
let chartInstances = {};
let currentExpandedChart = null;

// Chart configurations and data
const chartConfigs = {
    // Ingresos
    camaronesRevenue: {
        title: 'Ingresos Ventas Camarones',
        icon: 'fas fa-egg',
        dataUrl: './get_camarones_revenue_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    concentradoExpense: {
        title: 'Gastos en Concentrado',
        icon: 'fas fa-seedling',
        dataUrl: './get_concentrado_expense_data.php',
        color: 'rgba(0, 123, 255, 0.8)',
        borderColor: '#007bff'
    },
    harinasProteicasExpense: {
        title: 'Gastos en Harinas Proteicas',
        icon: 'fas fa-tint',
        dataUrl: './get_harinas_expense_data.php',
        color: 'rgba(255, 193, 7, 0.8)',
        borderColor: '#ffc107'
    },
    fermentadosExpense: {
        title: 'Gastos en Fermentados',
        icon: 'fas fa-fill-drip',
        dataUrl: './get_fermentados_expense_data.php',
        color: 'rgba(111, 66, 193, 0.8)',
        borderColor: '#6f42c1'
    },
    temperaturaRegistros: {
        title: 'Registros de Temperatura',
        icon: 'fas fa-water',
        dataUrl: './get_temperatura_registros_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    salinidadGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_salinidad_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    pHGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_ph_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    oxigenoGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_oxigeno_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    nitritosGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_nitritos_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    amoniacosGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_amoniaco_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    alcalinidadGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_alcalinidad_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    transparenciaGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_transparencia_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    redoxGastos: {
        title: 'Gastos y Niveles',
        icon: 'fas fa-water',
        dataUrl: './get_redox_gastos_data.php',
        color: 'rgba(210, 180, 140, 0.8)',
        borderColor: '#d2b48c'
    },
    variableCosts: {
        title: 'Total Costos Variables',
        icon: 'fas fa-calculator',
        dataUrl: './get_variable_costs_data.php',
        color: 'rgba(220, 53, 69, 0.8)',
        borderColor: '#dc3545'
    },
purchases: {
    title: 'Compras Mensuales Pls',
    icon: 'fas fa-shopping-cart',
    dataUrl: './get_purchases_data.php',
    color: 'rgba(102, 16, 242, 0.8)',
    borderColor: '#6610f2'
},
    deaths: {
        title: 'Decesos Mensuales Camarones',
        icon: 'fas fa-cross',
        dataUrl: './get_deceso_data.php',
        color: 'rgba(108, 117, 125, 0.8)',
        borderColor: '#6c757d'
    },
    grossProfitSummary: {
        title: 'Resumen Financiero',
        icon: 'fas fa-chart-bar',
        dataUrl: './get_gross_profit_summary_data.php',
        color: 'rgba(75, 192, 192, 0.8)',
        borderColor: '#4bc0c0'
    }
};

// Generic chart loading function
async function loadChart(chartType, canvasId, isModal = false) {
    const config = chartConfigs[chartType];
    if (!config) return;

    const loadingId = `${chartType}Loading`;
    const loadingDiv = document.getElementById(loadingId);
    
    if (loadingDiv) {
        loadingDiv.style.display = 'block';
    }

    try {
        const response = await fetch(config.dataUrl);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        // Check if data is empty (handle both array and object formats)
        const isEmpty = (Array.isArray(data) && data.length === 0) || 
                       (data.labels && data.labels.length === 0) ||
                       (!Array.isArray(data) && !data.labels && Object.keys(data).length === 0);
                       
        if (isEmpty) {
            if (loadingDiv) {
                loadingDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos disponibles</div>';
            }
            return;
        }

        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }

        // Process data based on chart type
        let chartData = processChartData(data, chartType, config);
        
        // Create chart
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart if it exists
        if (chartInstances[canvasId]) {
            chartInstances[canvasId].destroy();
        }

        // Get base options
        let chartOptions = getChartOptions(chartType, isModal);
        
        // Add dual Y-axes for camaronesRevenue chart
        if (chartType === 'camaronesRevenue') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Revenue (USD)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#483D8B'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#483D8B'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Individual Presentations (USD)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#666'
            };
        }

        // Add dual Y-axes for salinidadGastos chart
        if (chartType === 'salinidadGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Salinidad Levels (ppt)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for pHGastos chart
        if (chartType === 'pHGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: false, // pH scale doesn't start at 0
                min: 0,
                max: 14,
                title: {
                    display: true,
                    text: 'pH Levels',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for oxigenoGastos chart
        if (chartType === 'oxigenoGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Oxygen Levels (mg/L)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for nitritosGastos chart
        if (chartType === 'nitritosGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Niveles de Nitritos (mg/L)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Gastos Mensuales ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for amoniacosGastos chart
        if (chartType === 'amoniacosGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Ammonia Levels (mg/L)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for alcalinidadGastos chart
        if (chartType === 'alcalinidadGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Alkalinity Levels (ppm)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for transparenciaGastos chart
        if (chartType === 'transparenciaGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Water Transparency (cm)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for redoxGastos chart
        if (chartType === 'redoxGastos') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: false, // Redox values can be negative
                title: {
                    display: true,
                    text: 'Redox Potential (mV)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#228B22'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#228B22'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Expenses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#FFC107'
            };
        }

        // Add dual Y-axes for purchases chart
        if (chartType === 'purchases') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cumulative Purchases ($)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#ffc107'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#ffc107'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Monthly Purchases ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#6610f2'
            };
        }

        // Add dual Y-axes for deaths chart
        if (chartType === 'deaths') {
            chartOptions.scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cumulative Losses ($)',
                    font: {
                        size: isModal ? 12 : 10,
                        weight: 'bold'
                    },
                    color: '#dc3545'
                },
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#dc3545'
                },
                grid: {
                    drawOnChartArea: false,
                }
            };
            
            // Update primary Y-axis title
            chartOptions.scales.y.title = {
                display: true,
                text: 'Average Monthly Losses ($)',
                font: {
                    size: isModal ? 12 : 10,
                    weight: 'bold'
                },
                color: '#6c757d'
            };
        }

        // Add custom tooltip for camaronesRevenue chart
        if (chartType === 'camaronesRevenue' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const datasetIndex = context.datasetIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo) {
                            let tooltipLines = [`${datasetLabel}: $${context.parsed.y.toLocaleString()} USD`];
                            
                            // If this is the total line, show summary
                            if (datasetLabel.includes('Total')) {
                                tooltipLines.push(`Total Ventas: ${tooltipInfo.total.sales_count}`);
                                tooltipLines.push(`Cantidad Total: ${tooltipInfo.total.total_quantity} `);
                            } else {
                                // Show specific presentation details
                                const presentationName = datasetLabel.replace(' (USD)', '');
                                const presentationData = tooltipInfo.presentations[presentationName];
                                
                                if (presentationData) {
                                    tooltipLines.push(`Ventas: ${presentationData.sales_count}`);
                                    tooltipLines.push(`Cantidad: ${presentationData.total_quantity} `);
                                    tooltipLines.push(`Precio Promedio: $${presentationData.avg_price} USD/kg`);
                                    tooltipLines.push(`Tallas: ${presentationData.sizes}`);
                                }
                            }
                            
                            return tooltipLines;
                        }
                        return `$${context.parsed.y.toLocaleString()} USD`;
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#d2b48c',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: false,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for temperaturaRegistros chart
        if (chartType === 'temperaturaRegistros' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.temperatures) {
                            let tooltipLines = [`${datasetLabel}: ${context.parsed.y}°C`];
                            
                            // Add additional temperature information
                            const temps = tooltipInfo.temperatures;
                            tooltipLines.push(`Registros: ${temps.record_count}`);
                            
                            if (datasetLabel.includes('Superficial')) {
                                tooltipLines.push(`Rango: ${temps.temp_range_superficial}`);
                            } else if (datasetLabel.includes('Fondo')) {
                                tooltipLines.push(`Rango: ${temps.temp_range_fondo}`);
                            }
                            
                            return tooltipLines;
                        }
                        return `${context.parsed.y}°C`;
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#36A2EB',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for salinidadGastos chart
        if (chartType === 'salinidadGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_salinidad) {
                            const expData = tooltipInfo.expenses_and_salinidad;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Mínima')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.min_salinidad_level}`);
                                tooltipLines.push(`Salinidad antes: ${expData.avg_salinidad_antes}`);
                            } else if (datasetLabel.includes('Máxima')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.max_salinidad_level}`);
                                tooltipLines.push(`Salinidad después: ${expData.avg_salinidad_despues}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} ppt`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#FFC107',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for pHGastos chart
        if (chartType === 'pHGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_ph) {
                            const expData = tooltipInfo.expenses_and_ph;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Mínimo')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.min_ph_level}`);
                                tooltipLines.push(`pH antes: ${expData.avg_ph_antes}`);
                            } else if (datasetLabel.includes('Máximo')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.max_ph_level}`);
                                tooltipLines.push(`pH después: ${expData.avg_ph_despues}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y}`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#228B22',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for oxigenoGastos chart
        if (chartType === 'oxigenoGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_oxigeno) {
                            const expData = tooltipInfo.expenses_and_oxigeno;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Antes')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_oxigeno_antes}`);
                                tooltipLines.push(`Rango: ${expData.min_oxigeno_level} - ${expData.max_oxigeno_level}`);
                            } else if (datasetLabel.includes('Después')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_oxigeno_despues}`);
                                tooltipLines.push(`Rango: ${expData.min_oxigeno_level} - ${expData.max_oxigeno_level}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} mg/L`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#FFC107',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for nitritosGastos chart
        if (chartType === 'nitritosGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_nitritos) {
                            const expData = tooltipInfo.expenses_and_nitritos;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Niveles') || datasetLabel.includes('Nitritos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_nitrite_mg_l}`);
                                tooltipLines.push(`Tratamientos en este mes: ${expData.treatment_count}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} mg/L`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#DC143C',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for amoniacosGastos chart
        if (chartType === 'amoniacosGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_amoniaco) {
                            const expData = tooltipInfo.expenses_and_amoniaco;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Antes')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_amoniaco_antes}`);
                                tooltipLines.push(`Rango: ${expData.min_amoniaco_level} - ${expData.max_amoniaco_level}`);
                            } else if (datasetLabel.includes('Después')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_amoniaco_despues}`);
                                tooltipLines.push(`Rango: ${expData.min_amoniaco_level} - ${expData.max_amoniaco_level}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} mg/L`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#228B22',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for alcalinidadGastos chart
        if (chartType === 'alcalinidadGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_alcalinidad) {
                            const expData = tooltipInfo.expenses_and_alcalinidad;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Antes')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_alcalinidad_antes}`);
                                tooltipLines.push(`Rango: ${expData.min_alcalinidad_level} - ${expData.max_alcalinidad_level}`);
                            } else if (datasetLabel.includes('Después')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_alcalinidad_despues}`);
                                tooltipLines.push(`Rango: ${expData.min_alcalinidad_level} - ${expData.max_alcalinidad_level}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} ppm`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#FFC107',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for transparenciaGastos chart
        if (chartType === 'transparenciaGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_transparencia) {
                            const expData = tooltipInfo.expenses_and_transparencia;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Antes')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_transparencia_antes}`);
                                tooltipLines.push(`Rango: ${expData.min_transparencia_level} - ${expData.max_transparencia_level}`);
                            } else if (datasetLabel.includes('Después')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_transparencia_despues}`);
                                tooltipLines.push(`Rango: ${expData.min_transparencia_level} - ${expData.max_transparencia_level}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} cm`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#228B22',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Add custom tooltip for redoxGastos chart
        if (chartType === 'redoxGastos' && data.tooltipData) {
            chartOptions.plugins = chartOptions.plugins || {};
            chartOptions.plugins.tooltip = {
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const index = context.dataIndex;
                        const tooltipInfo = data.tooltipData[index];
                        const datasetLabel = context.dataset.label;
                        
                        if (tooltipInfo && tooltipInfo.expenses_and_redox) {
                            const expData = tooltipInfo.expenses_and_redox;
                            let tooltipLines = [];
                            
                            if (datasetLabel.includes('Gastos')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.total_expenses}`);
                                tooltipLines.push(`Tratamientos: ${expData.treatment_count}`);
                                tooltipLines.push(`Promedio por tratamiento: ${expData.avg_expense_per_treatment}`);
                            } else if (datasetLabel.includes('Antes')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_redox_antes}`);
                                tooltipLines.push(`Rango: ${expData.min_redox_level} - ${expData.max_redox_level}`);
                            } else if (datasetLabel.includes('Después')) {
                                tooltipLines.push(`${datasetLabel}: ${expData.avg_redox_despues}`);
                                tooltipLines.push(`Rango: ${expData.min_redox_level} - ${expData.max_redox_level}`);
                            }
                            
                            return tooltipLines;
                        }
                        
                        // Fallback formatting
                        if (datasetLabel.includes('Gastos')) {
                            return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                        } else {
                            return `${datasetLabel}: ${context.parsed.y} mV`;
                        }
                    }
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#DC143C',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: true,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            };
        }

        // Create new chart
        chartInstances[canvasId] = new Chart(ctx, {
            type: getChartType(chartType),
            data: chartData,
            options: chartOptions
        });

    } catch (error) {
        console.error(`Error loading ${chartType} chart:`, error);
        if (loadingDiv) {
            loadingDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Error al cargar datos</div>';
        }
    }
}

// Process chart data based on type
function processChartData(data, chartType, config) {
    let labels, values, additionalData = {};

    switch(chartType) {

        case 'camaronesRevenue':
            // Our endpoint already returns the correct format with proper colors
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors from backend
                };
            }
            // Fallback if data is in array format
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_revenue)) : [];
            break;

        case 'purchases':
            // Purchases data with dual y-axis (monthly bars + cumulative line)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and both series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.monthly_amount)) : [];
            break;

        case 'deaths':
            // Deaths data with dual y-axis (monthly avg bars + cumulative loss line)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and both series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.avg_monthly_monto)) : [];
            break;

        case 'temperaturaRegistros':
            // Temperature data already returns the correct format with proper colors and two series
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and both temperature series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.avg_temp_superficial)) : [];
            break;

        case 'salinidadGastos':
            // Salinidad expenses data with dual y-axis (expenses + min/max salinidad levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'pHGastos':
            // pH expenses data with dual y-axis (expenses + min/max pH levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'oxigenoGastos':
            // Oxigeno expenses data with dual y-axis (expenses + antes/despues oxygen levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'nitritosGastos':
            // Nitritos expenses data with dual y-axis (expenses + antes/despues nitrite levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'amoniacosGastos':
            // Amoniaco expenses data with dual y-axis (expenses + antes/despues ammonia levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'alcalinidadGastos':
            // Alcalinidad expenses data with dual y-axis (expenses + antes/despues alkalinity levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'transparenciaGastos':
            // Transparencia expenses data with dual y-axis (expenses + antes/despues transparency levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'redoxGastos':
            // Redox expenses data with dual y-axis (expenses + antes/despues redox potential levels)
            if (data.labels && data.datasets) {
                return {
                    labels: data.labels,
                    datasets: data.datasets  // Keep original colors and all three series from backend
                };
            }
            // Fallback if data is in array format (shouldn't happen with our format)
            labels = data.map ? data.map(item => item.month) : [];
            values = data.map ? data.map(item => parseFloat(item.total_expenses)) : [];
            break;

        case 'pesoRevenue':
            labels = data.map(item => {
                const [year, weekPart] = item.week.split('-W');
                const weekNumber = parseInt(weekPart);
                return `${year} S${weekNumber}`;
            });
            values = data.map(item => parseFloat(item.total_value));
            break;

        case 'revenuePollosEngorde':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseFloat(item.total_revenue));
            break;

        case 'huevoRevenue':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseFloat(item.total_revenue));
            break;

        case 'totalFarmIncome':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            // Use total_revenue which combines huevo and broiler revenue
            values = data.map(item => parseFloat(item.total_revenue || 0));
            // Store breakdown data for tooltip
            additionalData.huevoRevenue = data.map(item => parseFloat(item.huevo_revenue || 0));
            additionalData.broilerRevenue = data.map(item => parseFloat(item.broiler_revenue || 0));
            break;
            
        case 'concentradoExpense':
        case 'harinasProteicasExpense':
        case 'fermentadosExpense':
        case 'waterTreatmentExpense':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseFloat(item.total_expense));
            break;
            
        case 'vaccineCosts':
            // Handle pie chart data for vaccine costs by percentage
            if (data.data && Array.isArray(data.data)) {
                labels = data.data.map(item => item.label);
                values = data.data.map(item => parseFloat(item.value));
                additionalData.isPieChart = true;
                additionalData.percentages = data.data.map(item => parseFloat(item.percentage));
                additionalData.vaccineTypes = data.data.map(item => item.vaccine_type);
                additionalData.pieData = data.data; // Store original data for tooltips
                additionalData.summary = data.summary;
                
                // Define vaccine colors for pie chart
                const vaccineColors = {
                    'colera': 'rgba(255, 99, 132, 0.8)',
                    'coriza': 'rgba(54, 162, 235, 0.8)',
                    'corona_virus': 'rgba(255, 205, 86, 0.8)',
                    'encefalomielitis': 'rgba(75, 192, 192, 0.8)',
                    'influenza': 'rgba(153, 102, 255, 0.8)',
                    'marek': 'rgba(255, 159, 64, 0.8)',
                    'newcastle': 'rgba(199, 199, 199, 0.8)',
                    'viruela': 'rgba(83, 102, 255, 0.8)',
                    'garrapatas': 'rgba(255, 99, 255, 0.8)',
                    'parasitos': 'rgba(102, 255, 178, 0.8)'
                };
                
                // Assign colors based on vaccine type
                additionalData.colors = data.data.map(item => 
                    vaccineColors[item.vaccine_type] || 'rgba(128, 128, 128, 0.8)'
                );
            } else {
                labels = [];
                values = [];
                additionalData.isPieChart = true;
                additionalData.percentages = [];
                additionalData.vaccineTypes = [];
                additionalData.pieData = [];
                additionalData.colors = [];
                additionalData.summary = {};
            }
            break;

        case 'variableCosts':
            // Handle pie chart data for variable costs breakdown
            if (data.data && Array.isArray(data.data)) {
                labels = data.data.map(item => item.label);
                values = data.data.map(item => parseFloat(item.value));
                additionalData.isPieChart = true;
                additionalData.percentages = data.data.map(item => parseFloat(item.percentage));
                additionalData.categoryTypes = data.data.map(item => item.category_type);
                additionalData.pieData = data.data; // Store original data for tooltips
                additionalData.summary = data.summary;
                
                // Define cost category colors for pie chart
                const categoryColors = {
                    'concentrado': 'rgba(0, 123, 255, 0.8)',      // Blue
                    'harinas': 'rgba(255, 193, 7, 0.8)',          // Yellow  
                    'fermentados': 'rgba(111, 66, 193, 0.8)',            // Purple
                    'waterTreatment': 'rgba(23, 162, 184, 0.8)'         // Teal
                };
                
                // Assign colors based on category type
                additionalData.colors = data.data.map(item => 
                    categoryColors[item.category_type] || 'rgba(128, 128, 128, 0.8)'
                );
            } else {
                labels = [];
                values = [];
                additionalData.isPieChart = true;
                additionalData.percentages = [];
                additionalData.categoryTypes = [];
                additionalData.pieData = [];
                additionalData.colors = [];
                additionalData.summary = {};
            }
            break;

        case 'grossProfitSummary':
            // Handle bar chart data for gross profit summary (Income, Expenses, Profit)
            if (data.data && Array.isArray(data.data)) {
                labels = data.data.map(item => item.category);
                values = data.data.map(item => parseFloat(item.value));
                additionalData.types = data.data.map(item => item.type);
                additionalData.details = data.data.map(item => item.details);
                additionalData.summary = data.summary;
                
                // Define colors based on financial category
                additionalData.barColors = data.data.map(item => {
                    if (item.type === 'income') return 'rgba(40, 167, 69, 0.8)';     // Green for income
                    if (item.type === 'expense') return 'rgba(220, 53, 69, 0.8)';    // Red for expenses
                    if (item.type === 'profit') return 'rgba(75, 192, 192, 0.8)';    // Teal for profit
                    if (item.type === 'loss') return 'rgba(255, 99, 132, 0.8)';      // Pink for loss
                    return 'rgba(108, 117, 125, 0.8)';                               // Gray default
                });
            } else {
                labels = [];
                values = [];
                additionalData.types = [];
                additionalData.details = [];
                additionalData.barColors = [];
                additionalData.summary = {};
            }
            break;
            
        case 'deaths':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseInt(item.deaths_count));
            additionalData.causes = data.map(item => item.causes || 'No especificado');
            break;
            
        case 'discards':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseFloat(item.discards_count));
            additionalData.weights = data.map(item => item.total_weight_discarded ? `${item.total_weight_discarded} kg total` : 'Peso no disponible');
            break;
        case 'purchases':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseFloat(item.total_revenue));
            break;
        case 'sales':
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseFloat(item.total_revenue));
            break;

        default:
            labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            });
            values = data.map(item => parseInt(item[Object.keys(item).find(key => key.includes('_count'))] || item.count || 0));
    }

    // Ensure rawData is always an array that matches the data length
    const rawDataArray = [];
    for (let i = 0; i < labels.length; i++) {
        if (data && data[i]) {
            rawDataArray[i] = data[i];
        } else {
            // Create fallback data object
            rawDataArray[i] = {
                month: labels[i] || `Month ${i + 1}`,
                tagids: '',
                record_count: values[i] || 0
            };
        }
    }


    // Handle variableCosts pie charts with different colors per category
    if (chartType === 'variableCosts' && additionalData.isPieChart) {
        const colors = additionalData.colors || ['rgba(128, 128, 128, 0.8)'];
        const borderColors = colors.map(color => color.replace('0.8', '1'));

        return {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2,
                pieData: additionalData.pieData,
                additionalData: additionalData
            }]
        };
    }

    // Special handling for grossProfitSummary bar chart with custom colors per bar
    if (chartType === 'grossProfitSummary' && additionalData.barColors) {
        const borderColors = additionalData.barColors.map(color => color.replace('0.8', '1'));
        
        return {
            labels: labels,
            datasets: [{
                label: config.title,
                data: values,
                backgroundColor: additionalData.barColors,
                borderColor: borderColors,
                borderWidth: 2,
                additionalData: additionalData
            }]
        };
    }

    // Return the standard single dataset chart data structure
    return {
        labels: labels,
        datasets: [{
            label: config.title,
            data: values,
            backgroundColor: config.color,
            borderColor: config.borderColor,
            borderWidth: 2,
            rawData: rawDataArray,
            additionalData: additionalData
        }]
    };
}

// Get chart type based on data
function getChartType(chartType) {
    const barChartTypes = ['sales', 'discards', 'huevoRevenue', 'totalFarmIncome', 'grossProfitSummary'];
    const pieChartTypes = ['variableCosts'];
    const mixedChartTypes = ['salinidadGastos', 'pHGastos', 'oxigenoGastos', 'nitritosGastos', 'amoniacosGastos', 'alcalinidadGastos', 'transparenciaGastos', 'redoxGastos', 'purchases', 'deaths']; // Charts that use mixed bar+line with different datasets
    
    if (pieChartTypes.includes(chartType)) return 'pie';
    if (mixedChartTypes.includes(chartType)) return 'bar'; // Base type for mixed charts
    return barChartTypes.includes(chartType) ? 'bar' : 'line';
}

// Get chart options
function getChartOptions(chartType, isModal = false) {
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: chartConfigs[chartType].title,
                font: {
                    size: isModal ? 18 : 14,
                    weight: 'bold'
                },
                color: '#333'
            },
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: {
                        size: isModal ? 14 : 12,
                        weight: '500'
                    },
                    color: '#666'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#666'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                ticks: {
                    font: {
                        size: isModal ? 12 : 10
                    },
                    color: '#666',
                    maxRotation: 45
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    };



    // Special handling for pie charts
    if (chartType === 'variableCosts') {
        // Remove scales for pie charts
        delete baseOptions.scales;
        
        // Enhanced legend for pie chart
        baseOptions.plugins.legend = {
            display: true,
            position: 'right',
            labels: {
                font: {
                    size: isModal ? 12 : 10,
                    weight: '500'
                },
                color: '#666',
                usePointStyle: true,
                padding: 15,
                generateLabels: function(chart) {
                    const data = chart.data;
                    if (data.labels.length && data.datasets.length) {
                        const dataset = data.datasets[0];
                        
                        // For variableCosts, show percentages in legend
                        if (chartType === 'variableCosts' && dataset.pieData) {
                            return dataset.pieData.map((item, index) => {
                                // Use appropriate decimal precision for percentages
                                let percentageText;
                                if (item.percentage < 0.1 && item.percentage > 0) {
                                    percentageText = item.percentage.toFixed(2);
                                } else if (item.percentage < 1 && item.percentage >= 0.1) {
                                    percentageText = item.percentage.toFixed(2);
                                } else {
                                    percentageText = item.percentage.toFixed(2);
                                }
                                
                                return {
                                    text: `${item.label} (${percentageText}%)`,
                                    fillStyle: dataset.backgroundColor[index],
                                    strokeStyle: dataset.borderColor[index],
                                    lineWidth: dataset.borderWidth,
                                    hidden: false,
                                    index: index
                                };
                            });
                        }
                        
                        // Default legend generation for other pie charts
                        return data.labels.map((label, index) => ({
                            text: label,
                            fillStyle: dataset.backgroundColor[index],
                            strokeStyle: dataset.borderColor[index],
                            lineWidth: dataset.borderWidth,
                            hidden: false,
                            index: index
                        }));
                    }
                    return [];
                }
            }
        };
    }

    // Special handling for grossProfitSummary bar chart with percentage legend
    if (chartType === 'grossProfitSummary') {
        baseOptions.plugins.legend = {
            display: true,
            position: 'top',
            labels: {
                font: {
                    size: isModal ? 12 : 10,
                    weight: '500'
                },
                color: '#666',
                usePointStyle: true,
                padding: 15,
                generateLabels: function(chart) {
                    const data = chart.data;
                    if (data.labels.length && data.datasets.length) {
                        const dataset = data.datasets[0];
                        const additionalData = dataset.additionalData;
                        
                        if (additionalData && additionalData.summary) {
                            const summary = additionalData.summary;
                            const totalIncome = summary.total_income || 1;
                            const totalExpenses = summary.total_expenses || 0;
                            const grossProfit = summary.gross_profit || 0;
                            
                            // Calculate percentages based on income as 100%
                            const expensePercentage = (totalExpenses / totalIncome) * 100;
                            const profitPercentage = (grossProfit / totalIncome) * 100;
                            
                            return data.labels.map((label, index) => {
                                let percentageText = '';
                                
                                if (label === 'Ingresos Totales') {
                                    percentageText = '100%';
                                } else if (label === 'Gastos Totales') {
                                    percentageText = `${expensePercentage.toFixed(2)}%`;
                                } else if (label === 'Ganancia Bruta') {
                                    percentageText = `${profitPercentage.toFixed(2)}%`;
                                }
                                
                                return {
                                    text: `${label} (${percentageText})`,
                                    fillStyle: dataset.backgroundColor[index],
                                    strokeStyle: dataset.borderColor[index],
                                    lineWidth: dataset.borderWidth,
                                    hidden: false,
                                    index: index
                                };
                            });
                        }
                        
                        // Fallback to default legend if no summary data
                        return data.labels.map((label, index) => ({
                            text: label,
                            fillStyle: dataset.backgroundColor[index],
                            strokeStyle: dataset.borderColor[index],
                            lineWidth: dataset.borderWidth,
                            hidden: false,
                            index: index
                        }));
                    }
                    return [];
                }
            }
        };
    }

    // Add custom tooltips for specific chart types
    const tooltipChartTypes = ['deaths', 'discards', 'weaning', 'insemination', 'sales', 'pesoRevenue', 'huevoRevenue', 'totalFarmIncome', 'variableCosts', 'vaccineCosts', 'grossProfitSummary'];
    if (tooltipChartTypes.includes(chartType)) {
        baseOptions.plugins.tooltip = {
            enabled: true,
            mode: 'index',
            intersect: false,
            position: 'nearest',
            external: null,
            callbacks: {
                title: function(context) {
                    // Always show a title, use the label or fallback
                    return context[0] && context[0].label ? context[0].label : 'Mes';
                },
                label: function(context) {
                    try {
                        const dataset = context.dataset;
                        const dataIndex = context.dataIndex;
                        let chartType = context.chart.canvas.id.replace('Chart', '');
                        
                        // Handle modal chart case - use the current expanded chart type
                        if (chartType === 'modal' && currentExpandedChart) {
                            chartType = currentExpandedChart;
                        }
                        
                        // Handle sales chart specifically
                        if (chartType === 'sales') {
                            let rawData = null;
                            if (dataset && dataset.rawData && Array.isArray(dataset.rawData) && dataset.rawData[dataIndex]) {
                                rawData = dataset.rawData[dataIndex];
                            }
                            
                            const revenue = context.parsed ? context.parsed.y : 0;
                            const sales = rawData && rawData.total_sales ? rawData.total_sales : 0;
                            const weight = rawData && rawData.total_weight_sold ? rawData.total_weight_sold : 0;
                            
                            let tooltip = `Ingresos: $${revenue.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            if (sales > 0) {
                                tooltip += `\nVentas: ${sales} animales`;
                            }
                            if (weight > 0) {
                                tooltip += `\nPeso total: ${parseFloat(weight).toLocaleString('es-ES', {minimumFractionDigits: 1})} kg`;
                            }
                            
                            return tooltip.split('\n');
                        }
                        
                        // Handle discards chart specifically
                        if (chartType === 'discards') {
                            let rawData = null;
                            if (dataset && dataset.rawData && Array.isArray(dataset.rawData) && dataset.rawData[dataIndex]) {
                                rawData = dataset.rawData[dataIndex];
                            }
                            
                            const revenueLost = context.parsed ? context.parsed.y : 0;
                            const discards = rawData && rawData.total_discards ? rawData.total_discards : 0;
                            const weight = rawData && rawData.total_weight_discarded ? rawData.total_weight_discarded : 0;
                            
                            let tooltip = `Pérdida: $${revenueLost.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            if (discards > 0) {
                                tooltip += `\nDescartes: ${discards} animales`;
                            }
                            if (weight > 0) {
                                tooltip += `\nPeso total: ${parseFloat(weight).toLocaleString('es-ES', {minimumFractionDigits: 1})} kg`;
                            }
                            
                            return tooltip.split('\n');
                        }
                        
                        // Handle peso revenue chart specifically
                        if (chartType === 'pesoRevenue') {
                            let rawData = null;
                            if (dataset && dataset.rawData && Array.isArray(dataset.rawData) && dataset.rawData[dataIndex]) {
                                rawData = dataset.rawData[dataIndex];
                            }
                            
                            const revenue = context.parsed ? context.parsed.y : 0;
                            const records = rawData && rawData.total_records ? rawData.total_records : 0;
                            const weight = rawData && rawData.total_weight_sold ? rawData.total_weight_sold : 0;
                            const avgPrice = rawData && rawData.avg_price_per_kg ? rawData.avg_price_per_kg : 0;
                            
                            let tooltip = `Ingresos: $${revenue.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            if (records > 0) {
                                tooltip += `\nRegistros: ${records} pesajes`;
                            }
                            if (weight > 0) {
                                tooltip += `\nPeso total: ${parseFloat(weight).toLocaleString('es-ES', {minimumFractionDigits: 1})} kg`;
                            }
                            if (avgPrice > 0) {
                                tooltip += `\nPrecio prom: $${parseFloat(avgPrice).toLocaleString('es-ES', {minimumFractionDigits: 2})}/kg`;
                            }
                            
                            return tooltip.split('\n');
                        }

                        // Handle huevo revenue chart specifically
                        if (chartType === 'huevoRevenue') {
                            let rawData = null;
                            if (dataset && dataset.rawData && Array.isArray(dataset.rawData) && dataset.rawData[dataIndex]) {
                                rawData = dataset.rawData[dataIndex];
                            }
                            
                            const revenue = context.parsed ? context.parsed.y : 0;
                            const records = rawData && rawData.total_records ? rawData.total_records : 0;
                            const eggs = rawData && rawData.total_eggs_sold ? rawData.total_eggs_sold : 0;
                            const avgPrice = rawData && rawData.avg_price_per_egg ? rawData.avg_price_per_egg : 0;
                            
                            let tooltip = `Ingresos por huevos: $${revenue.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            if (records > 0) {
                                tooltip += `\nRegistros: ${records} ventas`;
                            }
                            if (eggs > 0) {
                                tooltip += `\nHuevos vendidos: ${parseInt(eggs).toLocaleString('es-ES')} unidades`;
                            }
                            if (avgPrice > 0) {
                                tooltip += `\nPrecio prom: $${parseFloat(avgPrice).toLocaleString('es-ES', {minimumFractionDigits: 2})}/huevo`;
                            }
                            
                            return tooltip.split('\n');
                        }

                        // Handle totalFarmIncome chart specifically - show breakdown
                        if (chartType === 'totalFarmIncome') {
                            const totalRevenue = context.parsed ? context.parsed.y : 0;
                            const huevoRevenue = dataset.additionalData && dataset.additionalData.huevoRevenue ? 
                                dataset.additionalData.huevoRevenue[dataIndex] : 0;
                            const broilerRevenue = dataset.additionalData && dataset.additionalData.broilerRevenue ? 
                                dataset.additionalData.broilerRevenue[dataIndex] : 0;
                            
                            let tooltip = `Ingresos Totales: $${totalRevenue.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            tooltip += `\n• Ingresos por Huevos: $${huevoRevenue.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            tooltip += `\n• Ingresos por Pollos: $${broilerRevenue.toLocaleString('es-ES', {minimumFractionDigits: 2})}`;
                            
                            return tooltip.split('\n');
                        }

                        // Handle variableCosts pie chart specifically - show category and percentage
                        if (chartType === 'variableCosts') {
                            const dataset = context.dataset;
                            const dataIndex = context.dataIndex;
                            
                            if (dataset && dataset.pieData && dataset.pieData[dataIndex]) {
                                const item = dataset.pieData[dataIndex];
                                
                                // Use appropriate decimal precision for percentages
                                let percentageText;
                                if (item.percentage < 0.1 && item.percentage > 0) {
                                    percentageText = item.percentage.toFixed(3);
                                } else if (item.percentage < 1 && item.percentage >= 0.1) {
                                    percentageText = item.percentage.toFixed(2);
                                } else {
                                    percentageText = item.percentage.toFixed(1);
                                }
                                
                                const value = item.value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                
                                return `${item.label}: $${value} (${percentageText}%)`;
                            }
                            
                            return 'Error al cargar datos';
                        }

                        // Handle purchases chart specifically - show monthly and cumulative data
                        if (chartType === 'purchases') {
                            const index = context.dataIndex;
                            const tooltipInfo = data.tooltipData[index];
                            const datasetLabel = context.dataset.label;
                            
                            if (tooltipInfo && tooltipInfo.purchases_data) {
                                const purchData = tooltipInfo.purchases_data;
                                let tooltipLines = [];
                                
                                if (datasetLabel.includes('Mensuales')) {
                                    tooltipLines.push(`${datasetLabel}: ${purchData.monthly_amount}`);
                                    tooltipLines.push(`Total de compras: ${purchData.total_purchases}`);
                                    tooltipLines.push(`Acumulado hasta la fecha: ${purchData.cumulative_amount}`);
                                } else if (datasetLabel.includes('Acumuladas')) {
                                    tooltipLines.push(`${datasetLabel}: ${purchData.cumulative_amount}`);
                                    tooltipLines.push(`Compras del mes: ${purchData.monthly_amount}`);
                                    tooltipLines.push(`Total de compras: ${purchData.total_purchases}`);
                                }
                                
                                return tooltipLines;
                            }
                            
                            // Fallback formatting
                            if (datasetLabel.includes('Mensuales')) {
                                return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                            } else {
                                return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                            }
                        }

                        // Handle deaths chart specifically - show monthly average and cumulative loss data
                        if (chartType === 'deaths') {
                            const index = context.dataIndex;
                            const tooltipInfo = data.tooltipData[index];
                            const datasetLabel = context.dataset.label;
                            
                            if (tooltipInfo && tooltipInfo.deaths_data) {
                                const deathsData = tooltipInfo.deaths_data;
                                let tooltipLines = [];
                                
                                if (datasetLabel.includes('Promedio')) {
                                    tooltipLines.push(`${datasetLabel}: ${deathsData.avg_monthly_monto}`);
                                    tooltipLines.push(`Total de decesos: ${deathsData.total_deaths}`);
                                    tooltipLines.push(`Peso promedio: ${deathsData.avg_weight}`);
                                    tooltipLines.push(`Precio promedio: ${deathsData.avg_price}`);
                                    tooltipLines.push(`Pérdida acumulada: ${deathsData.cumulative_loss}`);
                                    tooltipLines.push(`% Pérdida promedio: ${deathsData.avg_loss_percentage}`);
                                } else if (datasetLabel.includes('Acumuladas')) {
                                    tooltipLines.push(`${datasetLabel}: ${deathsData.cumulative_loss}`);
                                    tooltipLines.push(`Pérdida del mes: ${deathsData.total_monthly_loss}`);
                                    tooltipLines.push(`Promedio mensual: ${deathsData.avg_monthly_monto}`);
                                    tooltipLines.push(`Total de decesos: ${deathsData.total_deaths}`);
                                    tooltipLines.push(`Total kg perdidos: ${deathsData.total_quantity_kg}`);
                                }
                                
                                return tooltipLines;
                            }
                            
                            // Fallback formatting
                            if (datasetLabel.includes('Promedio')) {
                                return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                            } else {
                                return `${datasetLabel}: $${context.parsed.y.toFixed(2)}`;
                            }
                        }


                        // Handle grossProfitSummary bar chart - show financial details
                        if (chartType === 'grossProfitSummary') {
                            const dataset = context.dataset;
                            const dataIndex = context.dataIndex;
                            
                            if (dataset && dataset.additionalData && dataset.additionalData.types && dataset.additionalData.details) {
                                const type = dataset.additionalData.types[dataIndex];
                                const details = dataset.additionalData.details[dataIndex];
                                const value = context.parsed.y;
                                const formattedValue = value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                
                                let tooltip = `${context.label}: $${formattedValue}`;
                                
                                // Add additional details based on type
                                if (type === 'income' && details) {
                                    if (details.huevos && details.pollos_engorde) {
                                        tooltip += `\n• Huevos: $${details.huevos.toLocaleString('es-ES')}`;
                                        tooltip += `\n• Pollos Engorde: $${details.pollos_engorde.toLocaleString('es-ES')}`;
                                    } else if (details.ventas) {
                                        tooltip += `\n• Ventas: $${details.ventas.toLocaleString('es-ES')}`;
                                    }
                                } else if (type === 'expense' && details) {
                                    if (details.concentrado) tooltip += `\n• Concentrado: $${details.concentrado.toLocaleString('es-ES')}`;
                                    if (details.harinas) tooltip += `\n• Harinas: $${details.harinas.toLocaleString('es-ES')}`;
                                    if (details.fermentados) tooltip += `\n• Fermentados: $${details.fermentados.toLocaleString('es-ES')}`;
                                    if (details.waterTreatment) tooltip += `\n• Water Treatment: $${details.waterTreatment.toLocaleString('es-ES')}`;
                                    if (details.porcentaje_gastos !== undefined) tooltip += `\n• Porcentaje de Ingresos: ${details.porcentaje_gastos}%`;
                                } else if ((type === 'profit' || type === 'loss') && details && details.margen_porcentaje !== undefined) {
                                    tooltip += `\n• Margen: ${details.margen_porcentaje}%`;
                                }
                                
                                return tooltip.split('\n');
                            }
                            
                            return '';
                        }

                        
                        // Safely access rawData with multiple fallback checks for other chart types
                        let rawData = null;
                        if (dataset && dataset.rawData && Array.isArray(dataset.rawData) && dataset.rawData[dataIndex]) {
                            rawData = dataset.rawData[dataIndex];
                        }
                        
                        // Extract tag IDs with comprehensive validation
                        if (rawData && rawData.tagids) {
                            const tagidsString = String(rawData.tagids).trim();
                            if (tagidsString && tagidsString !== '' && tagidsString !== 'null' && tagidsString !== 'undefined') {
                                const tagIds = tagidsString.split(',')
                                    .map(tag => String(tag).trim())
                                    .filter(tag => tag && tag !== '' && tag !== 'null' && tag !== 'undefined');
                                
                                if (tagIds.length > 0) {
                                    return tagIds.join(', ');
                                }
                            }
                        }
                        
                        // Fallback: Show that there are records but no tag IDs
                        if (context.parsed && context.parsed.y > 0) {
                            return `${context.parsed.y} registros - Sin Tag IDs`;
                        }
                        
                        return 'Sin datos disponibles';
                        
                    } catch (error) {
                        console.warn('Tooltip error:', error);
                        return 'Error al cargar datos';
                    }
                },
                beforeBody: function() {
                    return null; // No additional content before body
                },
                afterBody: function() {
                    return null; // No additional content after body
                },
                footer: function() {
                    return null; // No footer content
                }
            },
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#555555',
            borderWidth: 1,
            cornerRadius: 6,
            displayColors: false,
            titleFont: {
                size: 13,
                weight: 'bold'
            },
            bodyFont: {
                size: 12,
                lineHeight: 1.3
            },
            padding: 10,
            caretPadding: 4,
            caretSize: 5,
            multiKeyBackground: 'transparent',
            filter: function(tooltipItem) {
                // Always show tooltip for every bar
                return true;
            },
            itemSort: function(a, b) {
                // Keep original order
                return 0;
            }
        };
        
        // Optimize interaction settings for better tooltip detection
        baseOptions.interaction = {
            intersect: false,
            mode: 'index',
            axis: 'x'
        };
        
        // Ensure hover settings work properly
        baseOptions.onHover = function(event, activeElements) {
            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
        };
    }

    return baseOptions;
}

// Expand chart to full screen
function expandChart(chartType) {
    const config = chartConfigs[chartType];
    if (!config) return;

    currentExpandedChart = chartType;
    
    // Update modal title
    const modalTitle = document.getElementById('modalChartTitle');
    modalTitle.innerHTML = `<i class="${config.icon}"></i> ${config.title}`;
    
    // Show modal
    const modal = document.getElementById('chartModal');
    modal.classList.add('active');
    
    // Load chart in modal
    setTimeout(() => {
        loadChart(chartType, 'modalChart', true);
    }, 300);
}

// Close chart modal
function closeChartModal() {
    const modal = document.getElementById('chartModal');
    modal.classList.remove('active');
    
    // Destroy modal chart
    if (chartInstances['modalChart']) {
        chartInstances['modalChart'].destroy();
        delete chartInstances['modalChart'];
    }
    
    currentExpandedChart = null;
}

// Export chart to PDF
async function exportChartPDF(chartType) {
    const config = chartConfigs[chartType];
    if (!config) return;

    try {
        const canvas = document.getElementById(`${chartType}Chart`);
        if (!canvas) return;

        // Show loading state
        Swal.fire({
            title: 'Generando PDF...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');

        // Add title
        pdf.setFontSize(18);
        pdf.setFont(undefined, 'bold');
        pdf.text(config.title, 20, 25);

        // Add subtitle
        pdf.setFontSize(12);
        pdf.setFont(undefined, 'normal');
        pdf.text('Sistema de Gestion Ganadera - Animalia', 20, 35);

        // Convert canvas to image
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 250;
        const imgHeight = 150;
        
        // Center the image
        const x = (297 - imgWidth) / 2;
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestion Ganadera - Animalia', 20, pageHeight - 15);
        
        const now = new Date();
        pdf.text(`Generado: ${now.toLocaleDateString()}`, 200, pageHeight - 15);

        // Save the PDF
        const filename = `${chartType}_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    }
}

// Close modal when clicking outside
document.getElementById('chartModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeChartModal();
    }
});

// Escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentExpandedChart) {
        closeChartModal();
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    // Load all dashboard charts
    Object.keys(chartConfigs).forEach(chartType => {
        loadChart(chartType, `${chartType}Chart`);
    });
});

// Resize handler to update charts
window.addEventListener('resize', function() {
    Object.values(chartInstances).forEach(chart => {
        if (chart) {
            chart.resize();
        }
    });
});
</script>

</body>
</html>