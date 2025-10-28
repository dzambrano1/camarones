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

// At the beginning of your PHP code, create the filter logic
$where_conditions = [];
$params = [];

// Add search condition if provided
if (!empty($_GET['search'])) {
    $where_conditions[] = "(tagid LIKE ? OR nombre LIKE ? OR etapa LIKE ? OR estatus LIKE ?)";
    $search_term = '%' . $_GET['search'] . '%';
    $params = array_merge($params, array_fill(0, 7, $search_term));
}

// Update edad column with calculated days from fecha_nacimiento to today
$updateAgeSql = "UPDATE camarones 
                 SET edad = TIMESTAMPDIFF(DAY, fecha_nacimiento, CURDATE()) 
                 WHERE fecha_nacimiento IS NOT NULL";
$updateStmt = $conn->prepare($updateAgeSql);
$updateStmt->execute();

// Build conditions in order (cascade from left to right)

if (!empty($_GET['etapa'])) {
    $where_conditions[] = "etapa = ?";
    $params[] = $_GET['etapa'];
}

if (!empty($_GET['estatus'])) {
    $where_conditions[] = "estatus = ?";
    $params[] = $_GET['estatus'];
}

// Create the WHERE clause
$where_clause = !empty($where_conditions) ? " WHERE " . implode(" AND ", $where_conditions) : "";

// Prepare and execute the query using prepared statements
$sql = "SELECT id, tagid, nombre, etapa, poblacion, poblacion_actual, supervivencia, estatus, fecha_nacimiento, edad, image, image2, image3, video 
        FROM camarones" . $where_clause . " ORDER BY tagid ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Debug query
$debug_sql = $sql;
foreach ($params as $param) {
    $debug_sql = preg_replace('/\?/', "'" . $param . "'", $debug_sql, 1);
}
error_log("Executed query: " . $debug_sql);

// Use this same $result for both your cards and DataTable


// Fetch counts for each filter category based on current filters

// Etapa Counts
$etapaCountsQuery = "SELECT etapa, COUNT(*) as count FROM camarones" . $where_clause . " GROUP BY etapa";

$etapaCountsResult = $conn->query($etapaCountsQuery);

$etapaLabels = [];
$etapaCounts = [];

if ($etapaCountsResult && $etapaCountsResult->num_rows > 0) {
    while ($row = $etapaCountsResult->fetch_assoc()) {
        $etapaLabels[] = $row['etapa'];
        $etapaCounts[] = $row['count'];
    }
} else {
    // Handle case when there are no records
    $etapaLabels = ['No Data'];
    $etapaCounts = [0];
}

// Etapa Counts
$etapaCountsQuery = "SELECT etapa, COUNT(*) as count FROM camarones" . $where_clause . " GROUP BY etapa";

$etapaCountsResult = $conn->query($etapaCountsQuery);

$etapaLabels = [];
$etapaCounts = [];

if ($etapaCountsResult && $etapaCountsResult->num_rows > 0) {
    while ($row = $etapaCountsResult->fetch_assoc()) {
        $etapaLabels[] = $row['etapa'];
        $etapaCounts[] = $row['count'];
    }
} else {
    $etapaLabels = ['No Data'];
    $etapaCounts = [0];
}

// Estatus Counts
$estatusCountsQuery = "SELECT estatus, COUNT(*) as count FROM camarones" . $where_clause . " GROUP BY estatus";

$estatusCountsResult = $conn->query($estatusCountsQuery);

$estatusLabels = [];
$estatusCounts = [];

if ($estatusCountsResult && $estatusCountsResult->num_rows > 0) {
    while ($row = $estatusCountsResult->fetch_assoc()) {
        $estatusLabels[] = $row['estatus'];
        $estatusCounts[] = $row['count'];
    }
} else {
    $estatusLabels = ['No Data'];
    $estatusCounts = [0];
}

// Calculate totals for percentage calculations if needed
$totalEtapa = array_sum($etapaCounts);
$totalEstatus = array_sum($estatusCounts);

// Fetch tagids based on current filters
$tagidsQuery = "SELECT tagid FROM camarones" . $where_clause;
$tagidsResult = $conn->query($tagidsQuery);

$tagids = [];
if ($tagidsResult && $tagidsResult->num_rows > 0) {
    while ($row = $tagidsResult->fetch_assoc()) {
        $tagids[] = "'" . $conn->real_escape_string($row['tagid']) . "'";
    }
}

// If no tagids are found, set to an array with a dummy value to prevent SQL errors
if (empty($tagids)) {
    $tagids[] = "'NONE'";
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario camarones</title>
    <!-- Link to the Favicon -->
<link rel="icon" href="/images/default_image.png" type="image/x-icon">
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
        font-size: 0.7rem;
        width: 100%;
    }

    /* Card Grid Layout and Styling */
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        padding: 20px;
        margin: 0 auto;
        max-width: 1500px;
    }

    /* Carousel fade effect */
    .carousel-fade .carousel-inner .carousel-item {
        transition-property: opacity;
        transition-duration: 1.04s;
    }
    
    .carousel-fade .carousel-inner .carousel-item,
    .carousel-fade .carousel-inner .active.carousel-item-start,
    .carousel-fade .carousel-inner .active.carousel-item-end {
        opacity: 0;
    }
    
    .carousel-fade .carousel-inner .active,
    .carousel-fade .carousel-inner .carousel-item-next.carousel-item-start,
    .carousel-fade .carousel-inner .carousel-item-prev.carousel-item-end {
        opacity: 1;
        transition: opacity 1.04s ease;
    }
    
    .carousel-fade .carousel-inner .carousel-item-next,
    .carousel-fade .carousel-inner .carousel-item-prev,
    .carousel-fade .carousel-inner .active.carousel-item-start,
    .carousel-fade .carousel-inner .active.carousel-item-end {
        left: 0;
        transform: translate3d(0, 0, 0);
    }

    .card {
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 0 !important; /* Remove the bottom margin */
        height: 360px; /* Slightly reduce height */
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.2);
    }

    .card .action-btn {
        transition: all 0.2s ease;
        border: none;
        background-color: transparent !important;
        padding: 0;
        margin: 0 8px;
    }

    .card .action-btn:hover {
        transform: scale(1.15);
        opacity: 1;
        background-color: transparent !important;
    }

    .card .action-btn i {
        font-size: 1.1rem;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.9), 0 0 2px rgba(0,0,0,0.9);
    }

    .card .update-btn i {
        color: #aaaaaa;
    }
    
    .card .update-btn:hover i {
        color: #cccccc;
    }
    
    .card .history-btn i {
        color: #4cd964;
    }
    
    .card .history-btn:hover i {
        color: #5dea74;
    }
    
    .card .delete-btn i {
        color: #ff3b30;
    }

    .card .delete-btn:hover i {
        color: #ff6259;
    }

    @media (max-width: 768px) {
        .cards-container {
            grid-template-columns: 1fr;
            gap: 0;
            padding: 0;
            margin: 0;
        }
        
        .card {
            height: 100vh;
            border-radius: 0;
            border: none;
            margin: 0 !important;
            box-shadow: none;
        }
        
        .card:hover {
            transform: none;
            box-shadow: none;
        }
        
        /* Make carousel/images cover the entire card */
        .card .carousel {
            width: 100%;
            height: 100%;
        }
        
        .card .carousel-item {
            height: 100vh;
        }
        
        .card .carousel-item img,
        .card .carousel-item video {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }
        
        /* Vertically stack action buttons in bottom right */
        .card .action-buttons {
            position: absolute;
            bottom: 80px;
            right: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10;
        }
        
        .card .action-btn {
            margin: 8px 0;
        }
        
        .card .action-btn i {
            font-size: 1.4rem;
            color: white;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.9);
        }
        
        /* Enhance text visibility */
        .card [style*="color: white"] {
            text-shadow: 1px 1px 5px rgba(0,0,0,0.9), 0 0 3px rgba(0,0,0,0.9) !important;
        }
        
        /* Position animal name and tag at top with larger font */
        .card [style*="margin-top: 5px"] {
            position: absolute;
            top: 20px;
            left: 0;
            padding: 15px;
            z-index: 10;
        }
        
        .card [style*="font-weight: bolder"] {
            font-size: 1.5rem !important;
        }
        
        /* Position info labels at bottom left */
        .card [style*="flex-wrap: wrap"] {
            position: absolute;
            bottom: 20px;
            left: 0;
            padding: 15px;
            z-index: 10;
            text-align: left;
        }
        
        /* Hide the spacer div */
        .card [style*="flex-grow: 1"] {
            display: none;
        }
        
        /* Larger icons for better visibility */
        .card .fa-2xs {
            font-size: 0.9rem !important;
        }
        
        /* Hide header navigation on single card view */
        body.single-card-view .nav-icons-container,
        body.single-card-view .scroll-icons-container,
        body.single-card-view .filters-container,
        body.single-card-view .btn-success[data-bs-toggle="modal"] {
            display: none;
        }
        
        /* Styling for single card view */
        body.single-card-view {
            overflow: hidden;
            background-color: black;
        }
        
        body.single-card-view .cards-container {
            height: 100vh;
            overflow-y: hidden;
        }
        
        body.single-card-view .card {
            display: none;
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.3s, transform 0.3s;
        }
        
        body.single-card-view .card.active-card {
            display: flex;
            opacity: 1;
            transform: scale(1);
            z-index: 100;
        }
        
        /* Instagram-like progress indicators */
        body.single-card-view .carousel-indicators {
            position: absolute;
            top: 10px;
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
            width: 100%;
            z-index: 15;
        }
        
        body.single-card-view .carousel-indicators button {
            width: 100%;
            height: 2px !important;
            border-radius: 0 !important;
            background-color: rgba(255, 255, 255, 0.3) !important;
            margin: 0 2px !important;
            opacity: 1 !important;
        }
        
        body.single-card-view .carousel-indicators button.active {
            background-color: white !important;
        }
        
        /* Instagram-like arrows for navigation */
        body.single-card-view .arrow-indicator {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            z-index: 1000;
        }
        
        body.single-card-view .arrow-left {
            left: 10px;
        }
        
        body.single-card-view .arrow-right {
            right: 10px;
        }
        
        /* Instagram-like "Close" button */
        body.single-card-view .close-fullscreen {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
            color: white;
            background: none;
            border: none;
            font-size: 1.5rem;
        }
        
        /* Additional Instagram-like styling for animal info */
        body.single-card-view .card [style*="flex-wrap: wrap"] span {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            padding: 2px 8px;
            margin: 3px;
            backdrop-filter: blur(3px);
        }
    }

    /* Mobile navigation controls */


    @media (max-width: 768px) {
        /* Action buttons positioning */
        .card .action-buttons {
            position: absolute !important;
            bottom: 80px !important;
            right: 15px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            z-index: 950 !important;
            justify-content: center !important;
            margin: 0 !important;
        }
        
        .card .action-btn {
            margin: 8px 0 !important;
            background-color: rgba(0, 0, 0, 0.2) !important; /* Reduced contrast background */
            border-radius: 50% !important;
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            backdrop-filter: blur(2px) !important; /* Light blur effect */
        }
        
        .card .action-btn i {
            font-size: 1.2rem !important;
            color: white !important;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.9) !important;
        }
    }

    /* Add this inside your existing mobile media query */
    @media (max-width: 768px) {
        /* Override action buttons styles for mobile */
        .mobile-action-buttons {
            position: fixed;
            bottom: 80px;
            right: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1000;
        }
        
        .mobile-action-buttons .action-btn {
            margin: 8px 0;
            width: 45px;
            height: 45px;
            background-color: rgba(0, 0, 0, 0.2) !important; /* Reduced contrast background */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(2px) !important; /* Light blur effect */
        }
        
        .mobile-action-buttons .action-btn i {
            font-size: 1.3rem !important;
            color: white !important;
        }
        
        /* Improved styling for specific buttons */
        .mobile-action-buttons .update-btn i {
            color: yellow !important;
        }
        
        .mobile-action-buttons .history-btn i {
            color: #4248f5 !important;
        }
        
        .mobile-action-buttons .delete-btn i {
            color: #ff3b30 !important;
        }
    }

    /* Add this JavaScript to handle mobile action buttons */
    
    /* Search Box Styles */
    .search-box-container {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    
    .search-container {
        position: relative;
        margin-bottom: 0;
    }
    
    .search-container .input-group {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 25px;
        overflow: hidden;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    
    .search-container .input-group:focus-within {
        border-color: #4e6c41;
        box-shadow: 0 6px 20px rgba(78, 108, 65, 0.2);
        transform: translateY(-2px);
    }
    
    .search-container .input-group-text {
        background: linear-gradient(135deg, #4e6c41, #759c63);
        color: white;
        border: none;
        padding: 5px 15px;
    }
    
    .search-container #animalSearch {
        border: none;
        padding: 5px 15px;
        font-size: 16px;
        background: white;
        transition: all 0.3s ease;
    }
    
    .search-container #animalSearch:focus {
        outline: none;
        box-shadow: none;
        background: #fafafa;
    }
    
    .search-container #animalSearch::placeholder {
        color: #888;
        font-style: italic;
    }
    
    .search-results {
        font-size: 14px;
        color: #666;
        text-align: center;
        font-style: italic;
        opacity: 0;
        transition: opacity 0.3s ease;
        margin-top: 5px;
    }
    
    .search-results.visible {
        opacity: 1;
    }
    
    /* Card filtering animation */
    .card {
        transition: all 0.4s ease, opacity 0.3s ease, transform 0.3s ease;
    }
    
    .card.hidden {
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
        margin: 0;
        height: 0;
        overflow: hidden;
    }
    
    /* Responsive search box */
    @media (max-width: 768px) {
        .search-box-container {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        .search-container .col-md-8 {
            padding: 0 15px;
        }
        
        .search-container #animalSearch {
            font-size: 14px;
            padding: 5px 12px;
        }
        
        .search-container .input-group-text {
            padding: 5px 12px;
        }
    }
