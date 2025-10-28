<?php

// Suppress warnings when generating JSON for ChatPDF upload
if (isset($_GET['upload_to_chatpdf']) && $_GET['upload_to_chatpdf'] == '1') {
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', 0);
}

require_once './pdo_conexion.php';
require('./fpdf/fpdf.php');

// Set memory and execution limits for large datasets
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 600); // 10 minutes

// Check if reports directory exists, if not create it
$reportsDir = './reports';
if (!file_exists($reportsDir)) {
    if (!mkdir($reportsDir, 0777, true)) {
        error_log('Failed to create reports directory: ' . $reportsDir);
        die('Error: Cannot create reports directory. Please check file permissions.');
    }
    chmod($reportsDir, 0777);
}

// Clean up old PDF files (older than 1 hour) to prevent disk space issues
$files = glob($reportsDir . '/Reporte_Camarones_*.pdf');
if ($files) {
    $currentTime = time();
    foreach ($files as $file) {
        if (is_file($file) && ($currentTime - filemtime($file)) > 3600) { // 1 hour
            unlink($file);
        }
    }
}

// Ensure no output has been sent before
if (ob_get_length()) ob_clean();

// Check if shrimp pond ID is provided
if (!isset($_GET['tagid']) || empty($_GET['tagid'])) {
    die('Error: No shrimp pond ID provided');
}

$tagid = $_GET['tagid'];

// Connect to database with enhanced error handling
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Error: Database connection failed. Please check server configuration.');
}

// Set charset to UTF-8 for proper character encoding in PDF
if (!mysqli_set_charset($conn, "utf8")) {
    error_log('Error setting charset: ' . mysqli_error($conn));
    die('Error: Failed to set database charset.');
}

// Fetch shrimp basic info
$sql_shrimp = "SELECT * FROM camarones WHERE tagid = ?";
$stmt_shrimp = $conn->prepare($sql_shrimp);

if (!$stmt_shrimp) {
    error_log('MySQL prepare error: ' . mysqli_error($conn));
    die('Error: Database query preparation failed.');
}

$stmt_shrimp->bind_param('s', $tagid);
if (!$stmt_shrimp->execute()) {
    error_log('MySQL execute error: ' . $stmt_shrimp->error);
    die('Error: Failed to execute shrimp query.');
}

$result_shrimp = $stmt_shrimp->get_result();
if (!$result_shrimp || $result_shrimp->num_rows === 0) {
    error_log('Shrimp pond not found with tagid: ' . $tagid);
    die('Error: Shrimp pond not found in database.');
}

$shrimp = $result_shrimp->fetch_assoc();

// Create PDF
class PDF extends FPDF
{
    protected $shrimpData;
    
    function setShrimpData($data) {
        $this->shrimpData = $data;
    }
    
    function EncodeText($text) {
        if ($text === null || $text === '') {
            return '';
        }
        return iconv('UTF-8', 'ISO-8859-1//IGNORE', $text);
    }
    
    // Page header
    function Header() {
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(0, 100, 150);
        $this->Cell(0, 15, $this->EncodeText('Reporte Integral de Camarones'), 0, 1, 'C');
        
        if ($this->shrimpData) {
            $this->SetFont('Arial', 'B', 14);
            $this->SetTextColor(50, 50, 50);
            $this->Cell(0, 10, $this->EncodeText('Estanque: ' . $this->shrimpData['nombre'] . ' (ID: ' . $this->shrimpData['tagid'] . ')'), 0, 1, 'C');
            $this->Cell(0, 8, $this->EncodeText('Etapa: ' . $this->shrimpData['etapa']), 0, 1, 'C');
        }
        
        $this->SetTextColor(100, 100, 100);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->EncodeText('Generado: ' . date('d/m/Y H:i:s')), 0, 1, 'C');
            $this->Ln(5);
            
        // Draw line
        $this->SetDrawColor(0, 100, 150);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);
    }

    // Page footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, $this->EncodeText('Página ' . $this->PageNo() . ' | Sistema de Gestión Animalia Camarones'), 0, 0, 'C');
    }
    
    // Section header
    function SectionHeader($title) {
        $this->SetFont('Arial', 'B', 14);
        $this->SetFillColor(230, 240, 255);
        $this->SetTextColor(0, 80, 120);
        $this->Cell(0, 10, $this->EncodeText($title), 0, 1, 'L', true);
            $this->Ln(3);
        }
        
    // Table header
    function TableHeader($headers) {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200, 220, 255);
        $this->SetTextColor(0);
        
        $widths = $this->calculateColumnWidths($headers, count($headers));
        
        foreach ($headers as $i => $header) {
            $this->Cell($widths[$i], 8, $this->EncodeText($header), 1, 0, 'C', true);
        }
        $this->Ln();
    }
    
    // Table row
    function TableRow($data, $numCols) {
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(245, 245, 245);
        
        $widths = $this->calculateColumnWidths(array_keys($data), $numCols);
        
        $i = 0;
        foreach ($data as $value) {
            if ($i < $numCols) {
                $this->Cell($widths[$i], 6, $this->EncodeText($value), 1, 0, 'C');
                $i++;
            }
        }
        $this->Ln();
    }
    
    // Calculate column widths
    function calculateColumnWidths($headers, $numCols) {
        $totalWidth = 190;
        $widths = [];
        
        switch ($numCols) {
            case 2:
                return [60, 130];
            case 3:
                return [50, 70, 70];
            case 4:
                return [40, 50, 50, 50];
            case 5:
                return [35, 40, 40, 35, 40];
            case 6:
                return [30, 32, 32, 32, 32, 32];
            case 7:
                return [25, 28, 28, 28, 28, 28, 25];
            case 8:
                return [22, 24, 24, 24, 24, 24, 24, 24];
            default:
                $width = $totalWidth / $numCols;
                return array_fill(0, $numCols, $width);
        }
    }
    
    // Info box
    function InfoBox($label, $value, $x = null, $y = null, $width = 90) {
        if ($x !== null && $y !== null) {
            $this->SetXY($x, $y);
        }
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(240, 240, 240);
        $this->Cell($width, 8, $this->EncodeText($label . ':'), 1, 0, 'L', true);
        
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(255, 255, 255);
        $this->Cell($width, 8, $this->EncodeText($value), 1, 1, 'L', true);
    }
}

// Initialize PDF
$pdf = new PDF();
$pdf->setShrimpData($shrimp);
$pdf->AddPage();

// Basic Information Section
$pdf->SectionHeader('Información Básica del Estanque');

$pdf->InfoBox('Nombre del Estanque', $shrimp['nombre'], 15, null, 40);
$pdf->InfoBox('ID del Estanque', $shrimp['tagid'], 105, $pdf->GetY() - 8, 40);

$pdf->InfoBox('Fecha de Siembra', $shrimp['fecha_nacimiento'] ?: 'No registrada', 15, null, 40);
$pdf->InfoBox('Población Inicial', number_format($shrimp['poblacion']), 105, $pdf->GetY() - 8, 40);

$pdf->InfoBox('Peso de Siembra (g)', number_format($shrimp['peso_nacimiento'], 2), 15, null, 40);
$pdf->InfoBox('Etapa Actual', $shrimp['etapa'], 105, $pdf->GetY() - 8, 40);

// Calculate age in days from fecha_nacimiento to today
if ($shrimp['fecha_nacimiento']) {
    $birth_date = new DateTime($shrimp['fecha_nacimiento']);
    $current_date = new DateTime();
    $age_days = $current_date->diff($birth_date)->days;
    $pdf->InfoBox('Edad Camarones', $age_days . ' días', 105, $pdf->GetY() - 8, 40);
}

$pdf->Ln(10);

// Death Records and Mortality Analysis
// Registros de decesos y análisis de mortalidad

// First, calculate total deaths from all records
$sql_total_deaths = "SELECT SUM(cah_decesos_cantidad) as total_deaths FROM cah_decesos WHERE cah_decesos_tagid = ?";
$stmt_total_deaths = $conn->prepare($sql_total_deaths);
$total_deaths_all = 0;

if ($stmt_total_deaths) {
    $stmt_total_deaths->bind_param('s', $tagid);
    $stmt_total_deaths->execute();
    $result_total = $stmt_total_deaths->get_result();
    $total_deaths_all = $result_total->fetch_assoc()['total_deaths'] ?: 0;
}

// Get recent death records for display
$sql_deaths = "SELECT cah_decesos_fecha, cah_decesos_causa, cah_decesos_cantidad 
               FROM cah_decesos WHERE cah_decesos_tagid = ? ORDER BY cah_decesos_fecha DESC LIMIT 10";
$stmt_deaths = $conn->prepare($sql_deaths);

if ($stmt_deaths) {
    $stmt_deaths->bind_param('s', $tagid);
    $stmt_deaths->execute();
    $result_deaths = $stmt_deaths->get_result();

    if ($result_deaths->num_rows > 0) {
        $pdf->SectionHeader('Registros de Decesos');
        $pdf->TableHeader(['Fecha', 'Causa', 'Cantidad']);
        
        while ($row = $result_deaths->fetch_assoc()) {
            $pdf->TableRow([
                $row['cah_decesos_fecha'],
                $row['cah_decesos_causa'],
                number_format($row['cah_decesos_cantidad'])
            ], 3);
        }
        $pdf->Ln(5);
    }
}

