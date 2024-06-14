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


$stmt = $pdo->prepare("SELECT DISTINCT party_abbreviation FROM announced_pu_results");
$stmt->execute();
$parties = $stmt->fetchAll(PDO::FETCH_ASSOC);


$lgaStmt = $pdo->prepare("SELECT lga_id, lga_name FROM lga WHERE state_id = 25");
$lgaStmt->execute();
$lgas = $lgaStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Polling Unit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #218838;
        }
        .party-score-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .party-score-group label {
            flex: 1;
        }
        .party-score-group input {
            flex: 2;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Create New Polling Unit</h2>
    <form id="newPollingUnitForm">
        <div class="form-group">
            <label for="polling_unit_id">Polling Unit ID</label>
            <input type="text" id="polling_unit_id" name="polling_unit_id" required>
        </div>
        <div class="form-group">
            <label for="polling_unit_name">Polling Unit Name</label>
            <input type="text" id="polling_unit_name" name="polling_unit_name" required>
        </div>
        <div class="form-group">
            <label for="lga_id">Local Government Area</label>
            <select id="lga_id" name="lga_id" required>
                <option value="">Select LGA</option>
                <?php
                foreach ($lgas as $lga) {
                    echo "<option value='{$lga['lga_id']}'>{$lga['lga_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="ward_id">Ward</label>
            <select id="ward_id" name="ward_id" required>
                <option value="">Select Ward</option>
                <!-- Wards will be populated based on selected LGA -->
            </select>
        </div>
        <div id="partyScoresContainer">
            <h3>Enter Party Scores</h3>
            <?php foreach ($parties as $party): ?>
                <div class="party-score-group">
                    <label for="party_<?php echo $party['party_abbreviation']; ?>"><?php echo $party['party_abbreviation']; ?></label>
                    <input type="number" id="party_<?php echo $party['party_abbreviation']; ?>" name="scores[<?php echo $party['party_abbreviation']; ?>]" required>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="form-group">
            <button type="submit">Save Results</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('lga_id').addEventListener('change', function() {
        var lga_id = this.value;
        var wardSelect = document.getElementById('ward_id');
        wardSelect.innerHTML = '<option value="">Select Ward</option>';

        if (lga_id) {
            fetch('get_wards.php?lga_id=' + lga_id)
                .then(response => response.json())
                .then(data => {
                    data.forEach(ward => {
                        var option = document.createElement('option');
                        option.value = ward.ward_id;
                        option.textContent = ward.ward_name;
                        wardSelect.appendChild(option);
                    });
                });
        }
    });

    document.getElementById('newPollingUnitForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        fetch('save_polling_unit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                this.reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the results.');
        });
    });
</script>

</body>
</html>