</style>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
    #chat { border: 1px solid #ccc; padding: 10px; background: #fff; height: 400px; overflow-y: scroll; }
    #inputContainer { margin-top: 10px; display: flex; }
    #userInput { flex: 1; padding: 8px; }
    #sendBtn { padding: 8px 12px; }
</style>

<style>
    /* Remove the old basic chat styles */
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
    #chat { border: 1px solid #ccc; padding: 10px; background: #fff; height: 400px; overflow-y: scroll; }
    #inputContainer { margin-top: 10px; display: flex; }
    #userInput { flex: 1; padding: 8px; }
    #sendBtn { padding: 8px 12px; }
</style>

<style>
    /* Chat Bubble Keyframe Animations */
    @keyframes bubblePulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 4px 20px rgba(75, 128, 70, 0.4);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(75, 128, 70, 0.5);
        }
    }

    @keyframes iconBounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-2px);
        }
    }

    /* Chat Bubble Styles */
    .chat-bubble {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4b8046, #37a446);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(75, 128, 70, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        overflow: hidden;
        animation: bubblePulse 3s ease-in-out infinite;
    }

    .chat-bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 30px rgba(75, 128, 70, 0.7);
        animation: bubblePulse 1.5s ease-in-out infinite;
    }

    .chat-bubble.expanded {
        width: 200px;
        border-radius: 30px;
    }

    .bubble-icon {
        color: white;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .chat-bubble:hover .bubble-icon {
        animation: iconBounce 0.6s ease-in-out infinite;
    }

    .chat-bubble.expanded .bubble-icon {
        font-size: 1.2rem;
    }

    .bubble-text {
        display: none;
    }

    .chat-bubble.expanded .bubble-text {
        display: none;
    }

    /* Full Screen Chat Styles - ChatGPT Dark Theme */
    .chat-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #212121;
        backdrop-filter: blur(10px);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform: scale(0.8);
        opacity: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
    }

    .chat-fullscreen.active {
        transform: scale(1);
        opacity: 1;
    }

    .hidden {
        display: none !important;
    }

    .chat-header {
        background: #2d2d2d;
        color: #ffffff;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        border-bottom: 1px solid #404040;
    }

    .chat-title {
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        color: #ffffff;
    }

    .chat-title i {
        color: #10a37f;
        margin-right: 8px;
    }

    .close-chat-btn {
        background: #404040;
        border: none;
        color: #ffffff;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .close-chat-btn:hover {
        background: #525252;
        transform: scale(1.05);
    }

    .chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        background: #212121;
        scrollbar-width: thin;
        scrollbar-color: #404040 #212121;
    }

    .chat-body::-webkit-scrollbar {
        width: 6px;
    }

    .chat-body::-webkit-scrollbar-track {
        background: #212121;
    }

    .chat-body::-webkit-scrollbar-thumb {
        background: #404040;
        border-radius: 3px;
    }

    .chat-body::-webkit-scrollbar-thumb:hover {
        background: #525252;
    }

    .chat-input-container {
        background: #2d2d2d;
        padding: 16px 20px;
        border-top: 1px solid #404040;
        display: flex;
        gap: 12px;
        align-items: center;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.3);
    }

    /* Modern ChatPDF Integration Styles - ChatGPT Dark Theme */
    .upload-section {
        background: #2d2d2d;
        padding: 20px;
        margin: 0;
        border-bottom: 1px solid #404040;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    #chat {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #212121;
        margin: 0;
        width: 100%;
        box-sizing: border-box;
        min-height: 400px;
    }

    .welcome-message {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .chat-message {
        margin: 10px 0;
        display: flex;
        flex-direction: column;
    }

    .user-message {
        align-items: flex-end;
    }

    .bot-message {
        align-items: flex-start;
    }

    .message-bubble {
        max-width: 80%;
        padding: 12px 16px;
        border-radius: 18px;
        margin: 4px 0;
        word-wrap: break-word;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }

    .user-message .message-bubble {
        background-color: #10a37f;
        color: #ffffff;
        border-bottom-right-radius: 6px;
        margin-left: auto;
    }

    .user-message .message-bubble:hover {
        background-color: #0d8a6b;
        transform: translateY(-1px);
    }

    .bot-message .message-bubble {
        background-color: #2d2d2d;
        color: #ffffff;
        border: 1px solid #404040;
        border-bottom-left-radius: 6px;
    }

    .bot-message .message-bubble:hover {
        background-color: #353535;
        transform: translateY(-1px);
    }

    .system-message .message-bubble {
        background-color: #3d3d3d;
        color: #ffd700;
        border: 1px solid #555555;
    }

    .error-message .message-bubble {
        background-color: #4a1a1a;
        color: #ff6b6b;
        border: 1px solid #6b2c2c;
        white-space: pre-line;
    }

    .message-bubble i {
        margin-right: 8px;
    }

    .chat-input-container #userInput {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #404040;
        border-radius: 20px;
        outline: none;
        font-size: 16px;
        box-sizing: border-box;
        background: #404040;
        color: #ffffff;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .chat-input-container #userInput::placeholder {
        color: #a0a0a0;
    }

    .chat-input-container #userInput:focus {
        border-color: #10a37f;
        box-shadow: 0 0 0 2px rgba(16, 163, 127, 0.2);
        background: #4a4a4a;
    }

    .chat-input-container #sendBtn {
        width: 44px;
        height: 44px;
        background: #10a37f;
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(16, 163, 127, 0.3);
        position: relative;
        overflow: hidden;
    }

    .chat-input-container #sendBtn:hover:not(:disabled) {
        background: #0d8a6b;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(16, 163, 127, 0.4);
    }

    .chat-input-container #sendBtn:active {
        transform: scale(0.95);
    }

    .chat-input-container #sendBtn:disabled {
        background: #404040;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .chat-input-container #sendBtn i {
        color: white;
        font-size: 18px;
        z-index: 1;
        position: relative;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .chat-bubble {
            bottom: 20px;
            left: 20px;
            width: 55px;
            height: 55px;
        }

        .chat-bubble.expanded {
            width: 180px;
        }

        .chat-header {
            padding: 15px;
        }

        .chat-title {
            font-size: 1.2rem;
        }

        .chat-input-container {
            padding: 15px;
            gap: 10px;
        }

        .chat-input-container #userInput {
            padding: 12px 16px;
            font-size: 16px;
        }

        .chat-input-container #sendBtn {
            width: 45px;
            height: 45px;
            font-size: 16px;
        }
    }

    @media (max-width: 768px) {
        .chat-section {
            margin: 1rem;
            border-radius: 8px;
        }

        #chat {
            height: 350px;
            margin: 0.8rem;
            padding: 1rem;
        }

        #inputContainer {
            padding: 0.8rem;
        }

        #sendBtn {
            padding: 0.8rem 1rem;
        }
    }

    .upload-status {
        margin-top: 10px;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 500;
        text-align: center;
    }

    .upload-status.info {
        background-color: #e3f2fd;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }

    .upload-status.success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .upload-status.error {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
        white-space: pre-line;
    }

    .error-message .message-bubble {
        background-color: #ffebee !important;
        border: 1px solid #ffcdd2 !important;
        color: #c62828 !important;
        white-space: pre-line;
    }

    .system-message .message-bubble {
        background-color: #fff3cd !important;
        border: 1px solid #ffeeba !important;
        color: #856404 !important;
    }

    .message-content {
        line-height: 1.6;
    }

    .message-content strong {
        font-weight: 600;
        color: inherit;
    }

    .message-content ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .message-content li {
        margin-bottom: 5px;
    }

    /* Ensure chat starts hidden */
    .chat-fullscreen {
        display: none;
    }

    .chat-fullscreen:not(.hidden) {
        display: flex;
    }

    /* Prompt Menu Styles */
    .prompt-menu {
        padding: 10px 0;
    }

    .prompt-buttons {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin: 15px 0;
        justify-content: center;
    }

    .prompt-btn {
        background: #404040;
        border: 1px solid #525252;
        border-radius: 10px;
        padding: 12px 16px;
        text-align: left;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #ffffff;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .prompt-btn:hover {
        border-color: #10a37f;
        background: #10a37f;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 163, 127, 0.3);
    }

    .prompt-btn i {
        margin-right: 10px;
        font-size: 16px;
        width: 20px;
        text-align: center;
        color: #10a37f;
    }

    .prompt-btn:hover i {
        color: white;
    }

    /* Responsive layout for prompt buttons */
    @media (max-width: 1199px) {
        .prompt-buttons {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 991px) {
        .prompt-buttons {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 767px) {
        .prompt-buttons {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .prompt-btn {
            font-size: 13px;
            padding: 10px 14px;
        }
    }

    @media (max-width: 575px) {
        .prompt-buttons {
            grid-template-columns: 1fr;
            gap: 8px;
        }
    }

    /* Menu Button Styles */
    .menu-btn {
        width: 44px;
        height: 44px;
        background: #404040;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .menu-btn:hover {
        background: #525252;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .menu-btn:active {
        transform: scale(0.95);
    }

    .menu-btn.active {
        background: #10a37f;
        box-shadow: 0 4px 12px rgba(16, 163, 127, 0.4);
    }

    /* Floating Prompt Menu Styles */
    .floating-prompt-menu {
        position: absolute;
        bottom: 70px;
        left: 20px;
        right: 20px;
        background: #2d2d2d;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        z-index: 1000;
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #404040;
        transition: all 0.3s ease;
    }

    .floating-prompt-menu.hidden {
        display: none;
    }

    .floating-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #404040;
        background: #353535;
        border-radius: 15px 15px 0 0;
        color: #ffffff;
    }

    .floating-menu-header h4 {
        margin: 0;
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .floating-menu-header h4 i {
        margin-right: 10px;
        color: #10a37f;
    }

    .close-menu-btn {
        background: #404040;
        border: none;
        color: #ffffff;
        font-size: 16px;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close-menu-btn:hover {
        background: #525252;
        transform: scale(1.05);
    }

    .floating-menu-content {
        padding: 20px;
        background: #2d2d2d;
    }

    .floating-menu-content .prompt-buttons {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin: 0;
        justify-content: center;
    }

    /* Mobile responsive for floating menu */
    @media (max-width: 768px) {
        .floating-prompt-menu {
            bottom: 60px;
            left: 10px;
            right: 10px;
            max-height: 400px;
        }

        .floating-menu-header {
            padding: 12px 15px;
        }

        .floating-menu-header h4 {
            font-size: 16px;
        }

        .floating-menu-content {
            padding: 15px;
        }

        .floating-menu-content .prompt-buttons {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
    }

    @media (max-width: 1199px) {
        .floating-menu-content .prompt-buttons {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 991px) {
        .floating-menu-content .prompt-buttons {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 575px) {
        .floating-menu-content .prompt-buttons {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .menu-btn {
            width: 40px;
            height: 40px;
            font-size: 14px;
            margin-right: 8px;
        }
    }
</style>

<!-- Fullscreen Modal for Images and Videos -->
<div class="modal fade" id="fullscreenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center">
                <img id="fullscreenImage" src="" alt="Fullscreen Image" style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
                <video id="fullscreenVideo" controls style="max-height: 90vh; max-width: 90vw; display: none;">
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview function for both new and update modals
function previewImage(event, previewId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(previewId);
        output.src = reader.result;
        
        // Show the correct carousel item
        if (previewId.includes('update')) {
            // For update modal
            const carouselItems = document.querySelectorAll('#updateImagePreviewCarousel .carousel-item');
            carouselItems.forEach((item, index) => {
                if (item.querySelector('img') && item.querySelector('img').id === previewId) {
                    const carousel = bootstrap.Carousel.getInstance(document.getElementById('updateImagePreviewCarousel'));
                    carousel.to(index);
                }
            });
        } else {
            // For new modal
            const carouselItems = document.querySelectorAll('#newImagePreviewCarousel .carousel-item');
            carouselItems.forEach((item, index) => {
                if (item.querySelector('img') && item.querySelector('img').id === previewId) {
                    const carousel = bootstrap.Carousel.getInstance(document.getElementById('newImagePreviewCarousel'));
                    carousel.to(index);
                }
            });
        }
    }
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Video preview function
function previewVideo(event, previewId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(previewId);
        output.src = reader.result;
        
        // Update source
        output.querySelector('source').src = reader.result;
        output.load(); // Important to reload the video element
        
        // Show the correct carousel item (last one for video)
        if (previewId.includes('update')) {
            const carousel = bootstrap.Carousel.getInstance(document.getElementById('updateImagePreviewCarousel'));
            const carouselItems = document.querySelectorAll('#updateImagePreviewCarousel .carousel-item');
            carousel.to(carouselItems.length - 1);
        } else {
            const carousel = bootstrap.Carousel.getInstance(document.getElementById('newImagePreviewCarousel'));
            const carouselItems = document.querySelectorAll('#newImagePreviewCarousel .carousel-item');
            carousel.to(carouselItems.length - 1);
        }
    }
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Function to open image in fullscreen modal
function openFullscreen(src) {
    const fullscreenImage = document.getElementById('fullscreenImage');
    const fullscreenVideo = document.getElementById('fullscreenVideo');
    
    fullscreenImage.src = src;
    fullscreenImage.style.display = 'block';
    fullscreenVideo.style.display = 'none';
    
    const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
    fullscreenModal.show();
}

// Function to open video in fullscreen modal
function openFullscreenVideo(videoElement) {
    const fullscreenImage = document.getElementById('fullscreenImage');
    const fullscreenVideo = document.getElementById('fullscreenVideo');
    
    fullscreenVideo.src = videoElement.querySelector('source').src;
    fullscreenVideo.load();
    
    fullscreenImage.style.display = 'none';
    fullscreenVideo.style.display = 'block';
    
    const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
    fullscreenModal.show();
}

// Initialize carousels when modals are shown
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('newEntryModal').addEventListener('shown.bs.modal', function() {
        new bootstrap.Carousel(document.getElementById('newImagePreviewCarousel'), {
            interval: 5200
        });
    });
    
    document.getElementById('updateModal').addEventListener('shown.bs.modal', function() {
        new bootstrap.Carousel(document.getElementById('updateImagePreviewCarousel'), {
            interval: 5200
        });
    });
});
</script>

<body>
<!-- Navigation Title -->
<nav class="navbar text-center">
    <!-- Title Row -->
    <div class="container-fluid">
        <div class="row w-100">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <h1 class="navbar-title text-center mx-auto">
                <i class="fas fa-user-md me-2"></i>VETERINARIO<span class="ms-2"></span>
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

<!-- Dynamic Search Box -->
<div class="container search-box-container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="search-container">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           id="animalSearch" 
                           class="form-control" 
                           placeholder="Buscar por Tag ID, nombre, etapa, estatus, población..."
                           autocomplete="off">
                </div>
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add this script after your buttons -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.scroll-icons-container button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            
            // Get the target section ID from data-bs-target attribute
            const targetId = this.getAttribute('data-bs-target');
            const targetElement = document.getElementById(targetId.substring(1)); // Remove the # from the ID
            
            if (targetElement) {
                // Smooth scroll to the target section
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // If using Bootstrap collapse, toggle it
                const bsCollapse = new bootstrap.Collapse(targetElement, {
                    toggle: true
                });
            }
        });
    });
});
</script>

<!-- Dynamic Search Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('animalSearch');
    const searchResults = document.getElementById('searchResults');
    const cardsContainer = document.querySelector('.cards-container');
    
    if (!searchInput || !searchResults || !cardsContainer) {
        console.error('Search elements not found');
        return;
    }
    
    // Get all cards
    let allCards = [];
    
    function updateCardsList() {
        allCards = Array.from(cardsContainer.querySelectorAll('.card'));
    }
    
    // Initial cards list
    updateCardsList();
    
    // Extract text content from a card for searching
    function getCardSearchText(card) {
        const cardData = {
            tagid: '',
            nombre: '',
            etapa: '',
            estatus: '',
            fecha_nacimiento: ''
        };
        
        // Extract tagid and nombre from the header section
        const headerDiv = card.querySelector('[style*="margin-top: 5px"]');
        if (headerDiv) {
            const nombreDiv = headerDiv.querySelector('[style*="font-weight: bolder"]');
            const tagidDiv = headerDiv.querySelector('[style*="text-shadow: 1px 1px 3px"]');
            
            if (nombreDiv) cardData.nombre = nombreDiv.textContent.trim();
            if (tagidDiv) {
                const tagText = tagidDiv.textContent.trim();
                cardData.tagid = tagText.replace(/^.*?\s+/, ''); // Remove icon and get tagid
            }
        }
        
        // Extract other info from info fields using positional logic (more reliable)
        const infoFields = card.querySelectorAll('.info-field');
        
        // Fields appear in this order: fecha_nacimiento, etapa, estatus, poblacion
        infoFields.forEach((field, index) => {
            const text = field.textContent.trim();
            
            // The textContent doesn't include the FontAwesome icons, so use the text directly
            let fieldValue = text.trim();
            
            // Assign based on field position
            switch(index) {
                case 0:
                    cardData.fecha_nacimiento = fieldValue;
                    break;
                case 1:
                    cardData.etapa = fieldValue;
                    break;
                case 2:
                    cardData.estatus = fieldValue;
                    break;
                case 3:
                    cardData.poblacion = fieldValue;
                    break;
            }
        });
        
        // Combine all searchable text, prioritizing the key fields the user mentioned
        const searchableText = [
            cardData.tagid,
            cardData.nombre,
            cardData.etapa,
            cardData.estatus,
            cardData.poblacion,
            cardData.fecha_nacimiento
        ].filter(field => field && field.trim() !== '').join(' ').toLowerCase();
        
        return searchableText;
    }
    
    // Filter function
    function filterCards(searchTerm) {
        if (!searchTerm.trim()) {
            // Show all cards
            allCards.forEach(card => {
                card.classList.remove('hidden');
            });
            updateSearchResults(allCards.length, allCards.length);
            return;
        }
        
        const terms = searchTerm.toLowerCase().trim().split(/\s+/);
        let visibleCount = 0;
        
        allCards.forEach(card => {
            const cardText = getCardSearchText(card);
            const matches = terms.every(term => cardText.includes(term));
            
            if (matches) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });
        
        updateSearchResults(visibleCount, allCards.length);
    }
    
    // Update search results text
    function updateSearchResults(visible, total) {
        if (searchInput.value.trim()) {
            searchResults.textContent = `Mostrando ${visible} de ${total} animales`;
            searchResults.classList.add('visible');
        } else {
            searchResults.classList.remove('visible');
        }
    }
    
    // Debounce function for better performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Debounced filter function
    const debouncedFilter = debounce(filterCards, 300);
    
    // Event listeners
    searchInput.addEventListener('input', function() {
        debouncedFilter(this.value);
    });
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterCards('');
        }
    });
    
    // Update cards list when cards are dynamically added/removed
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                updateCardsList();
            }
        });
    });
    
    observer.observe(cardsContainer, {
        childList: true,
        subtree: true
    });
    
    // Initial setup
    updateSearchResults(allCards.length, allCards.length);
});
</script>

