<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Camarones Registros</title>
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
        font-size: 0.55rem;
        font-weight: bold;
        width: 100%;
        margin-top: 2px;
    }

    .scroll-icons-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        overflow-y: visible;
        padding: 15px 0;
        position: relative;
        height: auto;
        -webkit-overflow-scrolling: touch;
    }

    .scroll-icons-container .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
        margin-bottom: 5px;
        width: 100%;
        padding: 0 10px;
    }

    .nav-icon {
        width: 30px;
        height: 30px;
        margin-bottom: 4px;
        object-fit: contain;
    }

    /* Make nav-buttons container more compact */
    #nav-buttons {
        margin-bottom: 10px;
        padding: 5px;
    }

    .icon-button-container {
        margin: 0 2px;
    }

    .icon-button {
        padding: 5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn.btn-secondary {
            width: 75px;
            height: 75px;
            margin: 2px;
        }
        
        .nav-icon {
            width: 25px;
            height: 25px;
        }
        
        .button-label {
            font-size: 0.55rem;
            font-weight: bold;
        }
    }

    @media (max-width: 480px) {
        .btn.btn-secondary {
            width: 65px;
            height: 65px;
            margin: 1px;
        }
        
        .scroll-icons-container .container {
            gap: 3px;
        }
        
        .button-label {
            font-size: 0.55rem;
            font-weight: bold;
        }
    }

    /* Optimize for height */
    @media (max-height: 700px) {
        .btn.btn-secondary {
            width: 70px;
            height: 70px;
            border-radius: 50%;
        }
        
        .scroll-icons-container .container {
            margin-bottom: 3px;
        }
    }

    @media (max-height: 600px) {
        .btn.btn-secondary {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
        
        .nav-icon {
            width: 22px;
            height: 22px;
            margin-bottom: 2px;
        }
        
        .button-label {
            font-size: 0.55rem;
            font-weight: bold;
        }
    }

    /* Enhanced 3D Effects for Scroll Buttons */
    .scroll-icons-container .btn.btn-secondary {
        background: linear-gradient(145deg, #ffffff, #f8f8f8, #f0f0f0);
        box-shadow: 
            8px 8px 16px rgba(0, 0, 0, 0.25),
            -4px -4px 8px rgba(255, 255, 255, 0.9),
            inset 2px 2px 4px rgba(255, 255, 255, 0.8),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
        transform: perspective(800px) translateZ(0) rotateX(5deg);
    }

    .scroll-icons-container .btn.btn-secondary:hover {
        transform: perspective(800px) translateZ(20px) rotateX(10deg) scale(1.05);
        box-shadow: 
            12px 12px 24px rgba(0, 0, 0, 0.35),
            -6px -6px 12px rgba(255, 255, 255, 0.95),
            inset 3px 3px 6px rgba(255, 255, 255, 0.9),
            inset -3px -3px 6px rgba(0, 0, 0, 0.15);
        background: linear-gradient(145deg, #ffffff, #f8f8f8, #f0f0f0);
    }

    .scroll-icons-container .btn.btn-secondary:active {
        transform: perspective(800px) translateZ(-10px) rotateX(2deg) scale(0.98);
        box-shadow: 
            4px 4px 8px rgba(0, 0, 0, 0.2),
            -2px -2px 4px rgba(255, 255, 255, 0.7),
            inset 4px 4px 8px rgba(0, 0, 0, 0.15),
            inset -2px -2px 4px rgba(255, 255, 255, 0.6);
        background: linear-gradient(145deg, #f8f8f8, #f0f0f0, #e8e8e8);
    }

    .scroll-icons-container .btn.btn-secondary:focus {
        box-shadow: 
            8px 8px 16px rgba(0, 0, 0, 0.25),
            -4px -4px 8px rgba(255, 255, 255, 0.9),
            inset 2px 2px 4px rgba(255, 255, 255, 0.8),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1),
            0 0 0 4px rgba(40, 167, 69, 0.3);
        transform: perspective(800px) translateZ(5px) rotateX(8deg);
    }

    .scroll-icons-container .btn.btn-secondary .nav-icon {
        filter: drop-shadow(2px 4px 4px rgba(0, 0, 0, 0.3));
        transform: translateZ(8px) rotateX(5deg);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .scroll-icons-container .btn.btn-secondary:hover .nav-icon {
        transform: translateZ(20px) scale(1.15) rotateX(10deg);
        filter: drop-shadow(3px 6px 6px rgba(0, 0, 0, 0.4));
    }

    .scroll-icons-container .btn.btn-secondary:active .nav-icon {
        transform: translateZ(5px) scale(0.92) rotateX(2deg);
        filter: drop-shadow(1px 2px 2px rgba(0, 0, 0, 0.3));
    }

    .scroll-icons-container .btn.btn-secondary .button-label {
        text-shadow: 2px 2px 3px rgba(255, 255, 255, 0.9), 1px 1px 2px rgba(0, 0, 0, 0.1);
        transform: translateZ(8px) rotateX(5deg);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .scroll-icons-container .btn.btn-secondary:hover .button-label {
        color: #28a745;
        text-shadow: 3px 3px 4px rgba(255, 255, 255, 1), 2px 2px 3px rgba(0, 0, 0, 0.15);
        transform: translateZ(12px) rotateX(10deg) scale(1.05);
    }

    .scroll-icons-container .btn.btn-secondary:active .button-label {
        text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.7);
        transform: translateZ(6px) rotateX(2deg) scale(0.98);
    }



 
    .nav-icon {
        width: 32px;
        height: 32px;
        margin-bottom: 8px;
        object-fit: contain;
        filter: drop-shadow(1px 2px 2px rgba(0, 0, 0, 0.2));
        transition: transform 0.2s ease, filter 0.2s ease;
        transform: translateZ(5px);
    }

    .btn.btn-secondary:hover .nav-icon {
        transform: translateZ(15px) scale(1.1);
        filter: drop-shadow(2px 4px 4px rgba(0, 0, 0, 0.25));
    }

    .btn.btn-secondary:active .nav-icon {
        transform: translateZ(0) scale(0.95);
        filter: drop-shadow(0px 1px 1px rgba(0, 0, 0, 0.2));
    }

    .button-label {
        display: block;
        text-align: center;
        font-size: 0.55rem;
        font-weight: 600;
        width: 100%;
        margin-top: 3px;
        color: #343a40;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.7);
        transition: all 0.2s ease;
        transform: translateZ(5px);
    }

    .btn.btn-secondary:hover .button-label {
        color: #28a745;
        text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.9);
    }

    .btn.btn-secondary:active .button-label {
        text-shadow: 0px 0px 1px rgba(255, 255, 255, 0.5);
    }

    /* Container refinements */
    .scroll-icons-container .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin-bottom: 10px;
        width: 100%;
        padding: 0 15px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn.btn-secondary {
            width: 75px;
            height: 75px;
            border-radius: 50%;
        }
        
        .nav-icon {
            width: 28px;
            height: 28px;
        }
    }

    @media (max-width: 480px) {
        .btn.btn-secondary {
            width: 65px;
            height: 65px;
            border-radius: 50%;
        }
        
        .nav-icon {
            width: 24px;
            height: 24px;
        }
        
        .button-label {
            font-size: 0.55rem;
        }
    }





    /* Button styling - same as navbar icon-button */
    .btn.btn-secondary {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 85px;
        height: 85px;
        margin: 3px;
        padding: 5px;
        border-radius: 50%;
        background: linear-gradient(145deg, #ffffff, #f8f8f8, #f0f0f0);
        border: none;
        box-shadow: 
            8px 8px 16px rgba(0, 0, 0, 0.25),
            -4px -4px 8px rgba(255, 255, 255, 0.9),
            inset 2px 2px 4px rgba(255, 255, 255, 0.8),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
        transform: perspective(800px) translateZ(0) rotateX(5deg);
    }

    .btn.btn-secondary:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 50%;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 11px 11px 0 0;
        pointer-events: none;
    }

    .btn.btn-secondary:hover {
        transform: perspective(800px) translateZ(20px) rotateX(10deg) scale(1.05);
        box-shadow: 
            12px 12px 24px rgba(0, 0, 0, 0.35),
            -6px -6px 12px rgba(255, 255, 255, 0.95),
            inset 3px 3px 6px rgba(255, 255, 255, 0.9),
            inset -3px -3px 6px rgba(0, 0, 0, 0.15);
        border: none;
        background: linear-gradient(145deg, #ffffff, #f8f8f8, #f0f0f0);
    }

    .btn.btn-secondary:active {
        transform: perspective(800px) translateZ(-10px) rotateX(2deg) scale(0.98);
        box-shadow: 
            4px 4px 8px rgba(0, 0, 0, 0.2),
            -2px -2px 4px rgba(255, 255, 255, 0.7),
            inset 4px 4px 8px rgba(0, 0, 0, 0.15),
            inset -2px -2px 4px rgba(255, 255, 255, 0.6);
        background: linear-gradient(145deg, #f8f8f8, #f0f0f0, #e8e8e8);
    }

    .btn.btn-secondary:focus {
        outline: none;
        border: none;
        box-shadow: 
            8px 8px 16px rgba(0, 0, 0, 0.25),
            -4px -4px 8px rgba(255, 255, 255, 0.9),
            inset 2px 2px 4px rgba(255, 255, 255, 0.8),
            inset -2px -2px 4px rgba(0, 0, 0, 0.1),
            0 0 0 4px rgba(40, 167, 69, 0.3);
        transform: perspective(800px) translateZ(5px) rotateX(8deg);
    }

    .nav-icon {
        width: 32px;
        height: 32px;
        margin-bottom: 8px;
        object-fit: contain;
        filter: drop-shadow(1px 2px 2px rgba(0, 0, 0, 0.2));
        transition: transform 0.2s ease, filter 0.2s ease;
        transform: translateZ(5px);
    }

    .btn.btn-secondary:hover .nav-icon {
        transform: translateZ(15px) scale(1.1);
        filter: drop-shadow(2px 4px 4px rgba(0, 0, 0, 0.25));
    }

    .btn.btn-secondary:active .nav-icon {
        transform: translateZ(0) scale(0.95);
        filter: drop-shadow(0px 1px 1px rgba(0, 0, 0, 0.2));
    }

    .button-label {
        display: block;
        text-align: center;
        font-size: 0.55rem;
        font-weight: 600;
        width: 100%;
        margin-top: 3px;
        color: #343a40;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.7);
        transition: all 0.2s ease;
        transform: translateZ(5px);
    }

    .btn.btn-secondary:hover .button-label {
        color: #28a745;
        text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.9);
    }

    .btn.btn-secondary:active .button-label {
        text-shadow: 0px 0px 1px rgba(255, 255, 255, 0.5);
    }

    /* Responsive adjustments for 3D buttons */
    @media (max-width: 768px) {
        .btn.btn-secondary {
            width: 75px;
            height: 75px;
            border-radius: 10px;
        }
        
        .nav-icon {
            width: 28px;
            height: 28px;
            margin-bottom: 6px;
        }
    }

    @media (max-width: 480px) {
        .btn.btn-secondary {
            width: 65px;
            height: 65px;
            border-radius: 8px;
            box-shadow: 
                3px 3px 7px rgba(0, 0, 0, 0.15),
                -1px -1px 3px rgba(255, 255, 255, 0.8),
                inset 0 1px 1px rgba(255, 255, 255, 0.6);
        }
        
        .nav-icon {
            width: 20px;
            height: 20px;
            margin-bottom: 4px;
        }
        
        .button-label {
            font-size: 0.55rem;
        }
    }

    /* Scrollable container fixes */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    /* Updated scroll-icons-container to allow unlimited vertical scrolling */
    .scroll-icons-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        overflow-y: visible;
        padding: 15px 0;
        position: relative;
        height: auto;
        -webkit-overflow-scrolling: touch;
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

    /* Remove height restriction for all screen sizes */
    @media (max-height: 800px) {
        .scroll-icons-container {
            max-height: none;
            overflow-y: visible;
        }
    }
</style>

</head>
<body>
<!-- Navigation Title -->
 <!-- Navigation Title -->
<nav class="navbar text-center">
    <!-- Title Row -->
    <div class="container-fluid">
        <div class="row w-100">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <h1 class="navbar-title text-center mx-auto">
                    
                    REGISTROS CAMARONES
                    
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

<!-- Scroll Icons Container -->
<div class="container scroll-icons-container">
    <div class="container  salud-container" data-category="ESTANQUE">    
    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="Temperatura"
        aria-expanded="false"
        aria-controls="temperatura"
        onclick="window.location.href='./camarones_register_temperatura.php'">
        <img src="./images/temperatura.png" alt="Temperatura" class="nav-icon">
        <span class="button-label">TEMPERATURA</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="Salinidad"
        aria-expanded="false"
        aria-controls="salinidad"
        onclick="window.location.href='./camarones_register_salinidad.php'">
        <img src="./images/salinidad.png" alt="Salinidad" class="nav-icon">
        <span class="button-label">SALINIDAD</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="pH"
        aria-expanded="false"
        aria-controls="ph"
        onclick="window.location.href='./camarones_register_ph.php'">
        <img src="./images/ph.png" alt="pH" class="nav-icon">
        <span class="button-label">pH</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="O2"
        aria-expanded="false"
        aria-controls="o2"
        onclick="window.location.href='./camarones_register_oxigeno.php'">
        <img src="./images/o2.png" alt="O2" class="nav-icon">
        <span class="button-label">OXIGENO</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#amoniaco" 
        data-tooltip="Amoniaco"
        aria-expanded="false"
        aria-controls="amoniaco"
        onclick="window.location.href='./camarones_register_ammonia.php'">
        <img src="./images/amoniaco.png" alt="Amoniaco" class="nav-icon">
        <span class="button-label">AMONIACO</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#nitritos" 
        data-tooltip="Nitritos"
        aria-expanded="false"
        aria-controls="nitritos"
        onclick="window.location.href='./camarones_register_nitritos.php'">
        <img src="./images/nitritos.png" alt="Nitritos" class="nav-icon">
        <span class="button-label">NITRITOS</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse"
        data-bs-target="#alcalinidad" 
        data-tooltip="Newcastle"
        aria-expanded="false"
        aria-controls="alcalinidad"
        onclick="window.location.href='./camarones_register_alcalinidad.php'">
        <img src="./images/alcalinidad.png" alt="Alcalinidad" class="nav-icon">
        <span class="button-label">ALCALINIDAD</span>
    </button>
    
    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#transparencia" 
        data-tooltip="Transparencia"
        aria-expanded="false"
        aria-controls="transparencia"
        onclick="window.location.href='./camarones_register_transparencia.php'">
        <img src="./images/transparencia.png" alt="Transparencia" class="nav-icon">
        <span class="button-label">TRANSPARENCIA</span>
    </button>
    
    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#redox" 
        data-tooltip="Redox"
        aria-expanded="false"
        aria-controls="redox"
        onclick="window.location.href='./camarones_register_redox.php'">
        <img src="./images/redox.jpeg" alt="Redox" class="nav-icon">
        <span class="button-label">REDOX</span>
    </button>
    </div>

    <div class="container poblacion-container" data-category="POBLACION">
    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#compra" 
        data-tooltip="Compras"
        aria-expanded="false"
        aria-controls="compra"
        onclick="window.location.href='./camarones_register_compras.php'">
        <img src="./images/compras.png" alt="Compra" class="nav-icon">
        <span class="button-label">COMPRAS</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#venta" 
        data-tooltip="Venta"
        aria-expanded="false"   
        aria-controls="venta"
        onclick="window.location.href='./camarones_register_venta.php'">
        <img src="./images/venta.png" alt="Venta" class="nav-icon">
        <span class="button-label">VENTAS</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#deceso" 
        data-tooltip="Deceso"
        aria-expanded="false"   
        aria-controls="deceso"
        onclick="window.location.href='./camarones_register_decesos.php'">
        <img src="./images/deceso.png" alt="Deceso" class="nav-icon">
        <span class="button-label">DECESOS</span>
    </button>
    </div>   

    <div class="container alimentacion-container" data-category="ALIMENTACION">
    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#feed" 
        data-tooltip="Feed"
        aria-expanded="false"   
        aria-controls="feed"  
        onclick="window.location.href='./camarones_register_feed.php'">    
        <img src="./images/aba.png" alt="ABA" class="nav-icon">
        <span class="button-label">ABA</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#fermentados" 
        data-tooltip="Fermentados"
        aria-expanded="false"   
        aria-controls="fermentados"  
        onclick="window.location.href='./camarones_register_fermentados.php'"> 
        <img src="./images/fermentados.png" alt="Fermentados" class="nav-icon">
        <span class="button-label">FERMENTADOS</span>
    </button>

    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#harinas" 
        data-tooltip="Harinas"
        aria-expanded="false"   
        aria-controls="harinas"  
        onclick="window.location.href='./camarones_register_harinas.php'"> 
        <img src="./images/harinas.png" alt="Harinas" class="nav-icon">
        <span class="button-label">HARINAS</span>
    </button>
    </div>

    <div class="container produccion-container" data-category="PRODUCCION">
    <button class="btn btn-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#biomasas" 
        data-tooltip="Biomasas"
        aria-expanded="false"   
        aria-controls="biomasas"  
        onclick="window.location.href='./camarones_register_meat.php'">
        <img src="./images/biomasa.png" alt="Biomasa" class="nav-icon">
        <span class="button-label">BIOMASA</span>
    </button>
    </div>
</div>