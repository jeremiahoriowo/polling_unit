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

// Fetch all polling units
$stmt = $pdo->prepare("SELECT uniqueid, polling_unit_name FROM polling_unit");
$stmt->execute();
$polling_units = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Polling Unit</title>
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
        form {
            text-align: center;
            margin-top: 20px;
        }
        select {
            padding: 10px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Polling Unit</h2>
        <form action="Question 1.php" method="GET">
            <select name="polling_unit_uniqueid">
                <?php foreach ($polling_units as $unit): ?>
                    <option value="<?php echo $unit['uniqueid']; ?>"><?php echo $unit['polling_unit_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit">Get Results</button>
        </form>
    </div>
</body>
</html>