<!-- Poblacion camarones -->

<!-- Chat Bubble (Collapsed State) -->
<div id="chatBubble" class="chat-bubble">
    <div class="bubble-icon">
        <i class="fas fa-user-doctor"></i>
    </div>
    <div class="bubble-text">Vet IA</div>
</div>

<!-- Full Screen Chat (Expanded State) -->
<div id="chatFullscreen" class="chat-fullscreen hidden">
    <div class="chat-header">
        <div class="chat-title">
            <i class="fas fa-user-doctor me-2"></i>
            Veterinario ChatGPT
        </div>
        <button id="closeChatBtn" class="close-chat-btn">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="chat-body">
        <div class="upload-section" id="uploadSection">
            <div class="upload-container">
                <input type="file" id="pdfFile" accept=".pdf" class="file-input" />
                <div id="fileName" class="file-name"></div>
                <button id="uploadBtn" class="upload-btn" disabled>
                    <i class="fas fa-upload" style="margin-right: 8px;"></i>Subir PDF Manual
                </button>
            </div>
            <div id="uploadStatus" class="upload-status"></div>
        </div>
        
        <div id="chat">
            <div class="welcome-message">
                <div class="chat-message bot-message">
                    <div class="message-bubble">
                        <i class="fas fa-user-doctor" style="color: #4b8046;"></i>
                        <div class="message-content">
                            ¡Hola! Soy el Veterinario ChatGPT. <br><br>
                            Por favor, ingresa el <strong>Tag ID</strong> o <strong>nombre</strong> del animal para generar y analizar su historial médico.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="chat-input-container">
        <button id="menuBtn" class="menu-btn" title="Mostrar consultas rápidas">
            <i class="fas fa-bars"></i>
        </button>
        <input type="text" id="userInput" placeholder="Ingresa Tag ID o nombre del animal..." />
        <button id="sendBtn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
    
    <!-- Floating Menu Prompt Menu -->
    <div id="floatingPromptMenu" class="floating-prompt-menu hidden">
        <div class="floating-menu-header">
            <h4><i class="fas fa-stethoscope"></i> Consultas Financieras</h4>
            <button id="closeMenuBtn" class="close-menu-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="floating-menu-content">
            <div class="prompt-buttons">
                <button class="prompt-btn" onclick="selectPromptFromMenu('Informe Financiero del Estanque y total')">
                    <i class="fas fa-weight"></i> Informe Financiero del Estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Ingresos Biomasa por tanque y total')">
                    <i class="fas fa-egg"></i> Ingresos Biomasa por tanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por concentrado por tanque y total')">
                    <i class="fas fa-seedling"></i> Egresos por concentrado por tanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por harinas proteicas por tanque y total')">
                <i class="fa-solid fa-droplet"></i> Egresos por harinas proteicas por tanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por fermentados por tanque y total')">
                    <i class="fas fa-cube"></i> Egresos por fermentados por tanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control oxigenacion por estanque y total')">
                    <i class="fas fa-syringe"></i> Egresos por gastos en control oxigenacion por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control amoniaco por estanque y total')">
                    <i class="fas fa-chart-pie"></i> Egresos por gastos en control amoniaco por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control nitritos por estanque y total')">
                    <i class="fas fa-layer-group"></i> Egresos por gastos en control nitritos por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control alcalinidad por estanque y total')">
                    <i class="fas fa-seedling"></i> Egresos por gastos en control alcalinidad por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control transparencia por estanque y total')">
                    <i class="fas fa-calculator"></i> Egresos por gastos en control transparencia por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control salinidad por estanque y total')">
                    <i class="fas fa-syringe"></i> Egresos por gastos en control salinidad por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control redox por estanque y total')">
                    <i class="fas fa-female"></i> Egresos por gastos en control redox por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Egresos por gastos en control alcalinidad por estanque y total')">
                    <i class="fas fa-cubes"></i> Egresos por gastos en control alcalinidad por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Estructura ed costos variables por estanque y total')">
                    <i class="fas fa-cubes"></i> Estructura ed costos variables por estanque y total
                </button>
                <button class="prompt-btn" onclick="selectPromptFromMenu('Informe Tecnico General de los estanques')">
                    <i class="fas fa-cubes"></i> Informe Tecnico General de los estanques
                </button>
            </div>
            <p style="margin-top: 15px; font-size: 0.9em; color: #666; text-align: center;">
                Selecciona una consulta o cierra este menú para escribir tu propia pregunta
            </p>
        </div>
    </div>
</div>




