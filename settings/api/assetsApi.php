<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
header("content-Type: application/json");
session_start();
$user_id = $_SESSION['userId'];


if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addAssets')) {
    if (!empty($_POST['name']) && !empty($_POST['status'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("INSERT INTO `assets`(`name`, `status`) VALUES (? , ?)");
            $result = $sql->execute([$_POST['name'] , $_POST['status']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull add assets.', "status" => 200));
            } else {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong.', "status" => 500));
            }
        } catch (Exception $e) {

            $conn->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => 'An error occurred: ' . $e->getMessage(), "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'editAssets')) {
    if (!empty($_POST['name']) && !empty($_POST['id'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("UPDATE `assets` SET `name` = ? , `status` = ? WHERE id = ?");
            $result = $sql->execute([$_POST['name'] , $_POST['status'] , $_POST['id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull update assets.', "status" => 200));
            } else {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong.', "status" => 500));
            }
        } catch (Exception $e) {

            $conn->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => 'An error occurred: ' . $e->getMessage(), "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getAssets')) {
    $sql = $conn->prepare('SELECT * FROM `assets` WHERE `id` = ?');
    $sql->execute([$_GET['id']]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No project found', "status" => 404));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'deleteAssets')) {
    $sql = $conn->prepare('DELETE FROM `assets` WHERE `id` = ?');
    $result = $sql->execute([$_GET['id']]);
    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => 'Delete Assets Succesfull']);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No project found', "status" => 404));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'assignAssets')) {
    if (!empty($_POST['user_id']) && !empty($_POST['asset_id'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("INSERT INTO `asset_assign`(`asset_id`, `user_id`) VALUES (?, ?)");
            $result = $sql->execute([ $_POST['asset_id'] , $_POST['user_id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull Add assets.', "status" => 200));
            } else {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong.', "status" => 500));
            }
        } catch (Exception $e) {

            $conn->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => 'An error occurred: ' . $e->getMessage(), "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'editAssignAssets')) {
    if (!empty($_POST['user_id']) && !empty($_POST['asset_id']) && !empty($_POST['id'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("UPDATE `asset_assign` SET `asset_id` = ? , `user_id` = ? WHERE `id` = ?");
            $result = $sql->execute([ $_POST['asset_id'] , $_POST['user_id'] , $_POST['id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull Add assets.', "status" => 200));
            } else {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong.', "status" => 500));
            }
        } catch (Exception $e) {

            $conn->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => 'An error occurred: ' . $e->getMessage(), "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'getDelete')) {
    if (!empty($_POST['id'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("DELETE FROM `asset_assign` WHERE `id` = ?");
            $result = $sql->execute([$_POST['id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull Delete assets.', "status" => 200));
            } else {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong.', "status" => 500));
            }
        } catch (Exception $e) {

            $conn->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => 'An error occurred: ' . $e->getMessage(), "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getAssignAsset')) {
    $sql = $conn->prepare('SELECT * FROM `asset_assign` WHERE `id` = ?');
    $sql->execute([$_GET['id']]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No Assets found', "status" => 404));
    }
}