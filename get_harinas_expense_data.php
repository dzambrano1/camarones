<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Enable error reporting in PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to calculate harinas expenses using new column structure
    // Formula: (cah_harinas_fecha_fin - cah_harinas_fecha_inicio) * cah_harinas_racion * cah_harinas_costo
    // Group by cah_harinas_fecha_fin month
    $query = "SELECT 
                DATE_FORMAT(cah_harinas_fecha_fin, '%Y-%m') AS month,
                SUM(
                    DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) * 
                    cah_harinas_racion * 
                    cah_harinas_costo
                ) AS total_expense,
                SUM(
                    DATEDIFF(cah_harinas_fecha_fin, cah_harinas_fecha_inicio) * 
                    cah_harinas_racion
                ) AS total_consumption_kg,
                COUNT(*) AS total_records,
                COUNT(DISTINCT cah_harinas_tagid) AS unique_animals
              FROM cah_harinas
              WHERE 
                cah_harinas_fecha_inicio IS NOT NULL AND 
                cah_harinas_fecha_inicio != '0000-00-00' AND
                cah_harinas_fecha_fin IS NOT NULL AND 
                cah_harinas_fecha_fin != '0000-00-00' AND
                cah_harinas_racion IS NOT NULL AND
                cah_harinas_costo IS NOT NULL AND
                cah_harinas_racion > 0 AND
                cah_harinas_costo > 0 AND
                cah_harinas_fecha_fin >= cah_harinas_fecha_inicio
              GROUP BY DATE_FORMAT(cah_harinas_fecha_fin, '%Y-%m')
              ORDER BY month ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data with proper numeric values
    $formattedData = [];
    foreach ($chartData as $row) {
        $formattedData[] = [
            'month' => $row['month'],
            'total_expense' => round((float)$row['total_expense'], 2),
            'total_consumption_kg' => round((float)$row['total_consumption_kg'], 2),
            'total_records' => (int)$row['total_records'],
            'unique_animals' => (int)$row['unique_animals']
        ];
    }
    
    echo json_encode($formattedData);
    
} catch (PDOException $e) {
    // Return error message
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
    // Log the error
    error_log('Error in get_harinas_expense_data.php: ' . $e->getMessage());
}