// Always show mortality summary (regardless of whether there are death records or not)
$poblacion_inicial = $shrimp['poblacion'];
$poblacion_real = $poblacion_inicial - $total_deaths_all;
$supervivencia_porcentaje = $poblacion_inicial > 0 ? (($poblacion_inicial - $total_deaths_all) / $poblacion_inicial) * 100 : 0;

// Display summary information
$pdf->SectionHeader('Resumen de Mortalidad y Supervivencia');
$pdf->InfoBox('Total Muertos', number_format($total_deaths_all), 15, null, 40);
$pdf->InfoBox('Población Real', number_format($poblacion_real), 105, $pdf->GetY() - 8, 40);
$pdf->InfoBox('% Supervivencia', number_format($supervivencia_porcentaje, 2) . '%', 15, null, 40);

$pdf->Ln(8);

// Purchase Information
if ($shrimp['fecha_compra'] && $shrimp['fecha_compra'] != '0000-00-00') {
    $pdf->SectionHeader('Información de Compra');
    
    $pdf->InfoBox('Fecha de Compra', $shrimp['fecha_compra'], 15, null, 40);
    $pdf->InfoBox('Peso en Compra (g)', number_format($shrimp['peso_compra'], 2), 105, $pdf->GetY() - 8, 40);
    
    $pdf->InfoBox('Monto de Compra', '$' . number_format($shrimp['monto_compra'], 2), 15, null, 40);
    $pdf->InfoBox('Cantidad Comprada', number_format($shrimp['cantidad_compra']), 105, $pdf->GetY() - 8, 40);
    
    $pdf->Ln(10);
}


// Sales Information from cah_ventas table
$sql_sales_detailed = "SELECT cah_ventas_fecha, cah_ventas_presentacion, cah_ventas_cantidad, 
                      cah_ventas_precio, cah_ventas_peso, cah_ventas_talla,
                      (cah_ventas_precio * cah_ventas_peso) as total_sale_value
                      FROM cah_ventas WHERE cah_ventas_tagid = ? ORDER BY cah_ventas_fecha DESC";
$stmt_sales_detailed = $conn->prepare($sql_sales_detailed);

if ($stmt_sales_detailed) {
    $tagid_int = (int)$tagid;
    $stmt_sales_detailed->bind_param('i', $tagid_int);
    $stmt_sales_detailed->execute();
    $result_sales_detailed = $stmt_sales_detailed->get_result();

    if ($result_sales_detailed->num_rows > 0) {
        $pdf->SectionHeader('Registros Detallados de Ventas');
        $pdf->TableHeader(['Fecha', 'Presentación', 'Cantidad', 'Precio/Kg', 'Peso (Kg)', 'Talla', 'Total Venta']);
        
        // Initialize summary variables
        $total_sales_value = 0;
        $total_quantity = 0;
        $total_weight = 0;
        $price_sum = 0;
        $sales_count = 0;
        
        while ($row = $result_sales_detailed->fetch_assoc()) {
            $sale_value = $row['total_sale_value'];
            $total_sales_value += $sale_value;
            $total_quantity += $row['cah_ventas_cantidad'];
            $total_weight += $row['cah_ventas_peso'];
            $price_sum += $row['cah_ventas_precio'];
            $sales_count++;
            
            $pdf->TableRow([
                $row['cah_ventas_fecha'],
                $row['cah_ventas_presentacion'],
                number_format($row['cah_ventas_cantidad']),
                '$' . number_format($row['cah_ventas_precio'], 2),
                number_format($row['cah_ventas_peso'], 2) . ' kg',
                $row['cah_ventas_talla'],
                '$' . number_format($sale_value, 2)
            ], 7);
        }
        $pdf->Ln(5);
        
        // Calculate averages
        $average_price = $sales_count > 0 ? $price_sum / $sales_count : 0;
        
        // Display summary information
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(240, 248, 255);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Ventas'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total en Ventas', '$' . number_format($total_sales_value, 2), 15, null, 60);
        $pdf->InfoBox('Precio Promedio/Kg', '$' . number_format($average_price, 2), 105, $pdf->GetY() - 8, 60);
        
        $pdf->InfoBox('Total Cantidad Vendida', number_format($total_quantity) . ' camarones', 15, null, 60);
        $pdf->InfoBox('Peso Total Vendido', number_format($total_weight, 2) . ' kg', 105, $pdf->GetY() - 8, 60);
        
        $pdf->InfoBox('Número de Ventas', number_format($sales_count) . ' registros', 15, null, 60);
        
        // Calculate average sale value per transaction
        $avg_sale_per_transaction = $sales_count > 0 ? $total_sales_value / $sales_count : 0;
        $pdf->InfoBox('Promedio por Venta', '$' . number_format($avg_sale_per_transaction, 2), 105, $pdf->GetY() - 8, 60);
        
        $pdf->Ln(8);
    } else {
        // Show message when no sales records exist
        $pdf->SectionHeader('Información de Ventas');
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se han registrado ventas para este estanque.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}

// Weight Records
$sql_weight = "SELECT p.cah_peso_fecha, c.poblacion_actual, p.cah_peso_promedio, p.cah_peso_precio, c.supervivencia 
               FROM cah_peso p
               INNER JOIN camarones c ON p.cah_peso_tagid = c.tagid
               WHERE p.cah_peso_tagid = ? 
               ORDER BY p.cah_peso_fecha DESC LIMIT 10";
$stmt_weight = $conn->prepare($sql_weight);
if ($stmt_weight) {
    $stmt_weight->bind_param('s', $tagid);
    $stmt_weight->execute();
    $result_weight = $stmt_weight->get_result();

    if ($result_weight->num_rows > 0) {
        $pdf->SectionHeader('Registros de Peso y Supervivencia');
        $pdf->TableHeader(['Fecha', 'Población Actual', 'Peso Promedio (g)', 'Precio', 'Supervivencia (%)']);
        
        while ($row = $result_weight->fetch_assoc()) {
            $pdf->TableRow([
                $row['cah_peso_fecha'],
                number_format($row['poblacion_actual']),
                number_format($row['cah_peso_promedio'], 3),
                '$' . number_format($row['cah_peso_precio'], 2),
                number_format($row['supervivencia'], 2) . '%'
            ], 5);
        }
        $pdf->Ln(8);
    }
}

// Feed/Concentrate Records
$sql_feed = "SELECT cah_concentrado_fecha_inicio, cah_concentrado_fecha_fin, cah_concentrado_etapa, 
             cah_concentrado_producto, cah_concentrado_racion, cah_concentrado_costo,
             DATEDIFF(cah_concentrado_fecha_fin, cah_concentrado_fecha_inicio) + 1 as dias_alimentacion,
             (cah_concentrado_racion * cah_concentrado_costo * (DATEDIFF(cah_concentrado_fecha_fin, cah_concentrado_fecha_inicio) + 1)) as inversion
             FROM cah_concentrado WHERE cah_concentrado_tagid = ? ORDER BY cah_concentrado_fecha_inicio DESC LIMIT 10";
$stmt_feed = $conn->prepare($sql_feed);
if ($stmt_feed) {
    $stmt_feed->bind_param('s', $tagid);
    $stmt_feed->execute();
    $result_feed = $stmt_feed->get_result();

    if ($result_feed->num_rows > 0) {
        $pdf->SectionHeader('Registros de Alimentación (Concentrado)');
        $pdf->TableHeader(['Fecha Inicio', 'Fecha Fin', 'Etapa', 'Producto', 'Ración/Día', 'Costo', 'Días', 'Inversión']);
        
        // Initialize total investment variable for displayed records
        $displayed_inversion = 0;
        
        while ($row = $result_feed->fetch_assoc()) {
            $inversion = $row['inversion'];
            $displayed_inversion += $inversion;
            
            $pdf->TableRow([
                $row['cah_concentrado_fecha_inicio'],
                $row['cah_concentrado_fecha_fin'],
                $row['cah_concentrado_etapa'],
                $row['cah_concentrado_producto'],
                number_format($row['cah_concentrado_racion'], 2),
                '$' . number_format($row['cah_concentrado_costo'], 2),
                $row['dias_alimentacion'] . ' días',
                '$' . number_format($inversion, 2)
            ], 8);
        }
        $pdf->Ln(5);
        
        // Calculate total investment from ALL records (not just displayed ones)
        $sql_total_inversion = "SELECT SUM(cah_concentrado_racion * cah_concentrado_costo * (DATEDIFF(cah_concentrado_fecha_fin, cah_concentrado_fecha_inicio) + 1)) as total_inversion 
                               FROM cah_concentrado WHERE cah_concentrado_tagid = ?";
        $stmt_total_inversion = $conn->prepare($sql_total_inversion);
        $total_inversion = 0;
        
        if ($stmt_total_inversion) {
            $stmt_total_inversion->bind_param('s', $tagid);
            $stmt_total_inversion->execute();
            $result_total = $stmt_total_inversion->get_result();
            $total_row = $result_total->fetch_assoc();
            $total_inversion = $total_row['total_inversion'] ?: 0;
        }
        
        // Display total investment summary
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 248, 220);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Inversión en Concentrado'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Inversión en Concentrado', '$' . number_format($total_inversion, 2), 15, null, 85);
        
        // Show additional info if there are more records than displayed
        $sql_count = "SELECT COUNT(*) as total_records FROM cah_concentrado WHERE cah_concentrado_tagid = ?";
        $stmt_count = $conn->prepare($sql_count);
        if ($stmt_count) {
            $stmt_count->bind_param('s', $tagid);
            $stmt_count->execute();
            $count_result = $stmt_count->get_result();
            $count_row = $count_result->fetch_assoc();
            $total_records = $count_row['total_records'];
            
            if ($total_records > 10) {
                $pdf->InfoBox('Registros de Alimentación', $total_records . ' registros totales (mostrando últimos 10)', 105, $pdf->GetY() - 8, 85);
            }
        }
        
        $pdf->Ln(8);
    }
}

