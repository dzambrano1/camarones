<?php
header('Content-Type: application/json');

// Include database connection details
require_once "./pdo_conexion.php"; // Adjust path if necessary

// Use mysqli for connection as in the previous examples
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

mysqli_set_charset($conn, "utf8");

$data = [];

try {
    // Query to get total monthly weight from ah_peso table
    // Assumes date column is ah_peso_fecha and weight column is ah_peso_animal	
    // Please adjust column names if they are different.
    $sql = "
        SELECT 
            DATE_FORMAT(ah_peso_fecha, '%Y-%m') AS month, 
            SUM(ah_peso_animal	) AS total_weight 
        FROM ah_peso 
        WHERE ah_peso_animal	 > 0 -- Optionally filter out zero or invalid weights
        GROUP BY month 
        ORDER BY month ASC;
    ";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                'month' => $row['month'],
                'total_weight' => (float)$row['total_weight'] // Ensure weight is a float
            ];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error executing weight query: " . mysqli_error($conn));
    }

    echo json_encode($data);

} catch (Exception $e) {
    // Log error if needed
    error_log("Error fetching monthly weight data: " . $e->getMessage());
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
} finally {
    // Close connection
    if (isset($conn)) {
        mysqli_close($conn);
    }
}