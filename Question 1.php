<?php
// Database connection settings
$host = 'localhost';
$dbname = 'bincomphptest';
$username = 'jerry';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

if (isset($_GET['polling_unit_uniqueid'])) {
    $polling_unit_uniqueid = $_GET['polling_unit_uniqueid'];

    // Fetch polling unit details
    $stmt = $pdo->prepare("
        SELECT pu.uniqueid, pu.polling_unit_name, pu.ward_id, w.ward_name, pu.lga_id, l.lga_name 
        FROM polling_unit pu
        JOIN ward w ON pu.ward_id = w.ward_id
        JOIN lga l ON pu.lga_id = l.lga_id
        WHERE pu.uniqueid = :polling_unit_uniqueid
    ");
    $stmt->execute(['polling_unit_uniqueid' => $polling_unit_uniqueid]);
    $polling_unit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($polling_unit) {
        // Fetch results from announced_pu_results
        $results_stmt = $pdo->prepare("
            SELECT party_abbreviation, party_score
            FROM announced_pu_results
            WHERE polling_unit_uniqueid = :polling_unit_uniqueid
        ");
        $results_stmt->execute(['polling_unit_uniqueid' => $polling_unit_uniqueid]);
        $results = $results_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "No polling unit found with the unique ID: $polling_unit_uniqueid";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling Unit Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Polling Unit Results</h2>
        <?php if (isset($error_message)): ?>
            <p><?php echo $error_message; ?></p>
        <?php elseif (isset($polling_unit)): ?>
            <h3>Polling Unit Details</h3>
            <p><strong>Name:</strong> <?php echo $polling_unit['polling_unit_name']; ?></p>
            <p><strong>Ward:</strong> <?php echo $polling_unit['ward_name']; ?></p>
            <p><strong>LGA:</strong> <?php echo $polling_unit['lga_name']; ?></p>

            <h3>Announced Results</h3>
            <?php if ($results): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Party</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?php echo $result['party_abbreviation']; ?></td>
                                <td><?php echo $result['party_score']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No results found for this polling unit.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
