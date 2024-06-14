<?php

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

if (isset($_GET['lga_id'])) {
    $lga_id = $_GET['lga_id'];

    
    $stmt = $pdo->prepare("
        SELECT lga_id, lga_name 
        FROM lga 
        WHERE lga_id = :lga_id
    ");
    $stmt->execute(['lga_id' => $lga_id]);
    $lga = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lga) {
        
        $results_stmt = $pdo->prepare("
            SELECT party_abbreviation, SUM(party_score) as total_score
            FROM announced_pu_results apr
            JOIN polling_unit pu ON apr.polling_unit_uniqueid = pu.uniqueid
            WHERE pu.lga_id = :lga_id
            GROUP BY party_abbreviation
        ");
        $results_stmt->execute(['lga_id' => $lga_id]);
        $results = $results_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "No LGA found with the ID: $lga_id";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGA Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
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
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>LGA Results</h2>
        <?php if (isset($error_message)): ?>
            <p><?php echo $error_message; ?></p>
        <?php elseif (isset($lga)): ?>
            <h3>LGA Details</h3>
            <p><strong>Name:</strong> <?php echo $lga['lga_name']; ?></p>

            <h3>Summed Results</h3>
            <?php if ($results): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Party</th>
                            <th>Total Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?php echo $result['party_abbreviation']; ?></td>
                                <td><?php echo $result['total_score']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No results found for this LGA.</p>
            <?php endif; ?>
        <?php endif; ?>
        <a class="back-link" href="index.php">Back to selection</a>
    </div>
</body>
</html>
