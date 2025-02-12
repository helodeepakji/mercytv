<?php
include __DIR__ . '/../settings/database/conn.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'createAccount') {
    if ($_POST['phone'] != '' || $_POST['email'] != '') {

        if($_POST['phone'] != ''){
            $sql = $conn->prepare("INSERT INTO `users`(`phone`, `is_active`, `is_verify_number`) VALUES (? ,? , ? )");
            $result = $sql->execute([$_POST['phone'] , 1 , 1]);
            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Account is successfull created."]);
                exit;
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Something went wrong."]);
                exit;
            }
        }

        if($_POST['email'] != ''){
            $sql = $conn->prepare("INSERT INTO `users`(`email`, `is_active`, `is_verify_email`) VALUES (? ,? , ?)");
            $result = $sql->execute([$_POST['email'] , 1 , 1]);
            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Account is successfull created."]);
                exit;
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Something went wrong."]);
                exit;
            }
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Phone no or Email Id is required to login."]);
        exit;
    }
}
