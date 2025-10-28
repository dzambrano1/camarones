<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Enable error reporting in PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get monthly sales data from cah_ventas table
    // Group by year, month, and tagid, calculate total revenue
    $query = "SELECT 
                YEAR(cah_ventas_fecha) AS year,
                MONTH(cah_ventas_fecha) AS month,
                cah_ventas_tagid AS tagid,
                SUM(cah_ventas_precio * cah_ventas_peso) AS total_revenue,
                COUNT(*) AS total_sales,
                SUM(cah_ventas_cantidad) AS total_quantity
              FROM cah_ventas
              WHERE 
                cah_ventas_fecha IS NOT NULL AND 
                cah_ventas_precio IS NOT NULL AND 
                cah_ventas_peso IS NOT NULL AND
                cah_ventas_cantidad IS NOT NULL
              GROUP BY YEAR(cah_ventas_fecha), MONTH(cah_ventas_fecha), cah_ventas_tagid
              ORDER BY year ASC, month ASC, tagid ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $monthlySalesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the data as JSON
    echo json_encode($monthlySalesData);
    
} catch (PDOException $e) {
    // Return error message
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
    // Log the error
    error_log('Error in get_monthly_sales.php: ' . $e->getMessage());
}