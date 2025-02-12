<?php
include __DIR__ . '/../settings/database/conn.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");


if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['type'] == 'getTodayProgram') {

    $query = $conn->prepare("SELECT * FROM `program` WHERE `date` = CURDATE() ORDER BY `time` ASC");
    $query->execute();
    $programs = $query->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($programs);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['type'] == 'getUpComingProgram') {

    $conn->exec("SET time_zone = '+05:30'");
    $query = $conn->prepare("SELECT * FROM `program` WHERE `date` = CURDATE() AND `time` >= DATE_FORMAT(NOW(), '%H:%i:%s') ORDER BY `time` ASC");
    $query->execute();
    $programs = $query->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($programs);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['type'] == 'currentProgram') {
    
    $conn->exec("SET time_zone = '+05:30'");

    // Fetch the latest program for the current date
    $query = $conn->prepare("SELECT * FROM `program` WHERE `date` = CURDATE() AND `time` <= DATE_FORMAT(NOW(), '%H:%i:%s') ORDER BY `time` DESC LIMIT 1");
    $query->execute();
    $program = $query->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        http_response_code(200);
        echo json_encode($program);
    } else {
        $query2 = $conn->prepare("SELECT `date` FROM `program` ORDER BY `date` DESC");
        $query2->execute();
        $lastDateData = $query2->fetch(PDO::FETCH_ASSOC);

        if ($lastDateData) {
            $lastDate = $lastDateData['date'];

            $query3 = $conn->prepare("SELECT * FROM `program` WHERE `date` = ? AND `time` <= DATE_FORMAT(NOW(), '%H:%i:%s') ORDER BY `time` DESC LIMIT 1");
            $query3->execute([$lastDate]);
            $program = $query3->fetch(PDO::FETCH_ASSOC);

            if ($program) {
                http_response_code(200);
                echo json_encode($program);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "No programs found for the last available date."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No program data available."]);
        }
    }
}

