<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
header("content-Type: application/json");
session_start();
$user_id = $_SESSION['userId'];

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addNotification')) {
    if (!empty($_POST['message']) && !empty($_POST['notificationType'])) {
        try {

            $conn->beginTransaction();
            if ($_POST['notificationType'] == 'role') {
                $role_id = $_POST['role_id'];
                foreach ($role_id as $value) {
                    $sql = $conn->prepare("INSERT INTO `notification`(`notification`, `user_id`, `type`, `type_id`) VALUES ( ? , ? , ? , ?)");
                    $result = $sql->execute([$_POST['message'], $user_id, $_POST['notificationType'], $value]);
                }
            } else if ($_POST['notificationType'] == 'user') {
                $userId = $_POST['user_id'];
                foreach ($userId as $value) {
                    $sql = $conn->prepare("INSERT INTO `notification`(`notification`, `user_id`, `type`, `type_id`) VALUES ( ? , ? , ? , ?)");
                    $result = $sql->execute([$_POST['message'], $user_id, $_POST['notificationType'], $value]);
                }
            } else {

                $sql = $conn->prepare("INSERT INTO `notification`(`notification`, `user_id`, `type`) VALUES ( ? , ? , ? )");
                $result = $sql->execute([$_POST['message'], $user_id, $_POST['notificationType']]);
            }

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull add notification.', "status" => 200));
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


if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'deleteNotification')) {
    if (!empty($_POST['id'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("DELETE FROM `notification` WHERE `id` = ?");
            $result = $sql->execute([$_POST['id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull Delete.', "status" => 200));
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


if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'seenNotification')) {
    try {

        $conn->beginTransaction();

        $stmt1 = $conn->prepare("UPDATE notification SET seen = 1 WHERE n.type = 'all'");
        $stmt1->execute();
        $notifications1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Fetch notifications for specific role
        $stmt2 = $conn->prepare("UPDATE notification SET seen = 1 WHERE type = 'role' AND `type_id` = ?");
        $stmt2->execute([$roleId]);

        // Fetch notifications for specific user
        $stmt3 = $conn->prepare("UPDATE notification SET seen = 1 WHERE type = 'user' AND `type_id` = ?");
        $result = $stmt3->execute([$userId]);

        if ($result) {
            $conn->commit();
            http_response_code(200);
            echo json_encode(array("message" => 'Successfull.', "status" => 200));
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
}
