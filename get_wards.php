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

if (isset($_GET['lga_id'])) {
    $lga_id = $_GET['lga_id'];

    // Fetch wards for the selected LGA
    $stmt = $pdo->prepare("SELECT ward_id, ward_name FROM ward WHERE lga_id = :lga_id");
    $stmt->bindParam(':lga_id', $lga_id, PDO::PARAM_INT);
    $stmt->execute();
    $wards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return wards as JSON
    header('Content-Type: application/json');
    echo json_encode($wards);
}
?>