<div class="cards-container">
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="card" data-id="' . $row['id'] . '" style="position: relative; padding: 0; border: 1px solid #ddd; display: flex; flex-direction: column; align-items: center; overflow: hidden; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">'; 
                
                // Create a carousel for each card
                echo '<div id="carousel-' . $row['id'] . '" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5200" style="width: 100%; height: 100%;">';
                
                // Carousel indicators
                echo '<div class="carousel-indicators">';
                echo '<button type="button" data-bs-target="#carousel-' . $row['id'] . '" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>';
                
                // Only add indicators if images/video exist
                if(!empty($row['image2'])) {
                    echo '<button type="button" data-bs-target="#carousel-' . $row['id'] . '" data-bs-slide-to="1" aria-label="Slide 2"></button>';
                }
                if(!empty($row['image3'])) {
                    echo '<button type="button" data-bs-target="#carousel-' . $row['id'] . '" data-bs-slide-to="2" aria-label="Slide 3"></button>';
                }
                if(!empty($row['video'])) {
                    echo '<button type="button" data-bs-target="#carousel-' . $row['id'] . '" data-bs-slide-to="' . 
                        ((!empty($row['image2']) ? 1 : 0) + (!empty($row['image3']) ? 1 : 0) + 1) . '" aria-label="Video"></button>';
                }
                echo '</div>';
                
                // Carousel items
                echo '<div class="carousel-inner" style="width: 100%; height: 100%;">';
                
                // Main image (always present)
                echo '<div class="carousel-item active" style="height: 100%;">';
                if(!empty($row['image'])) {
                    echo '<img src="' . htmlspecialchars($row['image']) . '" alt="Imagen principal" onclick="openFullscreen(\'' . htmlspecialchars($row['image']) . '\')" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; cursor: pointer;">'; 
                } else {
                    echo '<img src="./uploads/default_image.png" alt="Default Imagen" onclick="openFullscreen(\'./uploads/default_image.png\')" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; cursor: pointer;">'; 
                }
                echo '</div>';
                
                // Image 2 (optional)
                if(!empty($row['image2'])) {
                    echo '<div class="carousel-item" style="height: 100%;">';
                    echo '<img src="' . htmlspecialchars($row['image2']) . '" alt="Imagen 2" onclick="openFullscreen(\'' . htmlspecialchars($row['image2']) . '\')" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; cursor: pointer;">'; 
                    echo '</div>';
                }
                
                // Image 3 (optional)
                if(!empty($row['image3'])) {
                    echo '<div class="carousel-item" style="height: 100%;">';
                    echo '<img src="' . htmlspecialchars($row['image3']) . '" alt="Imagen 3" onclick="openFullscreen(\'' . htmlspecialchars($row['image3']) . '\')" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; cursor: pointer;">'; 
                    echo '</div>';
                }
                
                // Video (optional)
                if(!empty($row['video'])) {
                    echo '<div class="carousel-item" style="height: 100%;">';
                    echo '<video class="card-video" controls muted 
                            style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; cursor: pointer;"
                            onclick="openFullscreenVideo(this)">';
                    echo '<source src="' . htmlspecialchars($row['video']) . '" type="video/mp4">';
                    echo 'Your browser does not support the video tag.';
                    echo '</video>';
                    echo '</div>';
                }
                
                echo '</div>'; // End carousel-inner
                
                // Controls - only if more than one image/video
                if(!empty($row['image2']) || !empty($row['image3']) || !empty($row['video'])) {
                    echo '<button class="carousel-control-prev" type="button" data-bs-target="#carousel-' . $row['id'] . '" data-bs-slide="prev">';
                    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                    echo '<span class="visually-hidden">Previous</span>';
                    echo '</button>';
                    echo '<button class="carousel-control-next" type="button" data-bs-target="#carousel-' . $row['id'] . '" data-bs-slide="next">';
                    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                    echo '<span class="visually-hidden">Next</span>';
                    echo '</button>';
                }
                
                echo '</div>'; // End carousel
                
                echo '</div>

            <!-- Animal name and tag at top - now without background -->
            <div style="position: relative; z-index: 3; padding: 10px; text-align: center; margin-top: 5px; width: 100%;">
                <div style="font-weight: bolder; color: #ffffff; text-shadow: 2px 2px 4px rgba(0,0,0,0.9), 0 0 3px rgba(0,0,0,0.9); font-size: 1.2rem;">' . htmlspecialchars($row['nombre']) . '</div>
                <div style="color: #ffffff; text-shadow: 1px 1px 3px rgba(0,0,0,0.9), 0 0 2px rgba(0,0,0,0.9);"><i class="fas fa-tag"></i> ' . htmlspecialchars($row['tagid']) . '</div>
            </div>

            <!-- Spacer to push everything else to bottom -->
            <div style="flex-grow: 1;"></div>
            
            <!-- Info labels at bottom - now without backgrounds -->
            <div class="info-container" style="position: relative; z-index: 3; width: 100%; display: flex; flex-wrap: wrap; justify-content: center; padding: 5px 10px;">
                <div class="info-table" style="display: grid; grid-template-columns: 1fr 1fr; width: 100%; gap: 5px;">
                    <!-- Left Column -->
                    <div class="info-column" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <span class="info-field" style="display: inline-block; margin: 2px 5px; color: white; font-size: 0.85rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.9), 0 0 2px rgba(0,0,0,0.9);">
                            <i class="fa-solid fa-baby fa-2xs"></i> ' . htmlspecialchars($row['fecha_nacimiento']) . '
                        </span>
                        <span class="info-field" style="display: inline-block; margin: 2px 5px; color: white; font-size: 0.85rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.9), 0 0 2px rgba(0,0,0,0.9);">
                            <i class="fa-solid fa-layer-group fa-2xs"></i> ' . htmlspecialchars($row['etapa']) . '
                        </span>
                    </div>
                    <!-- Right Column -->
                    <div class="info-column" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <span class="info-field" style="display: inline-block; margin: 2px 5px; color: white; font-size: 0.85rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.9), 0 0 2px rgba(0,0,0,0.9);">
                            <i class="fa-solid fa-calendar-days fa-2xs"></i> ' . intval($row['edad']) . ' días
                        </span>
                        <span class="info-field" style="display: inline-block; margin: 2px 5px; color: white; font-size: 0.85rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.9), 0 0 2px rgba(0,0,0,0.9);">
                            <i class="fa-solid fa-fish fa-2xs"></i> ' . number_format($row['poblacion']) . '
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="action-buttons" style="position: relative; z-index: 3; display: flex; justify-content: center; margin-bottom: 8px; margin-top: 5px;">
                <button class="action-btn update-btn" 
                        title="Actualizar" 
                        onclick="openUpdateModal(\'' . htmlspecialchars($row['tagid']) . '\')" 
                        style="background: rgba(0,0,0,0.9); border: none; padding: 0; margin: 0 8px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; box-shadow: 0 1px 1px rgba(0,0,0,0.9);">
                    <i class="fa-regular fa-pen-to-square" style="color:hsl(165, 85.40%, 48.20%); font-size: 1.1rem;"></i>
                </button>
                <button class="action-btn history-btn" 
                        title="Reporte Compartir" 
                        onclick="registrar(\'' . htmlspecialchars($row['tagid']) . '\')"
                        style="background: rgba(0,0,0,0.9); border: none; padding: 0; margin: 0 8px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; box-shadow: 0 1px 1px rgba(0,0,0,0.9);">
                    <i class="fa-solid fa-share-from-square" style="color:hsl(165, 85.40%, 48.20%);font-size: 1.1rem;"></i>
                </button>
                <button class="action-btn chat-pdf-btn" 
                        title="Veterinario ChatGPT" 
                        onclick="generateAndUploadPDF(\'' . htmlspecialchars($row['tagid']) . '\')"
                        style="background: rgba(0,0,0,0.9); border: none; padding: 0; margin: 0 8px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; box-shadow: 0 1px 1px rgba(0,0,0,0.9);">                    
                    <i class="fa-solid fa-user-doctor" style="color:hsl(165, 85.40%, 48.20%);font-size: 1.1rem;"></i>
                </button>
                <button class="action-btn delete-btn" 
                        title="Borrar" 
                        onclick="deleteAnimal(this, ' . $row['id'] . ')"
                        style="background: rgba(0,0,0,0.9); border: none; padding: 0; margin: 0 8px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; box-shadow: 0 1px 1px rgba(0,0,0,0.9);">
                    <i class="fas fa-trash" style="color:hsl(165, 85.40%, 48.20%); font-size: 1.1rem;"></i>
                </button>
            </div>
        </div>';
    }
} else {
    echo "<p>No information found</p>";
}
?>
</div>

<!-- Borrar Animal -->
<script>
    function deleteAnimal(button, id) {
        // Confirm deletion
        if (!confirm('¿Está seguro de que desea borrar este animal? Esta acción no se puede deshacer.')) {
            return;
        }

        // Send AJAX request using jQuery
        $.ajax({
            url: './camarones_delete_animal.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Animal borrado exitosamente.');
                    // Remove the card from the UI
                    $(button).closest('.card').remove();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Ocurrió un error al procesar la solicitud.');
                console.error(error);
            }
        });
    }

    // Function to generate and share animal report
    function registrar(tagid) {
        if (confirm('¿Desea generar un informe completo para este animal?')) {
            // Redirect to the report generation page
            window.location.href = 'camarones_report.php?tagid=' + encodeURIComponent(tagid);
        }
    }
