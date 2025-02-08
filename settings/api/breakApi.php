<?php

session_start();
include __DIR__ . '/../database/conn.php';
header("content-Type: application/json");
$user_id = $_SESSION['userId'];


if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addBreak')) {

    if (($_POST['break_type'] != '') && ($_POST['project_id'] != '') && ($_POST['task_id'] != '') && ($_POST['time'] != '')) {

        $sql = $conn->prepare('INSERT INTO `break`(`user_id`,`task_id`, `project_id`, `break_type`, `other`, `who`, `why`, `time`, `remarks`) VALUES ( ? , ? ,? , ? , ? , ? ,? , ? , ? )');
        $result = $sql->execute([$user_id, $_POST['task_id'], $_POST['project_id'], $_POST['break_type'], $_POST['other'], $_POST['who'], $_POST['why'], $_POST['time'], $_POST['remarks']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'Add Break Successfull', "status" => 200, "time" => $_POST['time']));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went worrg', "status" => 500));
        }

    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'Fill All Required Fields', "status" => 404));
    }
}

?>