// Fermented Feed Records
$sql_fermentados = "SELECT cah_fermentados_fecha_inicio, cah_fermentados_fecha_fin, cah_fermentados_etapa, 
                   cah_fermentados_producto, cah_fermentados_racion, cah_fermentados_costo,
                   DATEDIFF(cah_fermentados_fecha_fin, cah_fermentados_fecha_inicio) + 1 as dias_alimentacion,
                   (cah_fermentados_racion * cah_fermentados_costo * (DATEDIFF(cah_fermentados_fecha_fin, cah_fermentados_fecha_inicio) + 1)) as inversion
                   FROM cah_fermentados WHERE cah_fermentados_tagid = ? ORDER BY cah_fermentados_fecha_inicio DESC LIMIT 10";
$stmt_fermentados = $conn->prepare($sql_fermentados);
if ($stmt_fermentados) {
    $stmt_fermentados->bind_param('s', $tagid);
    $stmt_fermentados->execute();
    $result_fermentados = $stmt_fermentados->get_result();

    if ($result_fermentados->num_rows > 0) {
        $pdf->SectionHeader('Registros de Alimentación (Fermentados)');
        $pdf->TableHeader(['Fecha Inicio', 'Fecha Fin', 'Etapa', 'Producto', 'Ración/Día', 'Costo', 'Días', 'Inversión']);
        
        // Initialize total investment variable for displayed records
        $displayed_inversion_fermentados = 0;
        
        while ($row = $result_fermentados->fetch_assoc()) {
            $inversion = $row['inversion'];
            $displayed_inversion_fermentados += $inversion;
            
            $pdf->TableRow([
                $row['cah_fermentados_fecha_inicio'],
                $row['cah_fermentados_fecha_fin'],
                $row['cah_fermentados_etapa'],
                $row['cah_fermentados_producto'],
                number_format($row['cah_fermentados_racion'], 2),
                '$' . number_format($row['cah_fermentados_costo'], 2),
                $row['dias_alimentacion'] . ' días',
                '$' . number_format($inversion, 2)
            ], 8);
        }
        $pdf->Ln(5);
        
        // Calculate total investment from ALL fermentados records
        $sql_total_inversion_fermentados = "SELECT SUM(cah_fermentados_racion * cah_fermentados_costo * (DATEDIFF(cah_fermentados_fecha_fin, cah_fermentados_fecha_inicio) + 1)) as total_inversion 
                                           FROM cah_fermentados WHERE cah_fermentados_tagid = ?";
        $stmt_total_inversion_fermentados = $conn->prepare($sql_total_inversion_fermentados);
        $total_inversion_fermentados = 0;
        
        if ($stmt_total_inversion_fermentados) {
            $stmt_total_inversion_fermentados->bind_param('s', $tagid);
            $stmt_total_inversion_fermentados->execute();
            $result_total_fermentados = $stmt_total_inversion_fermentados->get_result();
            $total_row_fermentados = $result_total_fermentados->fetch_assoc();
            $total_inversion_fermentados = $total_row_fermentados['total_inversion'] ?: 0;
        }
        
        // Display total investment summary
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(240, 255, 240);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Inversión en Fermentados'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Inversión en Fermentados', '$' . number_format($total_inversion_fermentados, 2), 15, null, 85);
        
        // Show additional info if there are more records than displayed
        $sql_count_fermentados = "SELECT COUNT(*) as total_records FROM cah_fermentados WHERE cah_fermentados_tagid = ?";
        $stmt_count_fermentados = $conn->prepare($sql_count_fermentados);
        if ($stmt_count_fermentados) {
            $stmt_count_fermentados->bind_param('s', $tagid);
            $stmt_count_fermentados->execute();
            $count_result_fermentados = $stmt_count_fermentados->get_result();
            $count_row_fermentados = $count_result_fermentados->fetch_assoc();
            $total_records_fermentados = $count_row_fermentados['total_records'];
            
            if ($total_records_fermentados > 10) {
                $pdf->InfoBox('Registros de Fermentados', $total_records_fermentados . ' registros totales (mostrando últimos 10)', 105, $pdf->GetY() - 8, 85);
            }
        }
        
        $pdf->Ln(8);
    }
}

// Flour Feed Records
$sql_harinas = "SELECT cah_harinas_fecha_inicio, cah_harinas_fecha_fin, cah_harinas_etapa, 
               cah_harinas_producto, cah_harinas_racion, cah_harinas_costo,
               DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) + 1 as dias_alimentacion,
               (cah_harinas_racion * cah_harinas_costo * (DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) + 1)) as inversion
               FROM cah_harinas WHERE cah_harinas_tagid = ? ORDER BY cah_harinas_fecha_inicio DESC LIMIT 10";
$stmt_harinas = $conn->prepare($sql_harinas);
if ($stmt_harinas) {
    $stmt_harinas->bind_param('s', $tagid);
    $stmt_harinas->execute();
    $result_harinas = $stmt_harinas->get_result();

    if ($result_harinas->num_rows > 0) {
        $pdf->SectionHeader('Registros de Alimentación (Harinas)');
        $pdf->TableHeader(['Fecha Inicio', 'Fecha Fin', 'Etapa', 'Producto', 'Ración/Día', 'Costo', 'Días', 'Inversión']);
        
        // Initialize total investment variable for displayed records
        $displayed_inversion_harinas = 0;
        
        while ($row = $result_harinas->fetch_assoc()) {
            $inversion = $row['inversion'];
            $displayed_inversion_harinas += $inversion;
            
            $pdf->TableRow([
                $row['cah_harinas_fecha_inicio'],
                $row['cah_harinas_fecha_fin'],
                $row['cah_harinas_etapa'],
                $row['cah_harinas_producto'],
                number_format($row['cah_harinas_racion'], 2),
                '$' . number_format($row['cah_harinas_costo'], 2),
                $row['dias_alimentacion'] . ' días',
                '$' . number_format($inversion, 2)
            ], 8);
        }
        $pdf->Ln(5);
        
        // Calculate total investment from ALL harinas records
        $sql_total_inversion_harinas = "SELECT SUM(cah_harinas_racion * cah_harinas_costo * (DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) + 1)) as total_inversion 
                                       FROM cah_harinas WHERE cah_harinas_tagid = ?";
        $stmt_total_inversion_harinas = $conn->prepare($sql_total_inversion_harinas);
        $total_inversion_harinas = 0;
        
        if ($stmt_total_inversion_harinas) {
            $stmt_total_inversion_harinas->bind_param('s', $tagid);
            $stmt_total_inversion_harinas->execute();
            $result_total_harinas = $stmt_total_inversion_harinas->get_result();
            $total_row_harinas = $result_total_harinas->fetch_assoc();
            $total_inversion_harinas = $total_row_harinas['total_inversion'] ?: 0;
        }
        
        // Display total investment summary
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 240, 240);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Inversión en Harinas'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Inversión en Harinas', '$' . number_format($total_inversion_harinas, 2), 15, null, 85);
        
        // Show additional info if there are more records than displayed
        $sql_count_harinas = "SELECT COUNT(*) as total_records FROM cah_harinas WHERE cah_harinas_tagid = ?";
        $stmt_count_harinas = $conn->prepare($sql_count_harinas);
        if ($stmt_count_harinas) {
            $stmt_count_harinas->bind_param('s', $tagid);
            $stmt_count_harinas->execute();
            $count_result_harinas = $stmt_count_harinas->get_result();
            $count_row_harinas = $count_result_harinas->fetch_assoc();
            $total_records_harinas = $count_row_harinas['total_records'];
            
            if ($total_records_harinas > 10) {
                $pdf->InfoBox('Registros de Harinas', $total_records_harinas . ' registros totales (mostrando últimos 10)', 105, $pdf->GetY() - 8, 85);
            }
        }
        
        $pdf->Ln(8);
    }
}

// Monthly Feed Costs Summary
$pdf->SectionHeader('Resumen Mensual de Costos de Alimentación');

