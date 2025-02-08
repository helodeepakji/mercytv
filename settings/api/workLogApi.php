<?php
include __DIR__ . '/../database/conn.php';
session_start();
$user_id = $_SESSION['userId'];
header("Access-Control-Allow-Origin: *");

// for multi files

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'startTask')) {

    $pending = $conn->prepare("SELECT `id` FROM `assign` WHERE `status` = ? AND `user_id` = ?");
    $pending->execute(['working', $user_id]);
    $pending = $pending->fetch(PDO::FETCH_ASSOC);

    if ($pending) {
        http_response_code(500);
        echo json_encode(array("message" => 'First Pause or Complete working Task.', "status" => 500));
        exit;
    }

    if (!empty($_POST['tasks'])) {
        try {

            $conn->beginTransaction();

            $tasks = $_POST['tasks'];
            foreach ($tasks as $assign_id) {
                $assigncheck = $conn->prepare("SELECT `id` , `task_id` , `project_id` , `role` FROM `assign` WHERE `user_id` = ? AND id = ?");
                $assigncheck->execute([$user_id, $assign_id]);
                $assigncheck = $assigncheck->fetch(PDO::FETCH_ASSOC);

                if (!$assigncheck) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Tasks is not assigned you, please check.', "status" => 500));
                    exit;
                }

                $sql = $conn->prepare("SELECT `status` , `project_id` FROM tasks WHERE `id` = ? AND (`status` = 'assign_pro' OR `status` = 'assign_qa' OR `status` = 'assign_qc')");
                $sql->execute(params: [$assigncheck['task_id']]);
                $sql = $sql->fetch(PDO::FETCH_ASSOC);

                if (!$sql) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Some tasks are already assigned, please check.', "status" => 500));
                    exit;
                }

                switch ($sql['status']) {
                    case 'assign_pro':
                        $role = 'pro';
                        $next_status = 'pro_in_progress';
                        $prev_status = 'assign_pro';
                        break;
                    case 'assign_qc':
                        $role = 'qc';
                        $next_status = 'qc_in_progress';
                        $prev_status = 'assign_qc';
                        break;
                    case 'assign_qa':
                        $role = 'qa';
                        $next_status = 'qa_in_progress';
                        $prev_status = 'assign_qa';
                        break;
                }


                $assign = $conn->prepare("UPDATE `assign` SET `status` = 'working' WHERE `id` = ? ");
                $assign = $assign->execute([$assign_id]);
                if (!$assign) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Tasks is not assigned you, please check..', "status" => 500));
                    exit;
                }

                $update = $conn->prepare("UPDATE `tasks` SET `status` = ? WHERE `id` = ?");
                $update->execute([$next_status, $assigncheck['task_id']]);

                $work = $conn->prepare("INSERT INTO `work_log`( `user_id`, `task_id`, `project_id`, `prev_status`, `next_status`) VALUES (? , ? , ? , ? , ?)");
                $work->execute([$user_id, $assigncheck['task_id'], $sql['project_id'], $prev_status, $next_status]);
            }

            $conn->commit();

            http_response_code(200);
            echo json_encode(array("message" => 'Tasks assigned successfully.', "status" => 200));
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

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'completeTask')) {

    $pending = $conn->prepare("SELECT `id` FROM `assign` WHERE `status` = ? AND `user_id` = ?");
    $pending->execute(['working', $user_id]);
    $pending = $pending->fetchAll(PDO::FETCH_ASSOC);

    if (!$pending) {
        http_response_code(500);
        echo json_encode(array("message" => 'First Start Task.', "status" => 500));
        exit;
    }

    if (!empty($_POST['tasks'])) {

        $tasks = $_POST['tasks'];
        $total_files = sizeof($tasks);

        if (sizeof($tasks) != sizeof($pending)) {
            http_response_code(500);
            echo json_encode(array("message" => 'All Start Files Complete at same time.', "status" => 500));
            exit;
        }

        try {
            $conn->beginTransaction();

            foreach ($tasks as $assign_id) {
                $assigncheck = $conn->prepare("SELECT `id` , `task_id` , `project_id` , `role`, `updated_at` FROM `assign` WHERE `user_id` = ? AND id = ?");
                $assigncheck->execute([$user_id, $assign_id]);
                $assigncheck = $assigncheck->fetch(PDO::FETCH_ASSOC);

                if (!$assigncheck) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Tasks is not assigned you, please check.', "status" => 500));
                    exit;
                }

                $sql = $conn->prepare("SELECT `status` , `project_id`, `estimated_hour` FROM tasks WHERE `id` = ? AND (`status` = 'pro_in_progress' OR `status` = 'qc_in_progress' OR `status` = 'qa_in_progress')");
                $sql->execute( [$assigncheck['task_id']]);
                $sql = $sql->fetch(PDO::FETCH_ASSOC);

                if (!$sql) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Some tasks are already assigned, please check.', "status" => 500));
                    exit;
                }

                switch ($sql['status']) {
                    case 'pro_in_progress':
                        $role = 'pro';
                        $next_status = 'ready_for_qc';
                        $prev_status = 'pro_in_progress';
                        break;
                    case 'qc_in_progress':
                        $role = 'qc';
                        $next_status = 'ready_for_qa';
                        $prev_status = 'qc_in_progress';
                        break;
                    case 'qa_in_progress':
                        $role = 'qa';
                        $next_status = 'complete';
                        $prev_status = 'qa_in_progress';
                        break;
                }


                $assign = $conn->prepare("UPDATE `assign` SET `status` = 'complete' WHERE `id` = ? ");
                $assign = $assign->execute([$assign_id]);
                if (!$assign) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Tasks is not assigned you, please check..', "status" => 500));
                    exit;
                }

                $check = $conn->prepare("SELECT * FROM `project_time` WHERE `project_id` = ?");
                $check->execute( [$sql['project_id']]);
                $check = $check->fetch(PDO::FETCH_ASSOC);
                if (!$check) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Time percentage is not assign, please check.', "status" => 500));
                    exit;
                }

                $update = $conn->prepare("UPDATE `tasks` SET `status` = ? WHERE `id` = ?");
                $update->execute([$next_status, $assigncheck['task_id']]);

                $total_time = $sql['estimated_hour'] * 60;

                $updatedAt = $assigncheck['updated_at'];

                // calcutate time diff
                $currentTime = new DateTime();
                $updatedAtTime = new DateTime($updatedAt);
                $timeDifference = $currentTime->diff($updatedAtTime);
                $minutesDifference = ($timeDifference->days * 24 * 60) + ($timeDifference->h * 60) +    $timeDifference->i;
                // end time diff

                $taken_time = $minutesDifference / $total_files;

                $total_time = $total_time * ($check[$role]/100);
                $eff = ($total_time / $taken_time) * 100;

                $work = $conn->prepare("INSERT INTO `work_log`( `user_id`, `task_id`, `project_id`, `work_percentage`, `taken_time`, `prev_status`, `next_status`) VALUES (? , ? , ? , ? , ? ,? , ?)");
                $work->execute([$user_id, $assigncheck['task_id'], $sql['project_id'], 100, $taken_time, $prev_status, $next_status]);

                $efficincy = $conn->prepare("INSERT INTO `efficiency`(`user_id`, `task_id`, `project_id`, `profile`, `efficiency`, `total_time`, `taken_time`, `created_at`) VALUES (? ,? ,? , ? , ? ,? ,? ,? )");

                $efficincy->execute([$user_id, $assigncheck['task_id'], $sql['project_id'], $role, $eff, $total_time, $taken_time, $assigncheck['updated_at']]);
            }

            $conn->commit();

            http_response_code(200);
            echo json_encode(array("message" => 'Tasks assigned successfully.', "status" => 200));
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


// end multi files