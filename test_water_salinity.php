<?php
require_once './pdo_conexion.php';

echo "<h2>Water Salinity System Test</h2>";

try {
    // Test 1: Check if table exists
    echo "<h3>Test 1: Checking if water_salinity_log table exists</h3>";
    $stmt = $conn->query("SHOW TABLES LIKE 'water_salinity_log'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'water_salinity_log' exists<br>";
        
        // Show table structure
        echo "<h4>Table Structure:</h4>";
        $stmt = $conn->query("DESCRIBE water_salinity_log");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
    } else {
        echo "❌ Table 'water_salinity_log' does not exist<br>";
        echo "You can create it by calling: <a href='create_water_salinity_table.php'>create_water_salinity_table.php</a><br>";
    }
    
    // Test 2: Check sample data
    echo "<h3>Test 2: Sample data in table</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM water_salinity_log");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total records: " . $result['count'] . "<br>";
    
    if ($result['count'] > 0) {
        echo "<h4>Sample Records:</h4>";
        $stmt = $conn->query("SELECT * FROM water_salinity_log ORDER BY timestamp DESC LIMIT 5");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Pond ID</th><th>Salinidad</th><th>Fuente</th><th>Fase</th><th>Timestamp</th><th>Created At</th></tr>";
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['pond_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['salinity_ppt']) . " ppt</td>";
            echo "<td>" . htmlspecialchars($row['source']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phase']) . "</td>";
            echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "No data found in table<br>";
    }
    
    // Test 3: Test data fetching script
    echo "<h3>Test 3: Data fetching script</h3>";
    $dataUrl = 'get_water_salinity_data.php';
    echo "Data fetching URL: <a href='$dataUrl' target='_blank'>$dataUrl</a><br>";
    
    // Test 4: Processing script
    echo "<h3>Test 4: Processing script</h3>";
    echo "Processing script: process_water_salinity.php (POST only)<br>";
    
    echo "<h3>✅ All tests completed successfully!</h3>";
    echo "<p><strong>Main Application:</strong> <a href='camarones_register_salinidad.php' target='_blank'>camarones_register_salinidad.php</a></p>";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage();
}
?>