</script>
<script>
    // Add this at the start of your script section
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all modals
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            new bootstrap.Modal(modal);
        });
    });

    // Add this function for the update modal image preview
    function previewUpdateImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('updateImagePreview');
            output.src = reader.result;
        }
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Update the openUpdateModal function to include the image upload handler
    function openUpdateModal(tagid) {
        console.log('Opening modal for tagid:', tagid);
        console.log('Current timestamp:', new Date().toISOString());
        
        const modalElement = document.getElementById('updateModal');
        if (!modalElement) {
            console.error('Modal element not found');
            alert('Error: Modal element not found');
            return;
        }

        const modal = new bootstrap.Modal(modalElement, {
            keyboard: true,
            backdrop: true
        });
        
        console.log('Starting AJAX request to fetch_camarones_data.php');
        
        $.ajax({
            url: 'fetch_camarones_data.php',
            type: 'GET',
            data: { tagid: tagid },
            dataType: 'text', // Changed from 'json' to 'text' for better error handling
            beforeSend: function(xhr) {
                console.log('AJAX request being sent...');
            },
            success: function(response) {
                console.log('AJAX request completed successfully');
                console.log('Response type:', typeof response);
                console.log('Response length:', response ? response.length : 'null');
                console.log('Raw response (first 500 chars):', response ? response.substring(0, 500) : 'null');
                
                // Check if response is empty
                if (!response || response.trim() === '') {
                    console.error('Empty response received');
                    alert('Error al cargar los datos del animal: Respuesta vacía del servidor');
                    return;
                }
                
                // Check for HTML error pages
                if (response.trim().startsWith('<') || response.includes('<!DOCTYPE')) {
                    console.error('HTML response received instead of JSON');
                    console.error('Full HTML response:', response);
                    alert('Error al cargar los datos del animal: El servidor devolvió HTML en lugar de JSON\n\nRespuesta: ' + response.substring(0, 300));
                    return;
                }
                
                // Try to parse JSON manually
                let data;
                try {
                    data = JSON.parse(response);
                    console.log('JSON parsed successfully');
                } catch (e) {
                    console.error('JSON parsing error:', e);
                    console.error('JSON error name:', e.name);
                    console.error('JSON error message:', e.message);
                    console.error('Response that failed to parse (full):', response);
                    alert('Error: ' + e.message);
                    return;
                }

            
                console.log('Parsed data:', data);
                console.log('Data type:', typeof data);
                console.log('Data keys:', data ? Object.keys(data) : 'null');
                console.log('Debug - Full data object:', JSON.stringify(data, null, 2));
                
                if (data && data.error) {
                    console.error('Data error:', data.error);
                    alert('Error: ' + data.error);
                    return;
                }

                // Update image previews with correct paths
                // Main image
                const updateImagePreview = document.getElementById('updateImagePreview');
                if (updateImagePreview) {
                    if (data.image && data.image.trim() !== '') {
                        updateImagePreview.src = data.image;
                    } else {
                        updateImagePreview.src = 'images/default_image.png';
                    }
                }
                
                // Image 2
                const updateImage2Preview = document.getElementById('updateImage2Preview');
                if (updateImage2Preview) {
                    if (data.image2 && data.image2.trim() !== '') {
                        updateImage2Preview.src = data.image2;
                    } else {
                        updateImage2Preview.src = 'images/default_image.png';
                    }
                }
                
                // Image 3
                const updateImage3Preview = document.getElementById('updateImage3Preview');
                if (updateImage3Preview) {
                    if (data.image3 && data.image3.trim() !== '') {
                        updateImage3Preview.src = data.image3;
                    } else {
                        updateImage3Preview.src = 'images/default_image.png';
                    }
                }
                
                // Video
                const updateVideoPreview = document.getElementById('updateVideoPreview');
                if (updateVideoPreview) {
                    if (data.video && data.video.trim() !== '') {
                        const videoSource = updateVideoPreview.querySelector('source');
                        if (videoSource) {
                            videoSource.src = data.video;
                            updateVideoPreview.load();
                        }
                    } else {
                        const videoSource = updateVideoPreview.querySelector('source');
                        if (videoSource) {
                            videoSource.src = '';
                            updateVideoPreview.load();
                        }
                    }
                }

                // Helper function to safely set form values
                const setFieldValue = (id, value) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.value = value || '';
                        console.log(`Set ${id} to:`, value);
                    } else {
                        console.warn(`Element not found: ${id}`);
                    }
                };

                // Helper function to set dropdown values with validation
                const setDropdownValue = (id, value) => {
                    const element = document.getElementById(id);
                    if (element && value) {
                        // Check if the option exists
                        const option = Array.from(element.options).find(opt => opt.value === value);
                        if (option) {
                            element.value = value;
                            console.log(`Set dropdown ${id} to:`, value);
                        } else {
                            console.warn(`Option "${value}" not found in dropdown ${id}. Available options:`, 
                                Array.from(element.options).map(opt => opt.value));
                        }
                    } else if (!element) {
                        console.warn(`Dropdown element not found: ${id}`);
                    }
                };

                // Populate form fields immediately
                setFieldValue('updateAnimalId', data.id); // Hidden field for database ID
                setFieldValue('updateNombre', data.nombre);
                setFieldValue('updateTagid', data.tagid);
                setFieldValue('updateFechaNacimiento', data.fecha_nacimiento);
                setFieldValue('updateFechaCompra', data.fecha_compra);
                setFieldValue('updatePoblacion', data.poblacion || '');

                // Set dropdown values with a small delay to ensure DOM is ready
                setTimeout(() => {
                    setDropdownValue('updateEtapa', data.etapa);
                    setDropdownValue('updateEstatus', data.estatus);
                    
                    // Additional debug logging for Etapa and Grupo
                    console.log('Debug - Etapa data from server:', data.etapa);
                    console.log('Debug - updateEtapa element value after setting:', document.getElementById('updateEtapa')?.value);
                }, 200);

                // Show the modal
                modal.show();
                
                // Initialize carousel
                setTimeout(() => {
                    if (document.getElementById('updateImagePreviewCarousel')) {
                        new bootstrap.Carousel(document.getElementById('updateImagePreviewCarousel'), {
                            interval: 5200
                        });
                    }
                }, 500); // Small delay to ensure modal is fully shown
            },
            error: function(xhr, status, error) {
                console.error('Ajax error details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response status:', xhr.status);
                console.error('Response text:', xhr.responseText);
                
                let errorMessage = 'Error al cargar los datos del animal:\n';
                errorMessage += 'Status: ' + status + '\n';
                errorMessage += 'Error: ' + error + '\n';
                if (xhr.responseText) {
                    errorMessage += 'Response: ' + xhr.responseText.substring(0, 200);
                }
                
                alert(errorMessage);
            }
        });
    }

    // Rest of your existing openUpdateModal code...

    </script>
<!-- Add this script for image preview -->
    <script>
function previewImage(event, targetId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(targetId);
        if (output) {
            output.src = reader.result;
        }
    };
    if (event.target.files && event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Add video preview function
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
        }
    };
    if (event.target.files && event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

</script>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="updateModalLabel">
                    <i class="fas fa-edit me-2"></i>Actualizar Estanque
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" class="needs-validation" novalidate enctype="multipart/form-data">
                    <!-- Hidden field for animal ID -->
                    <input type="hidden" name="id" id="updateAnimalId">
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
                                        <label for="updateNombre">Estanque</label>
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
                                            $sql_etapa = "SELECT DISTINCT cac_etapas_nombre FROM cac_etapas ORDER BY cac_etapas_nombre";
                                            $result_etapa = $conn_etapa->query($sql_etapa);
                                            while ($row_etapa = $result_etapa->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_etapa['cac_etapas_nombre']) . '">' 
                                                    . htmlspecialchars($row_etapa['cac_etapas_nombre']) . '</option>';
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
                                            $sql_estatus = "SELECT DISTINCT cac_estatus_nombre FROM cac_estatus ORDER BY cac_estatus_nombre";
                                            $result_estatus = $conn_estatus->query($sql_estatus);
                                            while ($row_estatus = $result_estatus->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_estatus['cac_estatus_nombre']) . '">' 
                                                    . htmlspecialchars($row_estatus['cac_estatus_nombre']) . '</option>';
                                            }
                                            $conn_estatus->close();
                                            ?>
                                        </select>
                                        <label for="updateEstatus">Estatus</label>
                                    </div>
                                </div>
                                <!-- Poblacion -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="poblacion" id="updatePoblacion" required>
                                        <label for="updatePoblacion">Poblacion</label>
                                    </div>
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
                <button type="button" class="btn btn-outline-success" onclick="saveUpdates()">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
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
    
    // Add the action parameter for update
    formData.append('action', 'update');
    
    // Debug: Log form data (can be removed in production)
    console.log('Form data being sent:', Object.fromEntries(formData));
    
    // Add any fields that might not be in the form
    const tagid = document.getElementById('updateTagid');
    if (tagid) {
        formData.append('tagid', tagid.value);
    }

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
            console.error('Ajax error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
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

// Function to open update modal and populate with animal data
function openUpdateModal(tagid) {
    // Fetch animal data using tagid
    $.ajax({
        url: 'fetch_camarones_data.php',
        type: 'GET',
        data: { tagid: tagid },
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error
                });
                return;
            }
            
            // Debug: Log received data (can be removed in production)
            console.log('Modal data loaded:', data);
            
            // Populate form fields
            document.getElementById('updateAnimalId').value = data.id;
            document.getElementById('updateTagid').value = data.tagid;
            document.getElementById('updateNombre').value = data.nombre;
            document.getElementById('updateFechaNacimiento').value = data.fecha_nacimiento;
            document.getElementById('updateFechaCompra').value = data.fecha_compra || '';
            document.getElementById('updatePoblacion').value = data.poblacion || '';
            
            // Populate image previews
            populateImagePreviews(data);
            
            // Set dropdown values with a small delay to ensure DOM is ready
            setTimeout(() => {
                setDropdownValue('updateEtapa', data.etapa);
                setDropdownValue('updateEstatus', data.estatus);
                
                console.log('Debug - Etapa data from server:', data.etapa);
                console.log('Debug - updateEtapa element value after setting:', document.getElementById('updateEtapa')?.value);
            }, 200);
            
            // Show the modal
            const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
            updateModal.show();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching animal data:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los datos del animal'
            });
        }
    });
}

// Helper function to set dropdown values
function setDropdownValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element && value) {
        element.value = value;
    }
}

// Helper function to set field values
function setFieldValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.value = value || '';
    }
}

// Function to populate image previews in update modal
function populateImagePreviews(data) {
    const defaultImage = './images/default_image.png';
    
    // Update main image preview
    const imagePreview = document.getElementById('updateImagePreview');
    if (imagePreview) {
        imagePreview.src = data.image || defaultImage;
    }
    
    // Update image2 preview
    const image2Preview = document.getElementById('updateImage2Preview');
    if (image2Preview) {
        image2Preview.src = data.image2 || defaultImage;
    }
    
    // Update image3 preview
    const image3Preview = document.getElementById('updateImage3Preview');
    if (image3Preview) {
        image3Preview.src = data.image3 || defaultImage;
    }
    
    // Update video preview
    const videoPreview = document.getElementById('updateVideoPreview');
    if (videoPreview) {
        const source = videoPreview.querySelector('source');
        if (source) {
            if (data.video) {
                source.src = data.video;
                videoPreview.load(); // Reload the video element
            } else {
                source.src = ''; // Clear video source if no video
                videoPreview.load();
            }
        }
    }
    
    console.log('Image previews populated:', {
        image: data.image,
        image2: data.image2, 
        image3: data.image3,
        video: data.video
    });
}

// Function to preview selected image files
function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
}

// Function to preview selected video files
function previewVideo(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const preview = document.getElementById(previewId);
        if (preview) {
            const url = URL.createObjectURL(file);
            const source = preview.querySelector('source');
            if (source) {
                source.src = url;
                preview.load(); // Reload the video element
            }
        }
    }
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

<!-- Back to top button -->
<button id="backToTop" class="back-to-top" onclick="scrollToTop()" title="Volver arriba">
    <div class="arrow-up"><i class="fa-solid fa-arrow-up"></i></div>
</button>

<script>
// Show/hide back to top button based on scroll position
window.onscroll = function() {
    const backToTopButton = document.getElementById("backToTop");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        backToTopButton.style.display = "flex";
                } else {
        backToTopButton.style.display = "none";
    }
};

// Smooth scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
            });
        }
    </script>

<!-- Scroll to Section-->

    <script>
// Add event listeners to all scroll buttons
document.querySelectorAll('.scroll-Icons-container button').forEach(button => {
    button.addEventListener('click', function() {
        // Get the target section ID from data-target attribute
        const targetId = this.getAttribute('data-target').substring(1); // Remove the # from the ID
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            // Smooth scroll to the target section
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            // If using Bootstrap collapse, toggle it
            const bsCollapse = new bootstrap.Collapse(targetElement, {
                toggle: true
            });
        }
    });
        });
    </script>