// Get monthly costs for all feed types
$sql_monthly_costs = "
    SELECT 
        YEAR(fecha) as year,
        MONTH(fecha) as month,
        MONTHNAME(fecha) as month_name,
        feed_type,
        SUM(total_cost) as monthly_cost
    FROM (
        SELECT 
            cah_concentrado_fecha_inicio as fecha,
            'Concentrado' as feed_type,
            (cah_concentrado_racion * cah_concentrado_costo * (DATEDIFF(cah_concentrado_fecha_fin, cah_concentrado_fecha_inicio) + 1)) as total_cost
        FROM cah_concentrado 
        WHERE cah_concentrado_tagid = ?
        
        UNION ALL
        
        SELECT 
            cah_fermentados_fecha_inicio as fecha,
            'Fermentados' as feed_type,
            (cah_fermentados_racion * cah_fermentados_costo * (DATEDIFF(cah_fermentados_fecha_fin, cah_fermentados_fecha_inicio) + 1)) as total_cost
        FROM cah_fermentados 
        WHERE cah_fermentados_tagid = ?
        
        UNION ALL
        
        SELECT 
            cah_harinas_fecha_inicio as fecha,
            'Harinas' as feed_type,
            (cah_harinas_racion * cah_harinas_costo * (DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) + 1)) as total_cost
        FROM cah_harinas 
        WHERE cah_harinas_tagid = ?
    ) as all_feeds
    GROUP BY year, month, feed_type
    ORDER BY year DESC, month DESC
";

