<?php
include __DIR__ . '/../database/conn.php';
session_start();

function validateAndFormatDate($date) {
    $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d'];
    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $d->format('Y-m-d'); // Return in standard format
        }
    }
    return false; // Invalid date
}

if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['type'] == "addFamily")) {
    $user_id = $_POST['user_id'];

    // Delete existing family records for the user
    $delete = $conn->prepare('DELETE FROM `family` WHERE `user_id` = ?');
    $delete->execute([$user_id]);

    $names = $_POST['name'];
    $relationships = $_POST['relationship'];
    $phones = $_POST['phone'];
    $dobs = $_POST['dob']; // Assuming `dob` is date of birth

    for ($i = 0; $i < count($names); $i++) {
        $name = $names[$i];
        $relationship = $relationships[$i];
        $phone = $phones[$i];
        $dob = validateAndFormatDate($dobs[$i]);

        // Insert into the family table
        $stmt = $conn->prepare("INSERT INTO `family`(`user_id`, `name`, `phone`, `relation`, `dob`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, $name, $phone, $relationship, $dob]);
    }

    // Respond back if successful
    echo json_encode(['success' => true]);
}


if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['type'] == "getFamily")) {
    if($_GET['user_id'] == ''){
        http_response_code(400);
            echo json_encode(['message' => "The field id is required.", "status" => 400]);
            exit;
    }
    $check = $conn->prepare("SELECT * FROM `family` WHERE `user_id` = ?");
    $check->execute([$_GET['user_id']]);
    $result = $check->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}