<!-- Crear Nuevo Animal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Get form element
    const createEntryForm = document.getElementById('newEntryForm');
            const newEntryModal = document.getElementById('newEntryModal');

    if (createEntryForm) {
            // Handle form submission
            createEntryForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Create a FormData object from the form
                const formData = new FormData(createEntryForm);

            // Show loading state
            const submitButton = createEntryForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            submitButton.disabled = true;

                // Send the form data using fetch
            fetch('camarones_create.php', {
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
                        const imagePreview = document.getElementById('newImagePreview');
                        if (imagePreview) {
                            imagePreview.src = 'images/default_image.png';
                        }
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

        // Handle image preview for new entry
            const newImageUpload = document.getElementById('newImageUpload');
            const newImagePreview = document.getElementById('newImagePreview');

        if (newImageUpload && newImagePreview) {
            newImageUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newImagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    // Initialize Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
  });
</script>


<?php
$conn->close();
?> 

<!-- DataTables Scripts -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<!-- Make sure these exact versions of the libraries are included in your head section -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Back to Top Button -->
<button id="backToTopBtn" class="back-to-top btn btn-success" aria-label="Back to Top">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    // Back to Top button functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Get the button
        const backToTopButton = document.getElementById('backToTopBtn');
        
        // Function to handle scrolling
        function handleScroll() {
            if (window.pageYOffset > 300) { // Show button after scrolling 300px
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        }
        
        // Add scroll event listener
        window.addEventListener('scroll', handleScroll);
        
        // Scroll to top when button is clicked
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Initial check for button visibility
        handleScroll();
    });
</script>

<script>
// Function to detect mobile devices
function isMobile() {
    return window.innerWidth <= 768;
}

// Add class to body when viewing single card on mobile
function setupMobileCardView() {
    if (isMobile()) {
        // Add navigation controls to the body
        if (!document.querySelector('.mobile-nav-controls')) {
            const navControls = document.createElement('div');
            navControls.className = 'mobile-nav-controls';
            navControls.innerHTML = `
                <button class="close-fullscreen">
                    <i class="fas fa-times"></i>
                </button>
                <div class="arrow-indicator arrow-left">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="arrow-indicator arrow-right">
                    <i class="fas fa-chevron-right"></i>
                </div>
            `;
            document.body.appendChild(navControls);
            
            // Add event listeners to navigation controls
            document.querySelector('.close-fullscreen').addEventListener('click', function() {
                document.body.classList.remove('single-card-view');
                document.querySelectorAll('.card.active-card').forEach(card => {
                    card.classList.remove('active-card');
                });
            });
            
            document.querySelector('.arrow-left').addEventListener('click', function() {
                navigateCards('prev');
            });
            
            document.querySelector('.arrow-right').addEventListener('click', function() {
                navigateCards('next');
            });
        }
        
        // Make cards take full screen when clicked on mobile
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking an action button
                if (e.target.closest('.action-btn') || e.target.closest('video')) {
                    return;
                }
                
                // Toggle full screen view
                document.body.classList.toggle('single-card-view');
                this.classList.toggle('active-card');
                
                // Scroll to the card
                if (this.classList.contains('active-card')) {
                    this.scrollIntoView({behavior: 'smooth'});
                    
                    // Show/hide navigation controls
                    const navControls = document.querySelector('.mobile-nav-controls');
                    if (navControls) {
                        navControls.style.display = 'block';
                    }
                    
                    // Ensure action buttons are styled properly for fullscreen
                    setupMobileActionButtons();
                } else {
                    // Hide navigation controls
                    const navControls = document.querySelector('.mobile-nav-controls');
                    if (navControls) {
                        navControls.style.display = 'none';
                    }
                }
            });
        });
        
        // Apply mobile action button styles initially
        setupMobileActionButtons();
        
        // Function to navigate between cards
        function navigateCards(direction) {
            const activeCard = document.querySelector('.card.active-card');
            if (!activeCard) return;
            
            let targetCard;
            if (direction === 'prev') {
                targetCard = activeCard.previousElementSibling;
                if (targetCard && targetCard.classList.contains('card')) {
                    // Add spring effect animation for previous card
                    activeCard.style.animation = 'slide-out-right 0.3s forwards';
                    activeCard.addEventListener('animationend', function() {
                        activeCard.classList.remove('active-card');
                        activeCard.style.animation = '';
                    }, {once: true});
                    
                    targetCard.classList.add('active-card');
                    targetCard.style.animation = 'slide-in-left 0.35s forwards';
                    targetCard.scrollIntoView({behavior: 'smooth'});
                    
                    // Ensure action buttons are styled properly on navigation
                    setupMobileActionButtons();
                }
            } else {
                targetCard = activeCard.nextElementSibling;
                if (targetCard && targetCard.classList.contains('card')) {
                    // Add spring effect animation for next card
                    activeCard.style.animation = 'slide-out-left 0.3s forwards';
                    activeCard.addEventListener('animationend', function() {
                        activeCard.classList.remove('active-card');
                        activeCard.style.animation = '';
                    }, {once: true});
                    
                    targetCard.classList.add('active-card');
                    targetCard.style.animation = 'slide-in-right 0.35s forwards';
                    targetCard.scrollIntoView({behavior: 'smooth'});
                    
                    // Ensure action buttons are styled properly on navigation
                    setupMobileActionButtons();
                }
            }
        }
        
        // Add swipe functionality between cards
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            if (document.body.classList.contains('single-card-view')) {
                if (touchEndX < touchStartX - 50) {
                    // Swipe left - next card
                    navigateCards('next');
                }
                
                if (touchEndX > touchStartX + 50) {
                    // Swipe right - previous card
                    navigateCards('prev');
                }
            }
        }
        
        // Double tap to like (Instagram-like functionality)
        document.querySelectorAll('.card').forEach(card => {
            let lastTap = 0;
            card.addEventListener('touchend', function(e) {
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTap;
                
                if (tapLength < 300 && tapLength > 0) {
                    // Double tap detected
                    if (document.body.classList.contains('single-card-view')) {
                        showLikeAnimation(e);
                    }
                }
                lastTap = currentTime;
            });
        });
        
        // Function to show heart animation on double tap
        function showLikeAnimation(e) {
            const heart = document.createElement('i');
            heart.className = 'fas fa-heart instagram-like-heart';
            heart.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: white;
                font-size: 80px;
                z-index: 1001;
                opacity: 0;
                animation: heartbeat 1s ease-in-out;
            `;
            
            // Add animation keyframes
            if (!document.getElementById('heart-animation')) {
                const style = document.createElement('style');
                style.id = 'heart-animation';
                style.innerHTML = `
                    @keyframes heartbeat {
                        0% { transform: translate(-50%, -50%) scale(0); opacity: 0; }
                        50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
                        100% { transform: translate(-50%, -50%) scale(1); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }
            
            document.body.appendChild(heart);
            
            // Remove heart element after animation
            setTimeout(() => {
                heart.remove();
            }, 1000);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupMobileCardView();
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (!isMobile()) {
            document.body.classList.remove('single-card-view');
            document.querySelectorAll('.card.active-card').forEach(card => {
                card.classList.remove('active-card');
            });
            
            // Hide navigation controls
            const navControls = document.querySelector('.mobile-nav-controls');
            if (navControls) {
                navControls.style.display = 'none';
            }
        } else {
            setupMobileCardView();
        }
    });
});
</script>

<script>
// Function to convert regular action buttons to mobile action buttons
function setupMobileActionButtons() {
    if (window.innerWidth <= 768) { // Mobile view
        // Get all action button containers
        const actionContainers = document.querySelectorAll('.action-buttons');
        
        actionContainers.forEach(container => {
            // Add mobile class
            container.classList.add('mobile-action-buttons');
            
            // Override inline styles that might conflict
            container.style.position = 'absolute';
            container.style.bottom = '20px';
            container.style.right = '15px';
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.margin = '0';
            container.style.justifyContent = 'flex-end';
            
            // Style each button
            const buttons = container.querySelectorAll('.action-btn');
            buttons.forEach(button => {
                button.style.margin = '5px 0';
                button.style.width = '40px';
                button.style.height = '40px';
                button.style.borderRadius = '50%';
                button.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                button.style.display = 'flex';
                button.style.justifyContent = 'center';
                button.style.alignItems = 'center';
            });
        });
    } else {
        // Remove mobile classes for desktop view
        const mobileContainers = document.querySelectorAll('.mobile-action-buttons');
        mobileContainers.forEach(container => {
            container.classList.remove('mobile-action-buttons');
            // Reset inline styles
            container.style.position = 'relative';
            container.style.bottom = 'auto';
            container.style.right = 'auto';
            container.style.display = 'flex';
            container.style.flexDirection = 'row';
            container.style.margin = '8px 0';
            container.style.justifyContent = 'center';
            
            // Reset button styles
            const buttons = container.querySelectorAll('.action-btn');
            buttons.forEach(button => {
                button.style.margin = '0 8px';
                button.style.width = 'auto';
                button.style.height = 'auto';
                button.style.borderRadius = '0';
                button.style.backgroundColor = 'transparent';
            });
        });
    }
}

// Run on page load and resize
document.addEventListener('DOMContentLoaded', function() {
    setupMobileActionButtons();
    
    // Reapply on window resize
    window.addEventListener('resize', setupMobileActionButtons);
});
</script>
<style>
/* Single card view action buttons positioning */
body.single-card-view .card.active-card .action-buttons {
    position: fixed !important;
    bottom: 80px !important; 
    right: 20px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    z-index: 1050 !important;
    margin: 0 !important;
}

body.single-card-view .card.active-card .action-btn {
    margin: 8px 0 !important;
    width: 45px !important;
    height: 45px !important;
    background-color: rgba(0, 0, 0, 0.2) !important; /* Reduced contrast background */
    border-radius: 50% !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15) !important; /* Lighter shadow */
    backdrop-filter: blur(2px) !important; /* Light blur effect */
}

body.single-card-view .card.active-card .action-btn i {
    font-size: 1.3rem !important;
    color: white !important;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7) !important;
}

body.single-card-view .card.active-card .update-btn i {
    color: yellow !important;
}

body.single-card-view .card.active-card .history-btn i {
    color: #4cd964 !important;
}

body.single-card-view .card.active-card .delete-btn i {
    color: #ff3b30 !important;
}

/* Specific styling for fullscreen view */
body.single-card-view .card.active-card [style*="flex-wrap: wrap"] {
    display: flex !important;
    flex-direction: column !important;
    gap: 1.2rem !important;
    bottom: 100px !important;
    padding-bottom: 2rem !important;
    left: 15px !important;
    width: 80% !important;
}

body.single-card-view .card.active-card [style*="flex-wrap: wrap"] > div {
    width: 100% !important;
    margin-bottom: 0 !important;
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: flex-start !important;
}

body.single-card-view .card.active-card [style*="flex-wrap: wrap"] span {
    background-color: rgba(0, 0, 0, 0.4) !important;
    padding: 6px 12px !important;
    border-radius: 20px !important;
    margin-right: 10px !important;
    margin-bottom: 8px !important;
    font-size: 1rem !important;
    backdrop-filter: blur(5px) !important;
    display: inline-flex !important;
    align-items: center !important;
}

body.single-card-view .card.active-card [style*="flex-wrap: wrap"] span i {
    margin-right: 6px !important;
    font-size: 1rem !important;
}

/* Add this style to center the info fields for mobile and desktop */
.info-column {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    text-align: center !important;
}

.info-field {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
}

/* Ensure mobile view also keeps the centered alignment */
@media (max-width: 768px) {
    /* Keep the info columns centered */
    .info-column {
        align-items: center !important;
        text-align: center !important;
    }
    
    /* Ensure the table layout preserves the centered alignment */
    .info-table {
        justify-items: center !important;
    }
    
    /* Make sure the spans themselves remain centered */
    .info-field {
        justify-content: center !important;
        background-color: rgba(230, 230, 230, 0.3) !important; /* Slightly increased opacity */
        backdrop-filter: blur(1.5px) !important;
        padding: 4px 10px !important;
        border-radius: 12px !important;
        margin: 3px 5px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) !important; /* Slightly stronger shadow */
    }
}

/* Professional Filter Styling */
.filters-container {
    background: linear-gradient(to right, #f8f9fa, #ffffff, #f8f9fa);
    padding: 1.25rem;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
    max-width: 95%;
    transition: all 0.3s ease;
}

.filters-form {
    display: flex !important;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
    width: 100%;
}

.filters-form select {
    appearance: none;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23495057' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.8rem center;
    background-size: 1em;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    color: #495057;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    line-height: 1.5;
    padding: 0.7rem 2.25rem 0.7rem 1rem;
    transition: all 0.2s ease-in-out;
}

.filters-form select:hover {
    border-color: #adb5bd;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.09);
    transform: translateY(-1px);
}

.filters-form select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
    outline: 0;
}

.filters-form select option {
    font-weight: normal;
    padding: 0.5rem;
}

/* Responsive adjustments for filters */
@media (max-width: 768px) {
    .filters-container {
        padding: 1rem;
        margin-left: auto;
        margin-right: auto;
    }
    
    .filters-form {
        gap: 10px;
    }
    
    .filters-form select {
        width: 100% !important;
        max-width: 300px;
        margin-bottom: 6px;
    }
}

/* Single card view hide filters */
body.single-card-view .filters-container {
    display: none !important;
}
</style>
<style>
/* Instagram-like snap in effect for single card view */
@keyframes snap-in {
    0% {
        transform: scale(0.85);
        opacity: 0;
    }
    40% {
        transform: scale(1.04);
        opacity: 1;
    }
    70% {
        transform: scale(0.98);
    }
    100% {
        transform: scale(1);
    }
}

body.single-card-view .card.active-card {
    display: flex;
    opacity: 1;
    transform: scale(1);
    z-index: 100;
    animation: snap-in 0.35s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}

/* Enhance transition for all cards */
.card {
    transition: all 0.25s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}

body.single-card-view .card {
    opacity: 0;
    transform: scale(0.85);
    transition: opacity 0.2s, transform 0.2s;
}

/* Improve initial loading appearance */
.cards-container {
    opacity: 0;
    animation: fade-in 0.5s ease forwards;
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Instagram-like slide animations for swiping between cards */
@keyframes slide-in-right {
    0% {
        transform: translateX(100%) scale(0.9);
        opacity: 0;
    }
    60% {
        transform: translateX(-2%) scale(1.01);
        opacity: 1;
    }
    100% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
}

@keyframes slide-in-left {
    0% {
        transform: translateX(-100%) scale(0.9);
        opacity: 0;
    }
    60% {
        transform: translateX(2%) scale(1.01);
        opacity: 1;
    }
    100% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
}

@keyframes slide-out-right {
    0% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
    100% {
        transform: translateX(100%) scale(0.9);
        opacity: 0;
    }
}

@keyframes slide-out-left {
    0% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
    100% {
        transform: translateX(-100%) scale(0.9);
        opacity: 0;
    }
}
</style>

<script>
    // Add pulsing effect to the Add Animal button on page load
    document.addEventListener('DOMContentLoaded', function() {
        const addButton = document.querySelector('.btn-add-animal');
        if (addButton) {
            // Add initial pulse effect
            setTimeout(() => {
                addButton.classList.add('btn-pulse');
            }, 1000);
            
            // Add focus and click effects
            addButton.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.03)';
            });
            
            addButton.addEventListener('mouseleave', function() {
                if (!this.classList.contains('clicked')) {
                    this.style.transform = '';
                }
            });
            
            addButton.addEventListener('click', function() {
                this.classList.add('clicked');
                
                // Create ripple effect
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                
                // Position the ripple where clicked
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height) * 2;
                ripple.style.width = ripple.style.height = `${size}px`;
                ripple.style.left = `${0}px`;
                ripple.style.top = `${0}px`;
                
                // Remove ripple after animation completes
                setTimeout(() => {
                    ripple.remove();
                    this.classList.remove('clicked');
                }, 600);
            });
        }
    });
