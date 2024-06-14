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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $polling_unit_id = $_POST['polling_unit_id'];
    $polling_unit_name = $_POST['polling_unit_name'];
    $ward_id = $_POST['ward_id'];
    $lga_id = $_POST['lga_id'];
    $party_scores = $_POST['scores'];

    // Insert the new polling unit into the polling_unit table
    $insertPollingUnitSQL = "INSERT INTO polling_unit (uniqueid, polling_unit_id, polling_unit_name, ward_id, lga_id)
                             VALUES (NULL, :polling_unit_id, :polling_unit_name, :ward_id, :lga_id)";
    $stmt = $pdo->prepare($insertPollingUnitSQL);
    $stmt->bindParam(':polling_unit_id', $polling_unit_id);
    $stmt->bindParam(':polling_unit_name', $polling_unit_name);
    $stmt->bindParam(':ward_id', $ward_id);
    $stmt->bindParam(':lga_id', $lga_id);
    
    if ($stmt->execute()) {
        // Get the uniqueid of the newly inserted polling unit
        $polling_unit_uniqueid = $pdo->lastInsertId();

        // Insert the party scores into the announced_pu_results table
        $insertResultsSQL = "INSERT INTO announced_pu_results (polling_unit_uniqueid, party_abbreviation, party_score)
                             VALUES (:polling_unit_uniqueid, :party_abbreviation, :party_score)";
        $stmt = $pdo->prepare($insertResultsSQL);
        
        foreach ($party_scores as $party_abbreviation => $party_score) {
            $stmt->bindParam(':polling_unit_uniqueid', $polling_unit_uniqueid);
            $stmt->bindParam(':party_abbreviation', $party_abbreviation);
            $stmt->bindParam(':party_score', $party_score);
            $stmt->execute();
        }

        $response = [
            'status' => 'success',
            'message' => 'Polling unit and results successfully saved.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Failed to save polling unit.'
        ];
    }

    // Return response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
