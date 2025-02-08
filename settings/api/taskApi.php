<?php
include __DIR__ . '/../database/conn.php';
session_start();
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'FilterTask') {
    // Get filter values from POST request
    $status = $_POST['status'] ?? '';
    $project_id = $_POST['project_id'] ?? '';

    // Start building the SQL query
    $sql = "SELECT `tasks`.* , `projects`.`area` , `projects`.`project_name`  FROM `tasks` JOIN `projects` ON `projects`.`id` = `tasks`.`project_id` WHERE 1 = 1";
    $params = [];


    if (!empty($status)) {
        $sql .= ' AND `tasks`.`status` = ?';
        $params[] = $status;
    }

    if (!empty($project_id)) {
        $sql .= ' AND `projects`.`id` = ?';
        $params[] = $project_id;
    } else {
        exit;
    }


    $sql .= ' ORDER BY `tasks`.`id` DESC';
    try {
        $query = $conn->prepare($sql);
        $query->execute($params);

        // Fetch all the results
        $tasks = $query->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        $i = 0;
        foreach ($tasks  as $value) {

            switch ($value['status']) {
                case 'assign_pro':
                    $role = 'pro';
                    break;
                case 'pro_in_progress':
                    $role = 'pro';
                    break;
                case 'assign_qc':
                    $role = 'qc';
                    break;
                case 'qc_in_progress':
                    $role = 'qc';
                    break;
                case 'assign_qa':
                    $role = 'qa';
                    break;
                case 'qa_in_progress':
                    $role = 'qa';
                    break;
            }

            $assign = $conn->prepare("SELECT `users`.`name` , `assign`.`user_id` FROM `assign` JOIN `users` ON `users`.`id` = `assign`.`user_id` WHERE `assign`.`task_id` = ? AND `assign`.`role` = ?");
            $assign->execute([$value['id'], $role]);
            $assign = $assign->fetch(PDO::FETCH_ASSOC);

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
                    <h6 class="fw-medium"><a href="project-details.php?id=' . $value['id'] . '">' . $value['task_id'] . '</a></h6>
                </td>
                <td>
                    <h6 class="fw-medium"><a href="project-details.php?id=' . $value['id'] . '">' . $value['project_name'] . '</a></h6>
                </td>
                <td>
                    ' . $value['area_sqkm'] . '' . strtoupper($value['area']) . '.
                </td>
                <td>
                ' . $value['estimated_hour'] . 'Hr.
                </td>
                <td>
                    ' . ucfirst(str_replace('_', ' ', $value['status'])) . '
                    <br> ' . $assign['name'] . '
                </td>
                <td>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-transparent-danger d-flex justify-content-center align-items-center me-2"><i class="ti ti-point-filled text-danger"></i></span> ' . ucfirst($value['complexity']) . '
                        </a>
                    </div>
                </td>
                    <td>
                    <span class="badge badge-' . ($value['status'] == 'complete' ? 'success' : 'danger') . ' d-inline-flex align-items-center badge-xs">
                        <i class="ti ti-point-filled me-1"></i>' . ($value['status'] == 'complete' ? 'Active' : 'Completed') . '
                    </span>
                </td>
                <td>
                    <div class="action-icon d-inline-flex">
                        <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_task" onclick="getTask(' . $value['id'] . ')"><i class="ti ti-edit"></i></a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal" onclick="deleteTask(' . $value['id'] . ')"><i class="ti ti-trash"></i></a>
                    </div>
                </td>
            </tr>
                ';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'addTask') {
    if (($_POST['project_id'] != '') && ($_POST['task_id'] != '') && ($_POST['area'] != '') && ($_POST['estimated_hour'] != '')) {

        $check = $conn->prepare("SELECT * FROM `project_time` WHERE `project_id` = ?");
        $check->execute(params: [$_POST['project_id']]);
        $check = $check->fetch(PDO::FETCH_ASSOC);
        if (!$check) {
            http_response_code(500);
            echo json_encode(array("message" => 'Project Time is not uploaded.', "status" => 500));
            exit;
        }

        $task = $conn->prepare("SELECT `id`  FROM `tasks` WHERE `task_id` = ?");
        $task->execute([$_POST['task_id']]);
        $task = $task->fetch(PDO::FETCH_ASSOC);
        if ($task) {
            http_response_code(400);
            echo json_encode(array("message" => "This Task Already Uploaded", "status" => 400));
        } else {
            $check = $conn->prepare('INSERT INTO `tasks`(`task_id`, `project_id`, `area_sqkm`, `complexity`, `start_date`, `end_date`, `estimated_hour`) VALUES ( ? , ? , ? , ? , ? , ? , ?)');
            $result = $check->execute([$_POST['task_id'], $_POST['project_id'], $_POST['area'], $_POST['complexity'], $_POST['start_date'], $_POST['end_date'], $_POST['estimated_hour']]);
            if ($result) {
                http_response_code(200);
                echo json_encode(array("message" => 'successfull Task Added.', "status" => 200));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong', "status" => 500));
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'editTask') {
    if (($_POST['id'] != '') && ($_POST['project_id'] != '') && ($_POST['task_id'] != '') && ($_POST['area'] != '') && ($_POST['estimated_hour'] != '')) {

        $startDate = date('Y-m-d', strtotime($_POST['start_date']));
        $endDate = date('Y-m-d', strtotime($_POST['end_date']));


        $task = $conn->prepare("SELECT `id`  FROM `tasks` WHERE `id` = ? AND `task_id` = ?");
        $task->execute([$_POST['id'], $_POST['task_id']]);
        $task = $task->fetch(PDO::FETCH_ASSOC);
        if ($task) {

            $check = $conn->prepare('UPDATE `tasks` SET `task_id` = ?, `project_id` = ?, `area_sqkm` = ?, `complexity` = ? , `start_date` = ?, `end_date` = ?, `estimated_hour` = ? WHERE `id` = ?');
            $result = $check->execute([$_POST['task_id'], $_POST['project_id'], $_POST['area'], $_POST['complexity'], $startDate, $endDate, $_POST['estimated_hour'], $_POST['id']]);
            if ($result) {
                http_response_code(200);
                echo json_encode(array("message" => 'successfull Task Update.', "status" => 200));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong', "status" => 500));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "This Task is not found", "status" => 400));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'deleteTask') {
    if ($_POST['id'] != '') {

        $task = $conn->prepare("SELECT `id`  FROM `tasks` WHERE `id` = ?");
        $task->execute([$_POST['id']]);
        $task = $task->fetch(PDO::FETCH_ASSOC);
        if ($task) {

            $check = $conn->prepare('DELETE FROM `tasks` WHERE `id` = ?');
            $result = $check->execute([$_POST['id']]);
            if ($result) {
                http_response_code(200);
                echo json_encode(array("message" => 'successfull Task Delete.', "status" => 200));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong', "status" => 500));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "This Task is not found", "status" => 400));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['type'] === 'getTask') {
    $sql = $conn->prepare("SELECT * FROM `tasks` WHERE `id` = ?");
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
