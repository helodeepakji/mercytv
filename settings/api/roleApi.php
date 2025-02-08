<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
header("content-Type: application/json");
session_start();
$user_id = $_SESSION['userId'];
$role_id = $_SESSION['roleId'];


if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'deleteRole')) {

    if (($_GET['id'] != '')) {

        $sql = $conn->prepare("SELECT COUNT(`id`) as user FROM `users` WHERE role_id = ?  AND `is_terminated` = 0");
        $sql->execute([$_GET['id']]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);
        if($user['user'] > 0){
            http_response_code(500);
            echo json_encode(array("message" => 'Users is exist of this role', "status" => 500));
            exit;
        }

        $sql = $conn->prepare('DELETE FROM `role` WHERE `id` = ?');
        $result = $sql->execute([$_GET['id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'Delete Role Successfull', "status" => 200, "time" => $_POST['time']));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went worrg', "status" => 500));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 404));
    }
}



if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'updateRole')) {
    if ($_POST['role_id'] != '' && $_POST['role_name'] != '') {
        $check = $conn->prepare("SELECT * FROM `role` WHERE `id` = ?");
        $check->execute([$_POST['role_id']]);
        $user = $check->fetch(PDO::FETCH_ASSOC);
        if ($user) {

            $sql = $conn->prepare("UPDATE `role` SET `role` =  ? WHERE id = ?");
            $result = $sql->execute([$_POST['role_name'], $_POST['role_id']]);
            if ($result) {
                http_response_code(200);
                echo json_encode(array('message' => 'Update Role successfully', 'status' => 500));
                exit;
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Something Went Wrong.', "status" => 400]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Role Not Found.', "status" => 400]);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Fill All Required Fields.', "status" => 400]);
        exit;
    }
}




if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addRole')) {
    if ($_POST['role_name'] != '') {
        $check = $conn->prepare("SELECT * FROM `role` WHERE `role` = ?");
        $check->execute([$_POST['role_name']]);
        $user = $check->fetch(PDO::FETCH_ASSOC);
        if (!$user) {

            $sql = $conn->prepare("INSERT INTO `role` (`role`) VALUES (?)");
            $result = $sql->execute([$_POST['role_name']]);
            if ($result) {
                http_response_code(200);
                echo json_encode(array('message' => 'Add Role successfully', 'status' => 500));
                exit;
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Something Went Wrong.', "status" => 400]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Role Not Found.', "status" => 400]);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Fill All Required Fields.', "status" => 400]);
        exit;
    }
}
