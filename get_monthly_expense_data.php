<?php
header('Content-Type: application/json');

// Include database connection details
require_once "./pdo_conexion.php"; // Adjust path if necessary

// Use mysqli for connection as in the original file provided
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

mysqli_set_charset($conn, "utf8");

// --- Calculation Logic ---
$monthly_totals = [];
$animal_data = [];
$min_date = null;
$today = new DateTime(); // Use DateTime for easier date manipulation

try {
    // 1. Fetch all relevant concentrado records, ordered correctly
    $sql = "
        SELECT
            ah_concentrado_tagid,
            ah_concentrado_fecha,
            ah_concentrado_racion,
            ah_concentrado_costo
        FROM ah_concentrado
        WHERE ah_concentrado_racion > 0 AND ah_concentrado_costo > 0 -- Consider only valid cost entries
        ORDER BY ah_concentrado_tagid, ah_concentrado_fecha ASC;
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception("Error fetching concentrado data: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        // 2. Process data and group by animal (tagid)
        while ($row = mysqli_fetch_assoc($result)) {
            $tagid = $row['ah_concentrado_tagid'];
            $fecha = $row['ah_concentrado_fecha'];
            $daily_cost = (float)$row['ah_concentrado_racion'] * (float)$row['ah_concentrado_costo'];

            if (!isset($animal_data[$tagid])) {
                $animal_data[$tagid] = [];
            }
            $animal_data[$tagid][] = ['date' => $fecha, 'daily_cost' => $daily_cost];

            // Track the earliest date overall
            if ($min_date === null || $fecha < $min_date) {
                $min_date = $fecha;
            }
        }
        mysqli_free_result($result);

        if ($min_date !== null) {
            $start_date = new DateTime($min_date);
            $end_date = $today;
            $interval = new DateInterval('P1D'); // 1 Day interval
            $period = new DatePeriod($start_date, $interval, $end_date->modify('+1 day')); // Include today

            // 3. Iterate through each day from the earliest record until today
            foreach ($period as $current_date) {
                $daily_total_expense = 0;
                $current_date_str = $current_date->format('Y-m-d');

                // 4. For each animal, find the applicable daily cost for the current day
                foreach ($animal_data as $tagid => $records) {
                    $applicable_cost = 0;
                    $last_record_date = null;

                    // Find the most recent record on or before the current date
                    foreach ($records as $record) {
                        if ($record['date'] <= $current_date_str) {
                             if ($last_record_date === null || $record['date'] > $last_record_date) {
                                $applicable_cost = $record['daily_cost'];
                                $last_record_date = $record['date'];
                             }
                        } else {
                            // Since records are sorted by date, we can break early
                            break;
                        }
                    }
                     // Add the cost for this animal if a valid record was found for this day or earlier
                     if ($last_record_date !== null) {
                          $daily_total_expense += $applicable_cost;
                     }

                } // end loop through animals

                // 5. Aggregate the total daily expense into the correct month
                $month_key = $current_date->format('Y-m');
                if (!isset($monthly_totals[$month_key])) {
                    $monthly_totals[$month_key] = 0;
                }
                $monthly_totals[$month_key] += $daily_total_expense;

            } // end loop through days
        }
    } else {
         // No data found, return empty array or specific message
         echo json_encode([]);
         mysqli_close($conn);
         exit();
    }

    // 6. Format the data for Chart.js
    $chart_data = [];
    // Ensure months are sorted chronologically
    ksort($monthly_totals);
    foreach ($monthly_totals as $month => $total) {
        $chart_data[] = [
            'month' => $month,
            'total_expense' => round($total, 2) // Round to 2 decimal places
        ];
    }

    // 7. Output JSON
    echo json_encode($chart_data);

} catch (Exception $e) {
    error_log("Error calculating monthly expense data: " . $e->getMessage());
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}