$stmt_monthly_costs = $conn->prepare($sql_monthly_costs);
if ($stmt_monthly_costs) {
    $stmt_monthly_costs->bind_param('sss', $tagid, $tagid, $tagid);
    $stmt_monthly_costs->execute();
    $result_monthly_costs = $stmt_monthly_costs->get_result();
    
    // Organize data by month
    $monthly_data = [];
    $grand_total_concentrado = 0;
    $grand_total_fermentados = 0;
    $grand_total_harinas = 0;
    
    while ($row = $result_monthly_costs->fetch_assoc()) {
        $month_key = $row['year'] . '-' . sprintf('%02d', $row['month']);
        $month_name = $row['month_name'] . ' ' . $row['year'];
        
        if (!isset($monthly_data[$month_key])) {
            $monthly_data[$month_key] = [
                'month_name' => $month_name,
                'concentrado' => 0,
                'fermentados' => 0,
                'harinas' => 0,
                'total' => 0
            ];
        }
        
        $cost = floatval($row['monthly_cost']);
        $feed_type = strtolower($row['feed_type']);
        
        $monthly_data[$month_key][$feed_type] = $cost;
        $monthly_data[$month_key]['total'] += $cost;
        
        // Add to grand totals
        if ($feed_type == 'concentrado') $grand_total_concentrado += $cost;
        if ($feed_type == 'fermentados') $grand_total_fermentados += $cost;
        if ($feed_type == 'harinas') $grand_total_harinas += $cost;
    }
    
    if (!empty($monthly_data)) {
        $pdf->TableHeader(['Mes', 'Concentrado', 'Harinas', 'Fermentados', 'Total']);
        
        // Sort by month (most recent first)
        krsort($monthly_data);
        
        foreach ($monthly_data as $month_data) {
            $pdf->TableRow([
                $month_data['month_name'],
                '$' . number_format($month_data['concentrado'], 2),
                '$' . number_format($month_data['harinas'], 2),
                '$' . number_format($month_data['fermentados'], 2),
                '$' . number_format($month_data['total'], 2)
            ], 5);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $grand_feeding_total= $grand_total_concentrado + $grand_total_fermentados + $grand_total_harinas;
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [50, 35, 35, 35, 35];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_concentrado, 2), 1, 0, 'C', true);
        $pdf->Cell($widths[2], 8, '$' . number_format($grand_total_harinas, 2), 1, 0, 'C', true);
        $pdf->Cell($widths[3], 8, '$' . number_format($grand_total_fermentados, 2), 1, 0, 'C', true);
        $pdf->Cell($widths[4], 8, '$' . number_format($grand_total, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(245, 245, 255);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen Total de Alimentación'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Concentrado', '$' . number_format($grand_total_concentrado, 2), 15, null, 60);
        $pdf->InfoBox('Total Harinas', '$' . number_format($grand_total_harinas, 2), 105, $pdf->GetY() - 8, 60);
        $pdf->InfoBox('Total Fermentados', '$' . number_format($grand_total_fermentados, 2), 15, null, 60);
        
        $pdf->InfoBox('GRAN TOTAL ALIMENTACIÓN', '$' . number_format($grand_feeding_total, 2), 105, $pdf->GetY() - 8, 60);
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de alimentación para generar el resumen mensual.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}


// Water Quality Monitoring Sections
// Water Temperature Records
$sql_temp = "SELECT fecha, temperature_celsius, source, phase, hora 
             FROM water_temperature_log WHERE pond_id = ? ORDER BY fecha DESC, hora DESC LIMIT 10";
$stmt_temp = $conn->prepare($sql_temp);
if ($stmt_temp) {
    $stmt_temp->bind_param('s', $tagid);
    $stmt_temp->execute();
    $result_temp = $stmt_temp->get_result();

    if ($result_temp->num_rows > 0) {
        $pdf->SectionHeader('Registros de Temperatura');
        $pdf->TableHeader(['Fecha', 'Hora', 'Temperatura (°C)', 'Fuente', 'Fase']);
        
        while ($row = $result_temp->fetch_assoc()) {
            $pdf->TableRow([
                $row['fecha'],
                $row['hora'],
                number_format($row['temperature_celsius'], 2) . '°C',
                $row['source'],
                $row['phase']
            ], 5);
        }
        $pdf->Ln(8);
    }
}

// Water pH Records
$sql_ph = "SELECT timestamp, ph_level, source, phase 
           FROM water_ph_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
$stmt_ph = $conn->prepare($sql_ph);
if ($stmt_ph) {
    $stmt_ph->bind_param('s', $tagid);
    $stmt_ph->execute();
    $result_ph = $stmt_ph->get_result();

    if ($result_ph->num_rows > 0) {
        $pdf->SectionHeader('Registros de pH');
        $pdf->TableHeader(['Fecha/Hora', 'Nivel de pH', 'Fuente', 'Fase']);
        
        while ($row = $result_ph->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['ph_level'], 2),
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// pH Control Expenses
$pdf->SectionHeader('Gastos Control pH');

$sql_ph_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_ph_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_ph_expenses = $conn->prepare($sql_ph_expenses);
if ($stmt_ph_expenses) {
    $stmt_ph_expenses->bind_param('s', $tagid);
    $stmt_ph_expenses->execute();
    $result_ph_expenses = $stmt_ph_expenses->get_result();

    if ($result_ph_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto pH']);
        
        $grand_total_ph = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_ph_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_ph += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_ph, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control pH'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos pH', '$' . number_format($grand_total_ph, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_ph / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de pH.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}

// Water Oxygen Records
$sql_oxygen = "SELECT timestamp, oxygen_mg_l, source, phase 
               FROM water_oxygen_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
$stmt_oxygen = $conn->prepare($sql_oxygen);
if ($stmt_oxygen) {
    $stmt_oxygen->bind_param('s', $tagid);
    $stmt_oxygen->execute();
    $result_oxygen = $stmt_oxygen->get_result();

    if ($result_oxygen->num_rows > 0) {
        $pdf->SectionHeader('Registros de Oxígeno');
        $pdf->TableHeader(['Fecha/Hora', 'Oxígeno (mg/L)', 'Fuente', 'Fase']);
        
        while ($row = $result_oxygen->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['oxygen_mg_l'], 2) . ' mg/L',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Oxygen Control Expenses
$pdf->SectionHeader('Gastos Control Oxigeno');

$sql_oxygen_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_oxygen_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_oxygen_expenses = $conn->prepare($sql_oxygen_expenses);
if ($stmt_oxygen_expenses) {
    $stmt_oxygen_expenses->bind_param('s', $tagid);
    $stmt_oxygen_expenses->execute();
    $result_oxygen_expenses = $stmt_oxygen_expenses->get_result();

    if ($result_oxygen_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Oxigeno']);
        
        $grand_total_oxygen = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_oxygen_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_oxygen += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_oxygen, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Oxigeno'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Oxigeno', '$' . number_format($grand_total_oxygen, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_oxygen / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de oxígeno.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}

// Water Salinidad Records
$sql_salinidad = "SELECT timestamp, salinity_ppt, source, phase 
               FROM water_salinity_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
$stmt_salinidad = $conn->prepare($sql_salinidad);
if ($stmt_salinidad) {
    $stmt_salinidad->bind_param('s', $tagid);
    $stmt_salinidad->execute();
    $result_salinidad = $stmt_salinidad->get_result();

    if ($result_salinidad->num_rows > 0) {
        $pdf->SectionHeader('Registros de Salinidad');
        $pdf->TableHeader(['Fecha/Hora', 'Salinidad (ppt)', 'Sensor', 'Fase']);
        
        while ($row = $result_salinidad->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['salinity_ppt'], 2) . ' ppt',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Salinity Control Expenses
$pdf->SectionHeader('Gastos Control Salinidad');

$sql_salinity_expenses = "SELECT
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_salinity_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_salinity_expenses = $conn->prepare($sql_salinity_expenses);
if ($stmt_salinity_expenses) {
    $stmt_salinity_expenses->bind_param('s', $tagid);
    $stmt_salinity_expenses->execute();
    $result_salinity_expenses = $stmt_salinity_expenses->get_result();

    if ($result_salinity_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Salinidad']);
        
        $grand_total_salinity = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_salinity_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_salinity += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_salinity, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Salinidad'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Salinidad', '$' . number_format($grand_total_salinity, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_salinity / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de salinidad.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}

// Water Amoniaco Records
$sql_amoniaco = "SELECT timestamp, total_ammonia_mg_l, source, phase 
               FROM water_ammonia_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
    $stmt_amoniaco = $conn->prepare($sql_amoniaco);
if ($stmt_amoniaco) {
    $stmt_amoniaco->bind_param('s', $tagid);
    $stmt_amoniaco->execute();
    $result_amoniaco = $stmt_amoniaco->get_result();

    if ($result_amoniaco->num_rows > 0) {
        $pdf->SectionHeader('Registros de Amoniaco');
        $pdf->TableHeader(['Fecha/Hora', 'Amoniaco (mg/L)', 'Sensor', 'Fase']);
        
        while ($row = $result_amoniaco->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['total_ammonia_mg_l'], 2) . ' mg/L',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Amoniaco Control Expenses
$pdf->SectionHeader('Gastos Control Amoniaco');

$sql_amoniaco_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_ammonia_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_amoniaco_expenses = $conn->prepare($sql_amoniaco_expenses);
if ($stmt_amoniaco_expenses) {
    $stmt_amoniaco_expenses->bind_param('s', $tagid);
    $stmt_amoniaco_expenses->execute();
    $result_amoniaco_expenses = $stmt_amoniaco_expenses->get_result();

    if ($result_amoniaco_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Amoniaco']);

        $grand_total_amoniaco = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_amoniaco_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_amoniaco += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_amoniaco, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Amoniaco'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Amoniaco', '$' . number_format($grand_total_amoniaco, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_amoniaco / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de amoniaco.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}


// Water Nitritos Records
$sql_nitritos = "SELECT timestamp, nitrite_mg_l, source, phase 
               FROM water_nitrite_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
    $stmt_nitritos = $conn->prepare($sql_nitritos);
if ($stmt_nitritos) {
    $stmt_nitritos->bind_param('s', $tagid);
    $stmt_nitritos->execute();
    $result_nitritos = $stmt_nitritos->get_result();

    if ($result_nitritos->num_rows > 0) {
        $pdf->SectionHeader('Registros de Nitritos');
        $pdf->TableHeader(['Fecha/Hora', 'Nitritos (mg/L)', 'Sensor', 'Fase']);
        
        while ($row = $result_nitritos->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['nitrite_mg_l'], 2) . ' mg/L',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Nitritos Control Expenses
$pdf->SectionHeader('Gastos Control Nitritos');

$sql_nitrite_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_nitrite_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_nitrite_expenses = $conn->prepare($sql_nitrite_expenses);
if ($stmt_nitrite_expenses) {
    $stmt_nitrite_expenses->bind_param('s', $tagid);
    $stmt_nitrite_expenses->execute();
    $result_nitrite_expenses = $stmt_nitrite_expenses->get_result();

    if ($result_nitrite_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Nitritos']);

        $grand_total_nitrite = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_nitrite_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_nitrite += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_nitrite, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Nitritos'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Nitritos', '$' . number_format($grand_total_nitrite, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_nitrite / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de nitritos.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}

// Water Alkalinity Records
$sql_alkalinity = "SELECT timestamp, alkalinity_mg_l, source, phase 
               FROM water_alkalinity_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
    $stmt_alkalinity = $conn->prepare($sql_alkalinity);
if ($stmt_alkalinity) {
    $stmt_alkalinity->bind_param('s', $tagid);
    $stmt_alkalinity->execute();
    $result_alkalinity = $stmt_alkalinity->get_result();

    if ($result_alkalinity->num_rows > 0) {
        $pdf->SectionHeader('Registros de Alcalinidad');
        $pdf->TableHeader(['Fecha/Hora', 'Alcalinidad (mg/L)', 'Sensor', 'Fase']);
        
        while ($row = $result_alkalinity->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['alkalinity_mg_l'], 2) . ' mg/L',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Alcalinidad Control Expenses
$pdf->SectionHeader('Gastos Control Alcalinidad');

$sql_alkalinity_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_alkalinity_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_alkalinity_expenses = $conn->prepare($sql_alkalinity_expenses);
if ($stmt_alkalinity_expenses) {
    $stmt_alkalinity_expenses->bind_param('s', $tagid);
    $stmt_alkalinity_expenses->execute();
    $result_alkalinity_expenses = $stmt_alkalinity_expenses->get_result();

    if ($result_alkalinity_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Alcalinidad']);

        $grand_total_alkalinity = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_alkalinity_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_alkalinity += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_alkalinity, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Alcalinidad'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Alcalinidad', '$' . number_format($grand_total_alkalinity, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_alkalinity / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de alcalinidad.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}


// Water Transparency Records
$sql_transparency = "SELECT timestamp, transparency_cm, source, phase 
               FROM water_transparency_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
    $stmt_transparency = $conn->prepare($sql_transparency);
if ($stmt_transparency) {
    $stmt_transparency->bind_param('s', $tagid);
    $stmt_transparency->execute();
    $result_transparency = $stmt_transparency->get_result();

    if ($result_transparency->num_rows > 0) {
        $pdf->SectionHeader('Registros de Transparencia');
        $pdf->TableHeader(['Fecha/Hora', 'Transparencia (cm)', 'Sensor', 'Fase']);
        
        while ($row = $result_transparency->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['transparency_cm'], 2) . ' cm',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Transparencia Control Expenses
$pdf->SectionHeader('Gastos Control Transparencia');

$sql_transparency_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_transparency_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_transparency_expenses = $conn->prepare($sql_transparency_expenses);
if ($stmt_transparency_expenses) {
    $stmt_transparency_expenses->bind_param('s', $tagid);
    $stmt_transparency_expenses->execute();
    $result_transparency_expenses = $stmt_transparency_expenses->get_result();

    if ($result_transparency_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Transparencia']);

        $grand_total_transparency = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_transparency_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_transparency += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_transparency, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Transparencia'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Transparencia', '$' . number_format($grand_total_transparency, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_transparency / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de transparencia.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}


// Water Redox Records
$sql_redox = "SELECT timestamp, redox_mv, source, phase 
               FROM water_redox_log WHERE pond_id = ? ORDER BY timestamp DESC LIMIT 10";
    $stmt_redox = $conn->prepare($sql_redox);
if ($stmt_redox) {
    $stmt_redox->bind_param('s', $tagid);
    $stmt_redox->execute();
    $result_redox = $stmt_redox->get_result();

    if ($result_redox->num_rows > 0) {
        $pdf->SectionHeader('Registros de Redox');
        $pdf->TableHeader(['Fecha/Hora', 'Redox (mV)', 'Sensor', 'Fase']);
        
        while ($row = $result_redox->fetch_assoc()) {
            $pdf->TableRow([
                $row['timestamp'],
                number_format($row['redox_mv'], 2) . ' mV',
                $row['source'],
                $row['phase']
            ], 4);
        }
        $pdf->Ln(8);
    }
}

// Redox Control Expenses
$pdf->SectionHeader('Gastos Control Redox');

$sql_redox_expenses = "SELECT 
                        YEAR(timestamp) as year,
                        MONTH(timestamp) as month,
                        MONTHNAME(timestamp) as month_name,
                        SUM(product_qty * product_price) as monthly_expense
                    FROM water_redox_log 
                    WHERE pond_id = ? 
                        AND product_qty IS NOT NULL 
                        AND product_price IS NOT NULL
                        AND product_qty > 0
                        AND product_price > 0
                    GROUP BY YEAR(timestamp), MONTH(timestamp)
                    ORDER BY year DESC, month DESC";

$stmt_redox_expenses = $conn->prepare($sql_redox_expenses);
if ($stmt_redox_expenses) {
    $stmt_redox_expenses->bind_param('s', $tagid);
    $stmt_redox_expenses->execute();
    $result_redox_expenses = $stmt_redox_expenses->get_result();

    if ($result_redox_expenses->num_rows > 0) {
        $pdf->TableHeader(['Mes', 'Gasto Redox']);

        $grand_total_redox = 0;
        $monthly_expenses = [];
        
        // Collect all monthly data
        while ($row = $result_redox_expenses->fetch_assoc()) {
            $month_name = $row['month_name'] . ' ' . $row['year'];
            $monthly_expense = floatval($row['monthly_expense']);
            
            $monthly_expenses[] = [
                'month_name' => $month_name,
                'expense' => $monthly_expense
            ];
            
            $grand_total_redox += $monthly_expense;
        }
        
        // Display monthly rows
        foreach ($monthly_expenses as $expense_data) {
            $pdf->TableRow([
                $expense_data['month_name'],
                '$' . number_format($expense_data['expense'], 2)
            ], 2);
        }
        
        $pdf->Ln(3);
        
        // Grand Total Row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        
        $widths = [85, 85];
        
        $pdf->Cell($widths[0], 8, $pdf->EncodeText('GRAN TOTAL'), 1, 0, 'C', true);
        $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_redox, 2), 1, 1, 'C', true);
        
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 245, 238);
        $pdf->Cell(0, 8, $pdf->EncodeText('Resumen de Gastos en Control Redox'), 0, 1, 'C', true);
        $pdf->Ln(2);
        
        $pdf->InfoBox('Total Gastos Redox', '$' . number_format($grand_total_redox, 2), 15, null, 85);
        
        // Show number of months with expenses
        $months_count = count($monthly_expenses);
        if ($months_count > 1) {
            $average_monthly = $grand_total_redox / $months_count;
            $pdf->InfoBox('Promedio Mensual', '$' . number_format($average_monthly, 2), 105, $pdf->GetY() - 8, 85);
        }
        
        $pdf->Ln(8);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron registros de gastos en control de redox.'), 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(8);
    }
}


// Summary Section
$pdf->AddPage();
$pdf->SectionHeader('Resumen Financiero y Estadísticas');

// Calculate Total feed Cost as Sum of Concentrado, Harinas and Fermentados costs
$total_concentrado_cost_query = "SELECT SUM(cah_concentrado_costo * cah_concentrado_racion * DATEDIFF(cah_concentrado_fecha_fin, cah_concentrado_fecha_inicio) + 1) as total_cost FROM cah_concentrado WHERE cah_concentrado_tagid = ?";
$stmt_total_concentrado_cost = $conn->prepare($total_concentrado_cost_query);
if ($stmt_total_concentrado_cost) {
    $stmt_total_concentrado_cost->bind_param('s', $tagid);
    $stmt_total_concentrado_cost->execute();
    $total_concentrado_cost = $stmt_total_concentrado_cost->get_result()->fetch_assoc()['total_cost'] ?: 0;
    } else {
    $total_concentrado_cost = 0;
}

$total_fermentados_cost_query = "SELECT SUM(cah_fermentados_costo * cah_fermentados_racion * DATEDIFF(cah_fermentados_fecha_fin, cah_fermentados_fecha_inicio) + 1) as total_cost FROM cah_fermentados WHERE cah_fermentados_tagid = ?";
$stmt_total_fermentados_cost = $conn->prepare($total_fermentados_cost_query);
if ($stmt_total_feed_cost) {
    $stmt_total_fermentados_cost->bind_param('s', $tagid);
    $stmt_total_fermentados_cost->execute();
    $total_fermentados_cost = $stmt_total_fermentados_cost->get_result()->fetch_assoc()['total_cost'] ?: 0;
    } else {
    $total_fermentados_cost = 0;
}

$total_harinas_cost_query = "SELECT SUM(cah_harinas_costo * cah_harinas_racion * DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) + 1) as total_cost FROM cah_harinas WHERE cah_harinas_tagid = ?";
$stmt_total_harinas_cost = $conn->prepare($total_harinas_cost_query);
if ($stmt_total_harinas_cost) {
    $stmt_total_harinas_cost->bind_param('s', $tagid);
    $stmt_total_harinas_cost->execute();
    $total_harinas_cost = $stmt_total_harinas_cost->get_result()->fetch_assoc()['total_cost'] ?: 0;
    } else {
    $total_harinas_cost = 0;
}

$pdf->InfoBox('Total en Alimentación', '$' . number_format($total_concentrado_cost, 2), 105, $pdf->GetY() - 8, 85);
$pdf->InfoBox('Total en Fermentados', '$' . number_format($total_fermentados_cost, 2), 105, $pdf->GetY() - 8, 85);
$pdf->InfoBox('Total en Harinas', '$' . number_format($total_harinas_cost, 2), 105, $pdf->GetY() - 8, 85);

$total_feed_costs = $total_concentrado_cost + $total_fermentados_cost + $total_harinas_cost;
$pdf->InfoBox('Total costo alimentacion:', '$' . number_format($total_feed_costs, 2), 15, null, 85);

// Calculate total water treatment costs
// Initialize variables in case sections weren't displayed
if (!isset($grand_total_ph)) $grand_total_ph = 0;
if (!isset($grand_total_oxygen)) $grand_total_oxygen = 0;
if (!isset($grand_total_salinity)) $grand_total_salinity = 0;
if (!isset($grand_total_amoniaco)) $grand_total_amoniaco = 0;
if (!isset($grand_total_nitrite)) $grand_total_nitrite = 0;
if (!isset($grand_total_alkalinity)) $grand_total_alkalinity = 0;
if (!isset($grand_total_transparency)) $grand_total_transparency = 0;
if (!isset($grand_total_redox)) $grand_total_redox = 0;

$total_water_treatment_costs = $grand_total_redox + $grand_total_transparency + $grand_total_alkalinity + 
                              $grand_total_nitrite + $grand_total_amoniaco + $grand_total_salinity + 
                              $grand_total_oxygen + $grand_total_ph;

$pdf->InfoBox('Total Tratamiento Agua:', '$' . number_format($total_water_treatment_costs, 2), 105, $pdf->GetY() - 8, 85);

// Pie Chart for Variable Costs
$pdf->Ln(10);
$pdf->SectionHeader('Distribución de Costos Variables');

// Calculate correct grand total using the right variables
// Use grand_feeding_total from the monthly summary section
$grand_total_all_costs = $grand_feeding_total + $total_water_treatment_costs;

if ($grand_total_all_costs > 0) {
    // Calculate main category percentages
    $feed_percentage = ($grand_feeding_total / $grand_total_all_costs) * 100;
    $water_treatment_percentage = ($total_water_treatment_costs / $grand_total_all_costs) * 100;
    
    // Individual water treatment percentages (relative to grand total)
    $ph_percentage = ($grand_total_ph / $grand_total_all_costs) * 100;
    $oxygen_percentage = ($grand_total_oxygen / $grand_total_all_costs) * 100;
    $salinity_percentage = ($grand_total_salinity / $grand_total_all_costs) * 100;
    $amoniaco_percentage = ($grand_total_amoniaco / $grand_total_all_costs) * 100;
    $nitrite_percentage = ($grand_total_nitrite / $grand_total_all_costs) * 100;
    $alkalinity_percentage = ($grand_total_alkalinity / $grand_total_all_costs) * 100;
    $transparency_percentage = ($grand_total_transparency / $grand_total_all_costs) * 100;
    $redox_percentage = ($grand_total_redox / $grand_total_all_costs) * 100;
    
    // Individual feed percentages (relative to grand total)
    $concentrado_percentage = ($grand_total_concentrado / $grand_total_all_costs) * 100;
    $fermentados_percentage = ($grand_total_fermentados / $grand_total_all_costs) * 100;
    $harinas_percentage = ($grand_total_harinas / $grand_total_all_costs) * 100;
    
    // Create pie chart data array
    $pie_data = [];
    
    // Add feed costs if they exist
    if ($grand_total_concentrado > 0) {
        $pie_data[] = ['Concentrado', $concentrado_percentage, $grand_total_concentrado];
    }
    if ($grand_total_fermentados > 0) {
        $pie_data[] = ['Fermentados', $fermentados_percentage, $grand_total_fermentados];
    }
    if ($grand_total_harinas > 0) {
        $pie_data[] = ['Harinas', $harinas_percentage, $grand_total_harinas];
    }
    
    // Add water treatment costs if they exist
    if ($grand_total_ph > 0) {
        $pie_data[] = ['Control pH', $ph_percentage, $grand_total_ph];
    }
    if ($grand_total_oxygen > 0) {
        $pie_data[] = ['Control Oxígeno', $oxygen_percentage, $grand_total_oxygen];
    }
    if ($grand_total_salinity > 0) {
        $pie_data[] = ['Control Salinidad', $salinity_percentage, $grand_total_salinity];
    }
    if ($grand_total_amoniaco > 0) {
        $pie_data[] = ['Control Amoniaco', $amoniaco_percentage, $grand_total_amoniaco];
    }
    if ($grand_total_nitrite > 0) {
        $pie_data[] = ['Control Nitritos', $nitrite_percentage, $grand_total_nitrite];
    }
    if ($grand_total_alkalinity > 0) {
        $pie_data[] = ['Control Alcalinidad', $alkalinity_percentage, $grand_total_alkalinity];
    }
    if ($grand_total_transparency > 0) {
        $pie_data[] = ['Control Transparencia', $transparency_percentage, $grand_total_transparency];
    }
    if ($grand_total_redox > 0) {
        $pie_data[] = ['Control Redox', $redox_percentage, $grand_total_redox];
    }
    
    // Sort by percentage (highest first)
    usort($pie_data, function($a, $b) {
        return $b[1] <=> $a[1];
    });
    
    // Display verification of totals first
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(255, 255, 220);
    $pdf->Cell(0, 8, $pdf->EncodeText('Verificación de Cálculos - Gran Total de Costos Variables'), 0, 1, 'C', true);
    $pdf->Ln(3);
    
    // Show calculation breakdown
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 6, $pdf->EncodeText('Costos de Alimentación = Concentrado + Harinas + Fermentados'), 0, 1, 'L');
    $pdf->Cell(0, 6, $pdf->EncodeText('Costos de Alimentación = $' . number_format($grand_total_concentrado, 2) . ' + $' . number_format($grand_total_harinas, 2) . ' + $' . number_format($grand_total_fermentados, 2) . ' = $' . number_format($grand_feeding_total, 2)), 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Cell(0, 6, $pdf->EncodeText('Costos de Tratamiento de Agua = pH + Oxígeno + Salinidad + Amoniaco + Nitritos + Alcalinidad + Transparencia + Redox'), 0, 1, 'L');
    $pdf->Cell(0, 6, $pdf->EncodeText('Costos de Tratamiento = $' . number_format($grand_total_ph, 2) . ' + $' . number_format($grand_total_oxygen, 2) . ' + $' . number_format($grand_total_salinity, 2) . ' + $' . number_format($grand_total_amoniaco, 2) . ' + $' . number_format($grand_total_nitrite, 2) . ' + $' . number_format($grand_total_alkalinity, 2) . ' + $' . number_format($grand_total_transparency, 2) . ' + $' . number_format($grand_total_redox, 2) . ' = $' . number_format($total_water_treatment_costs, 2)), 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 6, $pdf->EncodeText('GRAN TOTAL = Alimentación + Tratamiento Agua = $' . number_format($grand_feeding_total, 2) . ' + $' . number_format($total_water_treatment_costs, 2) . ' = $' . number_format($grand_total_all_costs, 2)), 0, 1, 'L');
    $pdf->Ln(5);
    
    // Display pie chart as table with percentages
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(240, 240, 255);
    $pdf->Cell(0, 8, $pdf->EncodeText('Desglose Porcentual de Costos Variables'), 0, 1, 'C', true);
    $pdf->Ln(3);
    
    $pdf->TableHeader(['Categoría', 'Monto', 'Porcentaje']);
    
    foreach ($pie_data as $data) {
        $category = $data[0];
        $percentage = $data[1];
        $amount = $data[2];
        
        $pdf->TableRow([
            $category,
            '$' . number_format($amount, 2),
            number_format($percentage, 1) . '%'
        ], 3);
    }
    
    $pdf->Ln(3);
    
    // Grand Total Row
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->SetTextColor(0);
    
    $widths = [85, 50, 35];
    
    $pdf->Cell($widths[0], 8, $pdf->EncodeText('TOTAL COSTOS VARIABLES'), 1, 0, 'C', true);
    $pdf->Cell($widths[1], 8, '$' . number_format($grand_total_all_costs, 2), 1, 0, 'C', true);
    $pdf->Cell($widths[2], 8, '100.0%', 1, 1, 'C', true);
    
    $pdf->Ln(5);
    
    // Summary boxes showing main categories
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(255, 255, 240);
    $pdf->Cell(0, 8, $pdf->EncodeText('Resumen por Categorías Principales'), 0, 1, 'C', true);
    $pdf->Ln(2);
    
    $pdf->InfoBox('Alimentación', '$' . number_format($grand_feeding_total, 2) . ' (' . number_format($feed_percentage, 1) . '%)', 15, null, 40);
    $pdf->InfoBox('Tratamiento Agua', '$' . number_format($total_water_treatment_costs, 2) . ' (' . number_format($water_treatment_percentage, 1) . '%)', 105, $pdf->GetY() - 8, 40);
    
    $pdf->Ln(8);
    
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, $pdf->EncodeText('No se encontraron datos suficientes para generar el gráfico de distribución de costos.'), 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(8);
}

// Estado de Ganancias y Pérdidas Bruto
$pdf->Ln(10);
$pdf->SectionHeader('Estado de Ganancias y Pérdidas Bruto');

// Calculate total sales revenue from cah_ventas
$sql_total_revenue = "SELECT 
                        SUM(cah_ventas_precio * cah_ventas_peso) as total_revenue,
                        COUNT(*) as total_transactions,
                        SUM(cah_ventas_cantidad) as total_quantity_sold,
                        SUM(cah_ventas_peso) as total_weight_sold
                      FROM cah_ventas 
                      WHERE cah_ventas_tagid = ?";

$stmt_total_revenue = $conn->prepare($sql_total_revenue);
$total_revenue = 0;
$total_transactions = 0;
$total_quantity_sold = 0;
$total_weight_sold = 0;

if ($stmt_total_revenue) {
    $tagid_int = (int)$tagid;
    $stmt_total_revenue->bind_param('i', $tagid_int);
    $stmt_total_revenue->execute();
    $result_revenue = $stmt_total_revenue->get_result();
    $revenue_data = $result_revenue->fetch_assoc();
    
    $total_revenue = $revenue_data['total_revenue'] ?: 0;
    $total_transactions = $revenue_data['total_transactions'] ?: 0;
    $total_quantity_sold = $revenue_data['total_quantity_sold'] ?: 0;
    $total_weight_sold = $revenue_data['total_weight_sold'] ?: 0;
}

// Calculate gross profit/loss
$gross_profit = $total_revenue - $grand_total_all_costs;
$profit_margin = $total_revenue > 0 ? ($gross_profit / $total_revenue) * 100 : 0;

// Display P&L Statement
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(245, 250, 255);
$pdf->Cell(0, 8, $pdf->EncodeText('Estado de Resultados - Estanque ' . $tagid), 0, 1, 'C', true);
$pdf->Ln(3);

// Revenue Section
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 255, 230);
$pdf->Cell(120, 8, $pdf->EncodeText('INGRESOS'), 1, 0, 'L', true);
$pdf->Cell(50, 8, '', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(120, 6, $pdf->EncodeText('  Ingresos por Ventas de Camarones'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($total_revenue, 2), 1, 1, 'R', true);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(120, 6, $pdf->EncodeText('TOTAL INGRESOS'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($total_revenue, 2), 1, 1, 'R', true);

$pdf->Ln(2);

// Expenses Section
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(255, 230, 230);
$pdf->Cell(120, 8, $pdf->EncodeText('EGRESOS'), 1, 0, 'L', true);
$pdf->Cell(50, 8, '', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(120, 6, $pdf->EncodeText('  Costos de Alimentación'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($grand_feeding_total, 2), 1, 1, 'R', true);

$pdf->Cell(120, 6, $pdf->EncodeText('  Costos de Tratamiento de Agua'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($total_water_treatment_costs, 2), 1, 1, 'R', true);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(120, 6, $pdf->EncodeText('TOTAL EGRESOS'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($grand_total_all_costs, 2), 1, 1, 'R', true);

$pdf->Ln(2);

// Gross Profit/Loss Section
$pdf->SetFont('Arial', 'B', 10);
if ($gross_profit >= 0) {
    $pdf->SetFillColor(200, 255, 200);  // Green for profit
    $profit_text = 'GANANCIA BRUTA';
} else {
    $pdf->SetFillColor(255, 200, 200);  // Red for loss
    $profit_text = 'PÉRDIDA BRUTA';
}

$pdf->Cell(120, 10, $pdf->EncodeText($profit_text), 1, 0, 'L', true);
$pdf->Cell(50, 10, '$' . number_format($gross_profit, 2), 1, 1, 'R', true);

$pdf->Ln(5);

// Financial Metrics Summary
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(255, 255, 240);
$pdf->Cell(0, 8, $pdf->EncodeText('Métricas Financieras'), 0, 1, 'C', true);
$pdf->Ln(3);

// Financial metrics in table format (like P&L statement)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(240, 248, 255);
$pdf->Cell(120, 8, $pdf->EncodeText('INDICADORES FINANCIEROS'), 1, 0, 'L', true);
$pdf->Cell(50, 8, '', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);

// Revenue metrics
$pdf->Cell(120, 6, $pdf->EncodeText('  Total de Ingresos'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($total_revenue, 2), 1, 1, 'R', true);

$pdf->Cell(120, 6, $pdf->EncodeText('  Total de Egresos'), 1, 0, 'L', true);
$pdf->Cell(50, 6, '$' . number_format($grand_total_all_costs, 2), 1, 1, 'R', true);

if ($gross_profit >= 0) {
    $pdf->Cell(120, 6, $pdf->EncodeText('  Ganancia Bruta'), 1, 0, 'L', true);
    $pdf->Cell(50, 6, '$' . number_format($gross_profit, 2), 1, 1, 'R', true);
} else {
    $pdf->Cell(120, 6, $pdf->EncodeText('  Pérdida Bruta'), 1, 0, 'L', true);
    $pdf->Cell(50, 6, '$' . number_format(abs($gross_profit), 2), 1, 1, 'R', true);
}

$pdf->Cell(120, 6, $pdf->EncodeText('  Margen de Ganancia'), 1, 0, 'L', true);
$pdf->Cell(50, 6, number_format($profit_margin, 1) . '%', 1, 1, 'R', true);

$pdf->Ln(2);

// Sales metrics section
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(240, 255, 240);
$pdf->Cell(120, 8, $pdf->EncodeText('INDICADORES DE VENTAS'), 1, 0, 'L', true);
$pdf->Cell(50, 8, '', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);

$pdf->Cell(120, 6, $pdf->EncodeText('  Número de Transacciones'), 1, 0, 'L', true);
$pdf->Cell(50, 6, number_format($total_transactions, 0), 1, 1, 'R', true);

$pdf->Cell(120, 6, $pdf->EncodeText('  Camarones Vendidos (unidades)'), 1, 0, 'L', true);
$pdf->Cell(50, 6, number_format($total_quantity_sold, 0), 1, 1, 'R', true);

if ($total_weight_sold > 0) {
    $pdf->Cell(120, 6, $pdf->EncodeText('  Peso Total Vendido (kg)'), 1, 0, 'L', true);
    $pdf->Cell(50, 6, number_format($total_weight_sold, 2), 1, 1, 'R', true);
}

$pdf->Ln(2);

// Price metrics section
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(255, 248, 240);
$pdf->Cell(120, 8, $pdf->EncodeText('INDICADORES DE PRECIOS'), 1, 0, 'L', true);
$pdf->Cell(50, 8, '', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);

if ($total_quantity_sold > 0) {
    $average_price_per_shrimp = $total_revenue / $total_quantity_sold;
    $pdf->Cell(120, 6, $pdf->EncodeText('  Precio Promedio por Camarón'), 1, 0, 'L', true);
    $pdf->Cell(50, 6, '$' . number_format($average_price_per_shrimp, 2), 1, 1, 'R', true);
}

if ($total_weight_sold > 0) {
    $average_price_per_kg = $total_revenue / $total_weight_sold;
    $pdf->Cell(120, 6, $pdf->EncodeText('  Precio Promedio por Kilogramo'), 1, 0, 'L', true);
    $pdf->Cell(50, 6, '$' . number_format($average_price_per_kg, 2), 1, 1, 'R', true);
}

if ($total_transactions > 0) {
    $average_per_transaction = $total_revenue / $total_transactions;
    $pdf->Cell(120, 6, $pdf->EncodeText('  Promedio por Transacción'), 1, 0, 'L', true);
    $pdf->Cell(50, 6, '$' . number_format($average_per_transaction, 2), 1, 1, 'R', true);
}

$pdf->Ln(8);

// Performance Analysis
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(250, 250, 250);
$pdf->Cell(0, 8, $pdf->EncodeText('Análisis de Rendimiento'), 0, 1, 'C', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 9);
if ($gross_profit > 0) {
    $pdf->SetTextColor(0, 128, 0);  // Green text
    $pdf->Cell(0, 6, $pdf->EncodeText('✓ El estanque está generando ganancias brutas positivas'), 0, 1, 'L');
    $pdf->Cell(0, 6, $pdf->EncodeText('✓ Los ingresos por ventas superan los costos operativos'), 0, 1, 'L');
    
    if ($profit_margin > 20) {
        $pdf->Cell(0, 6, $pdf->EncodeText('✓ Excelente margen de ganancia (' . number_format($profit_margin, 1) . '%)'), 0, 1, 'L');
    } elseif ($profit_margin > 10) {
        $pdf->Cell(0, 6, $pdf->EncodeText('✓ Buen margen de ganancia (' . number_format($profit_margin, 1) . '%)'), 0, 1, 'L');
    } else {
        $pdf->Cell(0, 6, $pdf->EncodeText('⚠ Margen de ganancia bajo (' . number_format($profit_margin, 1) . '%) - Considerar optimización'), 0, 1, 'L');
    }
} elseif ($gross_profit == 0) {
    $pdf->SetTextColor(255, 140, 0);  // Orange text
    $pdf->Cell(0, 6, $pdf->EncodeText('⚠ El estanque está en punto de equilibrio'), 0, 1, 'L');
    $pdf->Cell(0, 6, $pdf->EncodeText('⚠ Los ingresos igualan exactamente los costos'), 0, 1, 'L');
} else {
    $pdf->SetTextColor(255, 0, 0);  // Red text
    $pdf->Cell(0, 6, $pdf->EncodeText('✗ El estanque está generando pérdidas'), 0, 1, 'L');
    $pdf->Cell(0, 6, $pdf->EncodeText('✗ Los costos operativos superan los ingresos por ventas'), 0, 1, 'L');
    $pdf->Cell(0, 6, $pdf->EncodeText('✗ Se requiere revisión urgente de costos y estrategia de ventas'), 0, 1, 'L');
}

$pdf->SetTextColor(0, 0, 0);  // Reset to black
$pdf->Ln(8);

// Close database connection
mysqli_close($conn);

// Generate filename
$filename = 'Reporte_Camarones_' . $tagid . '_' . date('Y-m-d_H-i-s') . '.pdf';
$filepath = $reportsDir . '/' . $filename;

// Output PDF
try {
    $pdf->Output('F', $filepath);
    
    // Check if this is a request from the chat system
    $isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $isChatRequest = isset($_GET['for_chat']) && $_GET['for_chat'] == '1';
    $uploadToChatPDF = isset($_GET['upload_to_chatpdf']) && $_GET['upload_to_chatpdf'] == '1';
    
    if ($uploadToChatPDF) {
        // Upload to ChatPDF and return JSON response
        header('Content-Type: application/json');
        
        $response = array(
            'success' => false,
            'filename' => $filename,
            'error' => null,
            'upload_result' => null
        );
        
        try {
            // Check PDF file size (ChatPDF has a 32MB limit for free tier)
            $fileSize = filesize($filepath);
            $maxSize = 32 * 1024 * 1024; // 32MB in bytes
            
            error_log("PDF File Size: " . number_format($fileSize / 1024 / 1024, 2) . " MB");
            
            if ($fileSize > $maxSize) {
                throw new Exception('PDF file too large (' . number_format($fileSize / 1024 / 1024, 2) . ' MB). ChatPDF limit is 32MB.');
            }
            
            if ($fileSize === 0) {
                throw new Exception('PDF file is empty or corrupted');
            }
            
            // Upload the PDF to ChatPDF
            $ch = curl_init('https://api.chatpdf.com/v1/sources/add-file');
            
            $cfile = new CURLFile($filepath, 'application/pdf', $filename);
            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => $cfile));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'x-api-key: sec_AdQUXMlHjjhyrwud6dGCP9DFtUt8ZS7T'
            ));
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 second timeout
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // 30 second connect timeout
            
            $uploadResult = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                throw new Exception('CURL Error: ' . $curlError);
            }
            
            $uploadData = json_decode($uploadResult, true);
            
            // Log the API response for debugging
            error_log('ChatPDF API Response - HTTP Code: ' . $httpCode);
            error_log('ChatPDF API Response - Data: ' . print_r($uploadData, true));
            
            if ($httpCode == 200 && isset($uploadData['sourceId'])) {
                $response['success'] = true;
                $response['upload_result'] = array(
                    'success' => true,
                    'sourceId' => $uploadData['sourceId']
                );
            } else {
                $errorMessage = 'ChatPDF API Error (HTTP ' . $httpCode . '): ';
                
                // Parse different error types
                if ($httpCode == 500) {
                    $errorMessage .= 'Internal Server Error. ';
                    $errorMessage .= 'This usually means: ';
                    $errorMessage .= '(1) ChatPDF API is temporarily down, ';
                    $errorMessage .= '(2) API key is invalid, or ';
                    $errorMessage .= '(3) PDF format is not supported. ';
                    $errorMessage .= 'Please try again later or use "Ver PDF" to download the report.';
                } elseif ($httpCode == 401) {
                    $errorMessage .= 'Unauthorized - API key is invalid or expired';
                } elseif ($httpCode == 413) {
                    $errorMessage .= 'File too large - Maximum size is 32MB';
                } elseif ($httpCode == 429) {
                    $errorMessage .= 'Rate limit exceeded - Too many requests';
                } elseif (isset($uploadData['error'])) {
                    $errorMessage .= $uploadData['error'];
                } elseif (isset($uploadData['message'])) {
                    $errorMessage .= $uploadData['message'];
                } else {
                    $errorMessage .= 'Response: ' . substr($uploadResult, 0, 300);
                }
                
                throw new Exception($errorMessage);
            }
            
        } catch (Exception $uploadError) {
            $response['success'] = true; // PDF was generated successfully
            $response['upload_result'] = array(
                'success' => false,
                'error' => $uploadError->getMessage()
            );
        }
        
        echo json_encode($response);
        exit;
        
    } elseif ($isAjaxRequest || $isChatRequest) {
        // For chat requests, just redirect to the file location
        // Don't delete the file so the chat system can access it
        header('Location: ?file=' . urlencode($filename));
        exit;
    } else {
        // For direct access, provide the PDF download and clean up
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output file and clean up
        readfile($filepath);
        unlink($filepath); // Delete temporary file
    }
    
} catch (Exception $e) {
    error_log('PDF generation error: ' . $e->getMessage());
    
    // If this is an upload_to_chatpdf request, return JSON error
    if (isset($_GET['upload_to_chatpdf']) && $_GET['upload_to_chatpdf'] == '1') {
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'error' => 'Failed to generate PDF report: ' . $e->getMessage()
        ));
        exit;
    }
    
    die('Error: Failed to generate PDF report. Please try again.');
}

exit;
