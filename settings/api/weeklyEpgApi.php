<?php

include __DIR__ . '/../database/conn.php';
require '../phpOffice/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
session_start();

function isValidFileExtension($fileName, $validExtensions)
{
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    return in_array($ext, $validExtensions);
}

try {
    if (!isset($_FILES['excelFile'])) {
        http_response_code(400);
        echo json_encode(["message" => "No file uploaded."]);
        exit();
    }

    $fileInfo = $_FILES['excelFile'];
    $fileName = $fileInfo['name'];
    $validExtensions = ['xlsx', 'csv'];

    // Validate file extension
    if (!isValidFileExtension($fileName, $validExtensions)) {
        http_response_code(400);
        echo json_encode(["message" => "File must be in .xlsx or .csv format"]);
        exit();
    }

    $tempFile = $_FILES["excelFile"]["tmp_name"];
    $spreadsheet = IOFactory::load($tempFile);

    $conn->beginTransaction();
    
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $worksheet = $spreadsheet->getSheetByName($sheetName);
        $rows = $worksheet->toArray();

        // Skip the first row if it contains headers
        for ($i = 1; $i < count($rows); $i++) {
            $date = date("Y-m-d", strtotime($sheetName));
            $time = $rows[$i][0]; 
            $duration = intval($rows[$i][1]);
            $program = $rows[$i][2];
            $desc = $rows[$i][3] == '' ? "" : $rows[$i][3];

            // Check for duplicate entry
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM `program` WHERE `date` = ? AND `time` = ?");
            $checkStmt->execute([$date, $time]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                http_response_code(500);
                echo json_encode(["message" => "Duplicate entry for Date: $date and Time: $time. Entry already exists."]);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO `program`(`date`, `time`, `duration`, `program`, `desc`, `created_at`, `updated_at`) 
                                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$date, $time, $duration, $program, $desc]);
        }
    }

    $conn->commit();
    echo json_encode(["message" => "Data inserted successfully!"]);

} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(400);
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}

?>
