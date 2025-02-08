<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
session_start();
$user_id = $_SESSION['userId'];

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'assignTask')) {
    if (!empty($_POST['project_id']) && !empty($_POST['user_id']) && !empty($_POST['tasks'])) {
        try {
            
            $conn->beginTransaction();

            $tasks = $_POST['tasks'];
            foreach ($tasks as $task) {
                $sql = $conn->prepare("SELECT `status` FROM tasks WHERE `id` = ? AND (`status` = 'pending' OR `status` = 'ready_for_qc' OR `status` = 'ready_for_qa')");
                $sql->execute([$task]);
                $sql = $sql->fetch(PDO::FETCH_ASSOC);

                if (!$sql) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Some tasks are already assigned, please check.', "status" => 600));
                    exit;
                }

                switch ($sql['status']) {
                    case 'pending':
                        $role = 'pro';
                        $next_status = 'assign_pro';
                        break;
                    case 'ready_for_qc':
                        $role = 'qc';
                        $next_status = 'assign_qc';
                        break;
                    case 'ready_for_qa':
                        $role = 'qa';
                        $next_status = 'assign_qa';
                        break;
                }

                $assign = $conn->prepare("INSERT INTO `assign`(`user_id`, `project_id`, `task_id`, `role`, `assigned_by`) VALUES (?, ?, ?, ?, ?)");
                $assign->execute([$_POST['user_id'], $_POST['project_id'], $task, $role, $user_id]);

                $update = $conn->prepare("UPDATE `tasks` SET `status` = ? WHERE `id` = ?");
                $update->execute([$next_status, $task]);
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

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'reAssignTask')) {
    if (!empty($_POST['project_id']) && !empty($_POST['user_id']) && !empty($_POST['tasks'])) {
        try {
            
            $conn->beginTransaction();

            $tasks = $_POST['tasks'];
            foreach ($tasks as $task) {
                $sql = $conn->prepare("SELECT `status` FROM tasks WHERE `id` = ? AND (`status` = 'assign_pro' OR `status` = 'assign_qc' OR `status` = 'assign_qa')");
                $sql->execute([$task]);
                $sql = $sql->fetch(PDO::FETCH_ASSOC);

                if (!$sql) {
                    $conn->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => 'Some tasks are already assigned, please check.', "status" => 600));
                    exit;
                }

                switch ($sql['status']) {
                    case 'assign_pro':
                        $role = 'pro';
                        break;
                    case 'assign_qc':
                        $role = 'qc';
                        break;
                    case 'assign_qa':
                        $role = 'qa';
                        break;
                }

                $assign = $conn->prepare("UPDATE `assign` SET `user_id` = ? WHERE `project_id` =  ? AND `task_id` = ? AND `role` = ?");
                $assign->execute([$_POST['user_id'], $_POST['project_id'], $task, $role]);

            }

            $conn->commit();

            http_response_code(200);
            echo json_encode(array("message" => 'Tasks reassigned successfully.', "status" => 200));
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'FilterTask') {
    // Get filter values from POST request
    $status = $_POST['status'] ?? '';
    $project_id = $_POST['project_id'] ?? '';

    // Start building the SQL query
    $sql = "SELECT `assign`.* , `users`.`name` , `tasks`.`status` , `tasks`.`task_id`, `tasks`.`estimated_hour` , `tasks`.`area_sqkm` ,`projects`.`area` , `projects`.`project_name`  FROM `assign` JOIN `tasks` ON `tasks`.`id` = `assign`.`task_id` JOIN `projects` ON `tasks`.`project_id` = `projects`.`id` JOIN `users` ON `users`.`id` = `assign`.`user_id` WHERE `assign`.`status` != 'complete' AND `assign`.`user_id` = ?";
    $params = [];
    $params[] = $user_id;


    if (!empty($status)) {
        $sql .= ' AND `tasks`.`status` = ?';
        $params[] = $status;
    }

    if (!empty($project_id)) {
        $sql .= ' AND `assign`.`project_id` = ?';
        $params[] = $project_id;
    }else{
        exit;
    }


    $sql .= ' ORDER BY `assign`.`id` DESC';
    try {
        $query = $conn->prepare($sql);
        $query->execute($params);

        // Fetch all the results
        $tasks = $query->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        $i = 0;
        foreach ($tasks  as $value) {

            echo '
            <tr>
            <td>
                <div class="form-check form-check-md">
                    <input class="form-check-input task-checkbox" type="checkbox" value="' . $value['id'] . '">
                </div>
            </td>
            <td>
                ' . ++$i . '
            </td>
            <td>
                <h6 class="fw-medium"><a href="task-details.php?id=' . $value['id'] . '">' . $value['task_id'] . '</a></h6>
            </td>
            <td>
                <h6 class="fw-medium"><a href="task-details.php?id=' . $value['id'] . '">' . $value['project_name'] . '</a></h6>
            </td>
            <td>
                ' . $value['area_sqkm'] . '' . strtoupper($value['area']) . '.
            </td>
            <td>
            ' . $value['estimated_hour'] . 'Hr.
            </td>
            <td>
                <b>' . ucfirst(str_replace('_', ' ', $value['status'])) . '</b>
                <br> '.$value['name'].'
            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <span class="rounded-circle bg-transparent-danger d-flex justify-content-center align-items-center me-2"><i class="ti ti-point-filled text-danger"></i></span> ' . ucfirst($value['complexity'] ?? 'Lower') . '
                    </a>
                </div>
            </td>
             <td>
                '.date('d M, Y',strtotime($value['created_at'])).'
            </td>
            <td>
                <div class="action-icon d-inline-flex">
                    <a href="#" class="me-2" ><i class="ti ti-home"></i></a>
                </div>
            </td>
        </tr>
            ';
        
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

