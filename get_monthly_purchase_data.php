<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL query to get monthly purchase data
    // Note: monto_compra is the total amount paid for each tagid (poblacion group)
    $query = "SELECT 
                DATE_FORMAT(fecha_compra, '%Y-%m') as month,
                SUM(monto_compra) as total_purchase
              FROM camarones 
              WHERE fecha_compra IS NOT NULL 
                AND fecha_compra != '0000-00-00' 
                AND fecha_compra != ''
                AND monto_compra IS NOT NULL
                AND monto_compra > 0
              GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m')
              ORDER BY month ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process the results to ensure proper data types
    $monthlyData = [];
    foreach ($results as $row) {
        $monthlyData[] = [
            'month' => $row['month'],
            'total_purchase' => (float)$row['total_purchase']
        ];
    }
    
    // Return the data as JSON
    echo json_encode($monthlyData);
    
} catch (PDOException $e) {
    // Log the error
    error_log("Error in get_monthly_purchase_data.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Log the error
    error_log("General error in get_monthly_purchase_data.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}