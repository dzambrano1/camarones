<?php
require_once './pdo_conexion.php';  // Go up one directory since inventario_vacuno.php is in the vacuno folder
// Now you can use $conn for database queries

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Camarones Configuracion</title>
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
<!-- Custom Modal Styles -->
<link rel="stylesheet" href="./camarones.css">

<style>
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

    .button-label {
        display: block;
        text-align: center;
        font-size: 0.7rem;
        width: 100%;
    }

    /* Category container styling with improved spacing and vertical alignment */
    .salud-container, .reproduccion-container, .poblacion-container, .alimentacion-container, .produccion-container {
        border: 2px dotted #28a745;
        border-radius: 10px;
        padding: 15px 8px 8px 8px;
        margin-bottom: 20px;
        position: relative;
        width: 95%; /* Slightly narrower than parent to show borders clearly */
        background-color: rgba(255, 255, 255, 0.8); /* Slight background for better visibility */
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center; /* Center items vertically */
    }

    /* Adjust the button container inside categories for better spacing and alignment */
    .salud-container .container, .reproduccion-container .container, 
    .poblacion-container .container, .alimentacion-container .container, 
    .produccion-container .container {
        padding: 0;
        margin: 0;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center; /* Center items vertically */
    }

    /* Category label styling with improved visibility */
    .salud-container::before, .reproduccion-container::before, .poblacion-container::before, 
    .alimentacion-container::before, .produccion-container::before {
        content: attr(data-category);
        position: absolute;
        top: -12px;
        left: 20px;
        background-color: white;
        padding: 0 12px;
        font-size: 0.85rem;
        font-weight: bold;
        color: #28a745;
        text-transform: uppercase;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        z-index: 1;
    }


    /* Remove borders from navbar elements */
    .navbar,
    .navbar-nav,
    .nav-link,
    .navbar-toggler {
        border: none !important;
        outline: none !important;
    }

    /* Remove focus outlines while maintaining accessibility */
    button:focus,
    .btn:focus,
    input[type="button"]:focus,
    input[type="submit"]:focus,
    input[type="reset"]:focus {
        outline: none !important;
        box-shadow: 
            4px 4px 8px rgba(0, 0, 0, 0.15),
            -2px -2px 6px rgba(255, 255, 255, 0.7),
            0 0 0 3px rgba(0, 123, 255, 0.25) !important;
    }

    /* Override any DataTables button styling */
    .dt-button {
        background: linear-gradient(145deg, #f5f5f5, #e8e8e8) !important;
        border: none !important;
        color: #333 !important;
        padding: 6px 12px !important;
        margin: 2px !important;
    }

    /* Override modal button styling */
    .modal .btn {
        border: none !important;
    }

    /* Remove any remaining borders from form elements used as buttons */
    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
</style>
</head>
<body>

 <!-- Navigation Title -->
<nav class="navbar text-center">
    <!-- Title Row -->
    <div class="container-fluid">
        <div class="row w-100">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <h1 class="navbar-title text-center mx-auto">
                    <i class="fas fa-cow ms-2"></i>
                    CONFIGURACION
                    <i class="fas fa-cow ms-2"></i>
                </h1>
            </div>
        </div>
    </div>
</nav>

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

<!-- Scroll Icons Container -->
<div class="container scroll-icons-container">
    <div class="container  salud-container" data-category="SALUD">


        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_biofungicidas.php'" class="icon-button">
                <img src="./images/biofungicidas.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">BIOFUNGICIDAS</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_salinidad.php'" class="icon-button">
                <img src="./images/salinidad.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">SALINIDAD</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_ph.php'" class="icon-button">
                <img src="./images/ph.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">PH</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_oxigeno.php'" class="icon-button">
                <img src="./images/o2.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">OXIGENO</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_amoniaco.php'" class="icon-button">
                <img src="./images/amoniaco.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">AMONIACO</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_nitritos.php'" class="icon-button">
                <img src="./images/nitritos.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">NITRITOS</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_alcalinidad.php'" class="icon-button">
                <img src="./images/alcalinidad.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">ALCALINIDAD</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_transparencia.php'" class="icon-button">
                <img src="./images/transparencia.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">TRANSPARENCIA</span>
        </div>
        <div class="icon-button-container">
            <button onclick="window.location.href='./camarones_configuracion_redox.php'" class="icon-button">
                <img src="./images/redox.jpeg" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">REDOX</span>
        </div>
</div>
        <div class="container alimentacion-container" data-category="POBLACION">
            <div class="icon-button-container">
                <button onclick="window.location.href='./camarones_configuracion_razas.php'" class="icon-button">
                    <img src="./images/razas.png" alt="Razas" class="nav-icon">
                </button>
                <span class="button-label">RAZAS</span>
            </div>
            <div class="icon-button-container">
                <button onclick="window.location.href='./camarones_configuracion_etapas.php'" class="icon-button">
                    <img src="./images/etapas.png" alt="Etapas" class="nav-icon">
                </button>
                <span class="button-label">ETAPAS</span>
            </div>
            <div class="icon-button-container">
                <button onclick="window.location.href='./camarones_configuracion_estatus.php'" class="icon-button">
                    <img src="./images/estatus.png" alt="Estatus" class="nav-icon">
                </button>
                <span class="button-label">ESTATUS</span>
            </div>
        </div>
        <div class="container alimentacion-container" data-category="ALIMENTACION">
            <div class="icon-button-container">
                <button onclick="window.location.href='./camarones_configuracion_concentrado.php'" class="icon-button">
                    <img src="./images/aba.png" alt="Inicio" class="nav-icon">
                </button>
                <span class="button-label">ABA</span>
            </div>
            <div class="icon-button-container">
                <button onclick="window.location.href='./camarones_configuracion_fermentados.php'" class="icon-button">
                    <img src="./images/fermentados.png" alt="Inicio" class="nav-icon">
                </button>
                <span class="button-label">FERMENTADOS</span>
            </div>
            <div class="icon-button-container">
                <button onclick="window.location.href='./camarones_configuracion_harinas.php'" class="icon-button">
                    <img src="./images/harinas.png" alt="Inicio" class="nav-icon">
                </button>
                <span class="button-label">HARINAS</span>
            </div>
        </div>
</div>