</script>

<style>
    /* Button pulse and ripple effects */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    
    .btn-pulse {
        animation: pulse 2s infinite;
    }
    
    .btn-add-animal {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        z-index: 0;
    }
    
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
</style>

<div class="container filters-container" style="text-align: center;">
</div>

<!-- New Entry Modal -->
<div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Animal
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
                                        <label for="newNombre">Nombre</label>
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
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
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

<!-- Function to preview image -->

<script>
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

                // Show loading state
                const submitButton = document.querySelector('#newEntryModal .btn-success');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                submitButton.disabled = true;

                // Send the form data using fetch
                fetch('camarones_create.php', {
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



<script>
const apiKey = 'sec_AdQUXMlHjjhyrwud6dGCP9DFtUt8ZS7T'; // Reemplaza con tu API key real
const chatDiv = document.getElementById('chat');
const inputField = document.getElementById('userInput');
const sendBtn = document.getElementById('sendBtn');
const fileInput = document.getElementById('pdfFile');
const fileName = document.getElementById('fileName');
const uploadBtn = document.getElementById('uploadBtn');
const uploadStatus = document.getElementById('uploadStatus');
const chatBubble = document.getElementById('chatBubble');
const chatFullscreen = document.getElementById('chatFullscreen');
const closeChatBtn = document.getElementById('closeChatBtn');
    
let sourceId = null; // To store the ChatPDF source ID
let chatState = 'waiting_for_animal'; // 'waiting_for_animal', 'pdf_ready', 'chatting'
let currentAnimalData = null;

// Chat Bubble Toggle Functionality
chatBubble.addEventListener('click', () => {
    // Expand bubble briefly for better UX
    chatBubble.classList.add('expanded');
    
    setTimeout(() => {
        chatBubble.classList.remove('expanded');
        openFullscreenChat();
    }, 300);
});

closeChatBtn.addEventListener('click', closeFullscreenChat);

function openFullscreenChat() {
    chatFullscreen.classList.remove('hidden');
    setTimeout(() => {
        chatFullscreen.classList.add('active');
        inputField.focus();
    }, 50);
}

function closeFullscreenChat() {
    chatFullscreen.classList.remove('active');
    setTimeout(() => {
        chatFullscreen.classList.add('hidden');
        
        // Reset chat title to default
        const chatTitle = document.querySelector('.chat-title');
        chatTitle.innerHTML = `
            <i class="fas fa-user-doctor me-2"></i>
            Veterinario ChatGPT
        `;
        
        // Reset chat state and clear current animal data
        chatState = 'waiting_for_animal';
        currentAnimalData = null;
        sourceId = null;
        
        // Clear chat messages (keep only welcome message)
        const chatDiv = document.getElementById('chat');
        chatDiv.innerHTML = `
            <div class="welcome-message">
                <div class="chat-message bot-message">
                    <div class="message-bubble">
                        <i class="fas fa-user-doctor" style="color: #4b8046;"></i>
                        <div class="message-content">
                            ¡Hola! Soy el Veterinario ChatGPT. <br><br>
                            Por favor, ingresa el <strong>Tag ID</strong> o <strong>nombre</strong> del animal para generar y analizar su historial médico.
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Reset input placeholder
        inputField.placeholder = 'Ingresa Tag ID o nombre del animal...';
    }, 400);
}

// Animal Search and PDF Generation
async function searchAnimalAndGeneratePDF(query) {
    try {
        // Add thinking message
        addChatMessage('system', '<i class="fas fa-search"></i> Buscando animal...', 'system');
        
        // Search for animal by tagid or name
        const response = await fetch(`search_animal.php?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Animal no encontrado');
        }
        
        currentAnimalData = data.animal;
        
        // Show found animal
        addChatMessage('system', 
            `<i class="fas fa-check-circle"></i> Animal encontrado: <strong>${currentAnimalData.nombre}</strong> (Tag ID: ${currentAnimalData.tagid})<br><br>
            <i class="fas fa-file-pdf"></i> Generando reporte médico completo...`, 'system');
        
        // Generate PDF
        await generateAnimalPDF(currentAnimalData.tagid);
        
    } catch (error) {
        console.error('Search error:', error);
        addChatMessage('system', 
            `<i class="fas fa-exclamation-triangle"></i> ${error.message}<br><br>
            Por favor, verifica que el Tag ID o nombre sea correcto e intenta nuevamente.`, 'error');
    }
}

async function generateAnimalPDF(tagid) {
    try {
        // Generate the PDF for chat purposes
        const response = await fetch(`camarones_report.php?tagid=${encodeURIComponent(tagid)}&for_chat=1`);
        if (!response.ok) {
            throw new Error('Error generando el PDF del historial médico');
        }

        // Get the PDF filename from redirect
        const redirectUrl = response.url;
        const urlParams = new URLSearchParams(new URL(redirectUrl).search);
        const filename = urlParams.get('file');

        if (!filename) {
            throw new Error('No se pudo obtener el archivo PDF generado');
        }

        // Fetch the generated PDF
        const pdfResponse = await fetch(`reports/${filename}`);
        if (!pdfResponse.ok) {
            throw new Error('Error accediendo al PDF generado');
        }

        // Convert to File object
        const pdfBlob = await pdfResponse.blob();
        const file = new File([pdfBlob], filename, { type: 'application/pdf' });

        // Upload to ChatPDF
        addChatMessage('system', '<i class="fas fa-cloud-upload-alt"></i> Subiendo historial a la IA...', 'system');
        
        const formData = new FormData();
        formData.append('file', file);

        const uploadResponse = await fetch('./chatpdf_proxy.php?action=upload', {
            method: 'POST',
            body: formData
        });

        const uploadData = await uploadResponse.json();

        if (!uploadResponse.ok) {
            throw new Error(uploadData.error || 'Error al subir el historial');
        }

        // Success - enable chat
        sourceId = uploadData.sourceId;
        chatState = 'chatting';
        
        // Update chat title to include animal name and Tag ID
        const chatTitle = document.querySelector('.chat-title');
        chatTitle.innerHTML = `
            <i class="fas fa-user-doctor me-2"></i>
            Veterinario ChatGPT - ${currentAnimalData.nombre} (${currentAnimalData.tagid})
        `;
        
        // Prompts Predefinidos
        const menuHTML = `
            ¡Perfecto! He analizado el historial completo del estanque <strong>${currentAnimalData.nombre}</strong>.<br><br>
            <div class="prompt-menu">
                <p><i class="fas fa-stethoscope"></i> Consultas Operacionales:</p>
                <div class="prompt-buttons">
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Biomasa del Estanque')">
                        <i class="fas fa-weight"></i> Biomasa del Estanque
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Pesos')">
                        <i class="fas fa-egg"></i> Registros de Pesos
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Concentrado')">
                        <i class="fas fa-seedling"></i> Registros de Concentrado
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Harinas')">
                        <i class="fa-solid fa-droplet"></i> Registros de Harinas
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Fermentados')">
                        <i class="fas fa-cube"></i> Registros de Fermentados
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Temperatura del Agua')">
                        <i class="fas fa-syringe"></i> Registros Temperatura del Agua
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de pH del Agua')">
                        <i class="fas fa-heart"></i> Registros de pH del Agua
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Oxigeno del Agua')">
                        <i class="fas fa-chart-pie"></i> Registros de Oxigeno del Agua
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Nitritos')">
                        <i class="fas fa-layer-group"></i> Registros de Nitritos
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Alcalinidad')">
                        <i class="fas fa-seedling"></i> Registros de Alcalinidad
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Transparencia')">
                        <i class="fas fa-calculator"></i> Registros de Transparencia
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Salinidad')">
                        <i class="fas fa-syringe"></i> Registros de Salinidad
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Redox')">
                        <i class="fas fa-female"></i> Registros de Redox
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Registros de Alcalinidad')">
                        <i class="fas fa-cubes"></i> Registros de Alcalinidad
                    </button>
                    <button class="prompt-btn" onclick="selectPromptFromMenu('Indice de Conversion Alimentaria (ICA)')">
                        <i class="fas fa-cubes"></i> Indice de Conversion Alimentaria (ICA)
                    </button>
                </div>
                <p style="margin-top: 15px; font-size: 0.9em; color: #666;">
                    O escribe tu propia consulta: Ejemplo. Muestra todos los registros de compras, registros de Ventas, Tasa Supervivencia, Incremento Mensual de Pesos.
                </p>
            </div>
        `;
        
        addChatMessage('bot', menuHTML, 'bot');
        
        // Update placeholder
        inputField.placeholder = 'Escribe tu consulta aquí...';
        
    } catch (error) {
        console.error('PDF generation error:', error);
        addChatMessage('system', 
            `<i class="fas fa-exclamation-circle"></i> ${error.message}<br><br>
            Por favor intenta nuevamente.`, 'error');
        chatState = 'waiting_for_animal';
    }
}

function addChatMessage(role, content, type = 'normal') {
    const messageClass = role === 'user' ? 'user-message' : 'bot-message';
    const icon = role === 'user' ? 'fas fa-user' : 
                role === 'bot' ? 'fas fa-user-doctor' : 'fas fa-info-circle';
    
    let bubbleClass = '';
    if (type === 'error') bubbleClass = 'error-message';
    else if (type === 'system') bubbleClass = 'system-message';
    
    const messageHtml = `
        <div class="chat-message ${messageClass}">
            <div class="message-bubble ${bubbleClass}">
                <i class="${icon}" style="color: ${role === 'bot' ? '#4b8046' : 'inherit'};"></i>
                <div class="message-content">${content}</div>
            </div>
        </div>`;
    
    // Remove welcome message if it exists
    const welcomeMessage = chatDiv.querySelector('.welcome-message');
    if (welcomeMessage && role !== 'system') {
        welcomeMessage.remove();
    }
    
    chatDiv.innerHTML += messageHtml;
    chatDiv.scrollTop = chatDiv.scrollHeight;
}

// Function to handle prompt button clicks
function selectPrompt(promptText) {
    const inputField = document.getElementById('userInput');
    inputField.value = promptText;
    inputField.focus();
}

// Function to handle prompt button clicks from floating menu
function selectPromptFromMenu(promptText) {
    const inputField = document.getElementById('userInput');
    // Special cases - don't append animal data for distribution/general queries
    if (promptText === 'Razas' || promptText === 'Grupos' || promptText === 'Produccion Carnica' || promptText === 'Produccion lechera' || promptText === 'Consumo Concentrado' || promptText === 'Conversion' || promptText === 'Vacunas' || promptText === 'Preñez' || promptText === 'Dias Abiertos' || promptText === 'Descartes' || promptText === 'Sin parir' || promptText === 'sin parir' || promptText === 'Consumo Melaza' || promptText === 'Consumo Sal') {
        inputField.value = promptText;
    } else if (currentAnimalData) {
        inputField.value = `${promptText} del animal ${currentAnimalData.nombre} (Tag ID: ${currentAnimalData.tagid})`;
    } else {
        inputField.value = promptText;
    }
    inputField.focus();
    // Close the floating menu
    toggleFloatingMenu(false);
}

// Function to toggle floating prompt menu
function toggleFloatingMenu(show = null) {
    const floatingMenu = document.getElementById('floatingPromptMenu');
    const menuBtn = document.getElementById('menuBtn');
    
    if (show === null) {
        // Toggle current state
        show = floatingMenu.classList.contains('hidden');
    }
    
    if (show) {
        floatingMenu.classList.remove('hidden');
        menuBtn.classList.add('active');
    } else {
        floatingMenu.classList.add('hidden');
        menuBtn.classList.remove('active');
    }
}

fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        if (file.type !== 'application/pdf') {
            uploadStatus.textContent = 'Error: Solo se permiten archivos PDF';
            uploadStatus.className = 'upload-status error';
            uploadBtn.disabled = true;
            return;
        }
        fileName.textContent = file.name;
        uploadBtn.disabled = false;
        uploadStatus.textContent = '';
        uploadStatus.className = 'upload-status';
    } else {
        fileName.textContent = '';
        uploadBtn.disabled = true;
    }
});

uploadBtn.addEventListener('click', async () => {
    const file = fileInput.files[0];
    if (!file) return;

    uploadBtn.disabled = true;
    uploadStatus.textContent = 'Subiendo Historial...';
    uploadStatus.className = 'upload-status';

    try {
        const formData = new FormData();
        formData.append('file', file);

        console.log('Sending request to proxy...');
        // Use relative path since we're in the same directory
        const response = await fetch('./chatpdf_proxy.php?action=upload', {
            method: 'POST',
            body: formData
        });

        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);

        if (!response.ok) {
            let errorMsg = data.error || 'Error al subir el archivo';
            if (data.api_response) {
                console.log('API Response details:', data.api_response);
                if (data.api_response.error) {
                    errorMsg += `\nError API: ${data.api_response.error}`;
                }
                if (data.api_response.http_code) {
                    errorMsg += `\nCódigo HTTP: ${data.api_response.http_code}`;
                }
                if (data.api_response.headers) {
                    errorMsg += '\nDetalles de la respuesta en la consola';
                }
            }
            throw new Error(errorMsg);
        }

        sourceId = data.sourceId;
        uploadStatus.textContent = '¡PDF subido exitosamente!';
        uploadStatus.className = 'upload-status success';
        
        // Enable chat interface
        inputField.disabled = false;
        sendBtn.disabled = false;
        
        // Add initial message
        chatDiv.innerHTML = `<div><b>Sistema:</b> PDF cargado correctamente. ¡Puedes empezar a hacer preguntas sobre el documento!</div>`;

    } catch (error) {
        console.error('Upload error details:', error);
        uploadStatus.textContent = `Error: ${error.message}`;
        uploadStatus.className = 'upload-status error';
        uploadBtn.disabled = false;
    }
});

async function enviarMensaje() {
    const mensaje = inputField.value.trim();
    if (!mensaje) return;

    // Show user message
    addChatMessage('user', mensaje);
    inputField.value = '';
    sendBtn.disabled = true;
    
    try {
        if (chatState === 'waiting_for_animal') {
            // Search for animal and generate PDF
            await searchAnimalAndGeneratePDF(mensaje);
        } else if (chatState === 'chatting' && sourceId) {
            // Regular chat with veterinary AI
            await sendChatMessage(mensaje);
        } else {
            throw new Error('Sistema no listo. Por favor intenta nuevamente.');
        }
    } catch (error) {
        console.error('Message error:', error);
        addChatMessage('system', 
            `<i class="fas fa-exclamation-circle"></i> ${error.message}`, 'error');
    } finally {
        sendBtn.disabled = false;
    }
}

async function sendChatMessage(mensaje) {
    try {
        // Special handling for farm-wide statistics - don't include tag ID in response
        let promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei , analiza el historial médico y responde: `;
        
        if (mensaje === 'Produccion biomasa') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla cah_pesos Biomasa y responde sobre las tendencias estadísticas del peso de los camarones del estanque.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Temperatura del Agua') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_temperature_log y analiza la variacion minima y maxima de la temperatura del agua. Recomienda niveles optimos de temperatura para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de pH del Agua') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_ph_log y analiza la variacion minima y maxima del pH agua. Recomienda niveles optimos de pH para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Oxigeno del Agua') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_oxygen_log y analiza la variacion minima y maxima del nivel de oxigeno disuelto en el agua. Recomienda niveles optimos de oxigeno para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Nitritos') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_nitrito_log y analiza la variacion minima y maxima del nitrito del agua. Recomienda niveles optimos de nitrito para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Alcalinidad') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_alcalinidad_log y analiza la variacion minima y maxima de la alcalinidad del agua. Recomienda niveles optimos de alcalinidad para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Transparencia') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_transparencia_log y analiza la variacion minima y maxima de la transparencia del agua. Recomienda niveles optimos de transparencia para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Salinidad') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_salinidad_log y analiza la variacion minima y maxima de la salinidad del agua. Recomienda niveles optimos de salinidad para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Redox') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la table water_redox_log y analiza la variacion minima y maxima del redox del agua. Recomienda niveles optimos de redox para el crecimiento de los camarones.  Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Registros de Alcalinidad') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, muestra todos los registros de la tabla water_alcalinidad_log y analiza la variacion minima y maxima de la alcalinidad del agua. Recomienda niveles optimos de alcalinidad para el crecimiento de los camarones. Determina el tanque con mejor y peor desempeño. IMPORTANTE: No menciones números de tagid en tu respuesta, ya que es una estadística general de todo el cultivo, no de un estanque específico. Responde: `;
        } else if (mensaje === 'Indice de Conversion Alimentaria (ICA)') {
            promptPrefix = `Como veterinario especializado en piscicultura y siembra de Litopenaeus vannamei, analiza el historial de la tabla de conversion de todas los estanques y responde sobre el índice de conversión alimenticia. Determina el tanque con mejor y peor desempeñoIMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        } else if (mensaje === 'Vacunas') {
            promptPrefix = `Como veterinario especializado en ganado bovino, analiza el historial de vacunas de aftosa, brucelosis, carbunco, ibr, cbr, parasitos, garrapatas, mastitis de toda la finca y responde sobre el resumen de vacunaciones y tratamientos. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        } else if (mensaje === 'Preñez') {
            promptPrefix = `Como veterinario especializado en ganado bovino, analiza el historial de la tabla de preñez de toda la finca y responde sobre las estadísticas de preñez. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        } else if (mensaje === 'Dias Abiertos') {
            promptPrefix = `Como veterinario especializado en ganado bovino, analiza el historial de la tabla de Dias Abiertos de toda la finca y responde sobre las estadísticas de días abiertos. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        } else if (mensaje === 'Descartes') {
            promptPrefix = `Como veterinario especializado en ganado bovino, analiza el historial médico de la tabla Descartes en base a los vientres con mas de 365 dias sin gestar en toda la finca y responde sobre las estadísticas de hembras sin registro de gestación. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        } else if (mensaje === 'sin parir') {
            promptPrefix = `Como veterinario especializado en ganado bovino, analiza el historial médico de toda la finca y responde sobre las estadísticas de animales con más de 365 días sin parir. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        } else if (mensaje === 'Consumo de Melaza') {
            promptPrefix = `Como veterinario especializado en ganado bovino, analiza el historial de consumo de melaza de toda la finca y responde sobre las estadísticas de consumo de melaza. IMPORTANTE: No menciones números de tag ID en tu respuesta, ya que es una estadística general de toda la finca, no de un animal específico. Responde: `;
        }
        
        const response = await fetch('./chatpdf_proxy.php?action=chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                sourceId: sourceId,
                messages: [
                    {
                        role: 'user',
                        content: `${promptPrefix}${mensaje}`
                    }
                ]
            })
        });

        const data = await response.json();

        if (!response.ok) {
            let errorMsg = data.error || 'Error al procesar la consulta veterinaria';
            if (data.api_response) {
                console.log('API Response details:', data.api_response);
                if (data.api_response.error) {
                    errorMsg += `\nError API: ${data.api_response.error}`;
                }
                if (data.api_response.http_code) {
                    errorMsg += `\nCódigo HTTP: ${data.api_response.http_code}`;
                }
            }
            throw new Error(errorMsg);
        }

        // Format and show AI response
        const formattedContent = formatChatResponse(data.content);
        addChatMessage('bot', formattedContent);
        
    } catch (error) {
        console.error('Chat error details:', error);
        throw error;
    }
}

// Function to format chat response
function formatChatResponse(content) {
    if (!content) return '';
    
    // Replace asterisks with line breaks and proper spacing
    content = content.replace(/\*/g, '');
    
    // Convert bullet points (if text contains - or • at the beginning of lines)
    content = content.replace(/^[-•]\s+(.+)$/gm, '<li>$1</li>');
    if (content.includes('<li>')) {
        content = '<ul style="margin: 1em 0; padding-left: 1.5em;">' + content + '</ul>';
    }
    
    // Convert numbered lists (if text contains 1., 2., etc. at the beginning of lines)
    content = content.replace(/^\d+\.\s+(.+)$/gm, '<li>$1</li>');
    
    // Add paragraphs for better spacing
    content = content.split('\n\n').map(para => {
        if (para.trim().startsWith('<')) {
            return para;
        }
        return `<p style="margin-bottom: 1.2em; line-height: 1.5;">${para}</p>`;
    }).join('');
    
    // Preserve single line breaks with extra spacing
    content = content.replace(/\n/g, '<br><br>');
    
    return content;
}

// Add CSS for chat message formatting
const style = document.createElement('style');
style.textContent = `
    .message-content p {
        margin-bottom: 1.2em;
        line-height: 1.5;
    }
    .message-content br + br {
        margin-top: 0.8em;
    }
    .message-content ul {
        margin: 1em 0;
        padding-left: 1.5em;
    }
    .message-content li {
        margin-bottom: 0.5em;
        line-height: 1.5;
    }
`;
document.head.appendChild(style);

// Event listeners
sendBtn.addEventListener('click', enviarMensaje);
inputField.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !sendBtn.disabled) {
        e.preventDefault();
        enviarMensaje();
    }
});

// Menu button event listener
document.getElementById('menuBtn').addEventListener('click', (e) => {
    e.stopPropagation();
    // Only show menu if we're in chatting state (PDF has been generated)
    if (chatState === 'chatting') {
        toggleFloatingMenu();
    } else {
        // Show a message that menu is only available after selecting an animal
        addChatMessage('system', 
            '<i class="fas fa-info-circle"></i> El menú de consultas rápidas estará disponible después de seleccionar un animal.', 'system');
    }
});

// Close menu button event listener
document.getElementById('closeMenuBtn').addEventListener('click', (e) => {
    e.stopPropagation();
    toggleFloatingMenu(false);
});

// Close menu when clicking outside
document.addEventListener('click', (e) => {
    const floatingMenu = document.getElementById('floatingPromptMenu');
    const menuBtn = document.getElementById('menuBtn');
    
    if (!floatingMenu.contains(e.target) && !menuBtn.contains(e.target)) {
        toggleFloatingMenu(false);
    }
});

// Close chat with Escape key or close floating menu
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const floatingMenu = document.getElementById('floatingPromptMenu');
        if (!floatingMenu.classList.contains('hidden')) {
            toggleFloatingMenu(false);
        } else if (chatFullscreen.classList.contains('active')) {
            closeFullscreenChat();
        }
    }
});
</script>

<style>
    /* Existing styles ... */
    
    .upload-section {
        border-bottom: 1px solid #404040;
        background: #2d2d2d;
    }

    .upload-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .file-input {
        display: none;
    }

    .file-label {
        background: #10a37f;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 8px 16px;
    }

    .file-label:hover {
        background: #0d8a6b;
        transform: translateY(-1px);
    }

    .file-name {
        font-size: 0.9rem;
        color: #a0a0a0;
        text-align: center;
    }

    .upload-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #10a37f;
        color: white;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
        width: 100%;
        margin-top: 10px;
        padding: 10px 16px;
    }

    .upload-btn:hover:not(:disabled) {
        background: #0d8a6b;
        transform: translateY(-1px);
    }

    .upload-btn:disabled {
        background: #404040;
        cursor: not-allowed;
        color: #666666;
    }

    .upload-btn i {
        color: white;
        margin-right: 8px;
    }

    .upload-status {
        margin-top: 1rem;
        text-align: center;
        font-size: 0.9rem;
    }

    .upload-status.error {
        color: #ff6b6b;
    }

    .upload-status.success {
        color: #10a37f;
    }

    
</style>
<script>
async function generateAndUploadPDF(tagid) {
    // Open the chat and automatically start the process for this animal
    chatBubble.classList.add('expanded');
    
    setTimeout(async () => {
        chatBubble.classList.remove('expanded');
        openFullscreenChat();
        
        // Wait for chat to open, then automatically process this animal
        setTimeout(async () => {
            // Set the animal tagid in the input field and trigger search
            inputField.value = tagid;
            await enviarMensaje();
        }, 500);
    }, 300);
}
</script>
</body>
</html>