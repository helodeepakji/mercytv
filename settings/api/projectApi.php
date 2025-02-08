<?php
include __DIR__ . '/../database/conn.php';
session_start();
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'FilterProduct') {
    // Get filter values from POST request
    $dateRange = $_POST['dateRange'] ?? '';
    $status = $_POST['status'] ?? '';

    // Start building the SQL query
    $sql = "SELECT `projects`.*, `users`.`name`, `users`.`profile` 
    FROM `projects` 
    LEFT JOIN `project_assign` ON `project_assign`.`project_id` = `projects`.`id` 
    LEFT JOIN `users` ON `users`.`id` = `project_assign`.`user_id` WHERE 1 = 1";
    $params = [];

    // Filter by date range if provided
    if (!empty($dateRange)) {
        // Split date range into start and end date
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND `projects`.`end_date` BETWEEN ? AND ?';

        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }


    if (!empty($status)) {
        $sql .= ' AND `projects`.`is_complete` = ?';
        $params[] = $status; // Assuming 0 for Active and 1 for Completed
    }

    // Add ordering by project ID
    $sql .= ' ORDER BY `projects`.`id` DESC';

    // Prepare and execute the SQL query
    try {
        $query = $conn->prepare($sql);
        $query->execute($params);

        // Fetch all the results
        $product = $query->fetchAll(PDO::FETCH_ASSOC);

        // Loop through the results and display the project details
        http_response_code(200);
        foreach ($product as $value) {
            if ($value['name'] != '') {
                $data = '<div class="d-flex align-items-center file-name-icon">
                    <a href="javascript:void(0);" class="avatar avatar-sm border avatar-rounded">
                        <img src="' . ($value['profile'] != '' ? $value['profile'] : 'assets/img/users/user-39.jpg') . '" class="img-fluid" alt="img">
                    </a>
                    <div class="ms-2">
                        <h6 class="fw-normal"><a href="javascript:void(0);">' . $value['name'] . '</a></h6>
                    </div>
                </div>';
            } else {
                $data = '<span class="badge badge-success d-inline-flex align-items-center badge-xs" data-bs-toggle="modal" data-bs-target="#edit_project" onclick="getProject(' . $value['id'] . ')">
                    <i class="ti ti-point-filled me-1"></i>Assign Project
                </span>';
            }

            echo '
            <tr>
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </td>
                <td><a href="project-details.php">PRO-' . $value['id'] . '</a></td>
                <td><a href="project-details.php?id=' . $value['id'] . '">' . $value['client_id'] . '</a></td>
                <td>
                    <h6 class="fw-medium"><a href="project-details.php?id=' . $value['id'] . '">' . $value['project_name'] . '</a></h6>
                </td>
                <td>' . $data . '
                </td>
                <td>
                    ' . $value['estimated_hour'] . 'Hr.
                </td>
                <td>' . date('d M, Y', strtotime($value['start_date'])) . '</td>
                <td>' . date('d M, Y', strtotime($value['end_date'])) . '</td>
                <td>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-transparent-danger d-flex justify-content-center align-items-center me-2">
                                <i class="ti ti-point-filled text-danger"></i>
                            </span> ' . ucfirst($value['complexity']) . '
                        </a>
                    </div>
                </td>
                <td>
                    <span class="badge badge-' . ($value['is_complete'] == 0 ? 'success' : 'danger') . ' d-inline-flex align-items-center badge-xs" onclick="switchProject(' . $value['id'] . ',' . $value['is_complete'] . ')">
                        <i class="ti ti-point-filled me-1"></i>' . ($value['is_complete'] == 0 ? 'Active' : 'Completed') . '
                    </span>
                </td>
                <td>
                    <div class="action-icon d-inline-flex">
                        <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_project" onclick="getProject(' . $value['id'] . ')"><i class="ti ti-edit"></i></a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal" onclick="deleteProject(' . $value['id'] . ')"><i class="ti ti-trash"></i></a>
                    </div>
                </td>
            </tr>';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'MyFilterProduct') {
    // Get filter values from POST request
    $dateRange = $_POST['dateRange'] ?? '';
    $status = $_POST['status'] ?? '';

    // Start building the SQL query
    $sql = "SELECT `projects`.*, `users`.`name` , `users`.`profile` FROM `projects` JOIN `project_assign` ON `project_assign`.`project_id` = `projects`.`id` JOIN `users` ON `users`.`id` = `project_assign`.`user_id` WHERE `project_assign`.`user_id` = ? ";
    $params = [];
    $params[] = $_SESSION['userId'];

    // Filter by date range if provided
    if (!empty($dateRange)) {
        // Split date range into start and end date
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND `projects`.`end_date` BETWEEN ? AND ?';

        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }


    if (!empty($status)) {
        $sql .= ' AND `projects`.`is_complete` = ?';
        $params[] = $status; // Assuming 0 for Active and 1 for Completed
    }

    // Add ordering by project ID
    $sql .= ' ORDER BY `projects`.`id` DESC';

    // Prepare and execute the SQL query
    try {
        $query = $conn->prepare($sql);
        $query->execute($params);

        // Fetch all the results
        $product = $query->fetchAll(PDO::FETCH_ASSOC);

        // Loop through the results and display the project details
        http_response_code(200);
        foreach ($product as $value) {
            if ($value['name'] != '') {
                $data = '<div class="d-flex align-items-center file-name-icon">
                    <a href="javascript:void(0);" class="avatar avatar-sm border avatar-rounded">
                        <img src="' . ($value['profile'] != '' ? $value['profile'] : 'assets/img/users/user-39.jpg') . '" class="img-fluid" alt="img">
                    </a>
                    <div class="ms-2">
                        <h6 class="fw-normal"><a href="javascript:void(0);">' . $value['name'] . '</a></h6>
                    </div>
                </div>';
            } else {
                $data = '<span class="badge badge-success d-inline-flex align-items-center badge-xs" data-bs-toggle="modal" data-bs-target="#edit_project" onclick="getProject(' . $value['id'] . ')">
                    <i class="ti ti-point-filled me-1"></i>Assign Project
                </span>';
            }

            echo '
            <tr>
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </td>
                <td><a href="project-details.php">PRO-' . $value['id'] . '</a></td>
                <td><a href="project-details.php?id=' . $value['id'] . '">' . $value['client_id'] . '</a></td>
                <td>
                    <h6 class="fw-medium"><a href="project-details.php?id=' . $value['id'] . '">' . $value['project_name'] . '</a></h6>
                </td>
                <td>' . $data . '
                </td>
                <td>
                    ' . $value['estimated_hour'] . 'Hr.
                </td>
                <td>' . date('d M, Y', strtotime($value['start_date'])) . '</td>
                <td>' . date('d M, Y', strtotime($value['end_date'])) . '</td>
                <td>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-transparent-danger d-flex justify-content-center align-items-center me-2">
                                <i class="ti ti-point-filled text-danger"></i>
                            </span> ' . ucfirst($value['complexity']) . '
                        </a>
                    </div>
                </td>
                <td>
                    <span class="badge badge-' . ($value['is_complete'] == 0 ? 'success' : 'danger') . ' d-inline-flex align-items-center badge-xs" onclick="switchProject(' . $value['id'] . ',' . $value['is_complete'] . ')">
                        <i class="ti ti-point-filled me-1"></i>' . ($value['is_complete'] == 0 ? 'Active' : 'Completed') . '
                    </span>
                </td>
                <td>
                    <div class="action-icon d-inline-flex">
                        <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_project" onclick="getProject(' . $value['id'] . ')"><i class="ti ti-edit"></i></a>
                    </div>
                </td>
            </tr>';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addProject')) {

    if (($_POST['project_name'] != '') && ($_POST['client_id'] != '') && ($_POST['description'] != '') && ($_POST['complexity'] != '')  && ($_POST['area'] != '')  &&  ($_POST['start_date'] != '') && ($_POST['end_date'] != '')) {

        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        if ($_POST['estimated_hour']) {
            $estimated_hour = $_POST['estimated_hour'];
        } else {
            $date1 = new DateTime($startDate);
            $date2 = new DateTime($endDate);
            $interval = $date1->diff($date2);
            $daysDifference = $interval->days;
            $estimated_hour = $daysDifference * 8;
        }

        $project = array($_POST['project_name'], $_POST['client_id'], $_POST['description'], $_POST['area'], $_POST['complexity'], $startDate, $endDate, $estimated_hour);

        $check = $conn->prepare('INSERT INTO `projects`(`project_name`, `client_id`, `description`,`area` , `complexity`, `start_date`, `end_date` , `estimated_hour` ) VALUES (? , ? , ? , ? , ? , ? , ?,? )');
        $result = $check->execute($project);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'successfull Project Added.', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getAllProduct')) {
    $sql = $conn->prepare('SELECT * FROM `projects`');
    $sql->execute();
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No project found', "status" => 404));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['type'] === 'getProduct') {
    $sql = $conn->prepare("SELECT `projects`.* , `project_assign`.`user_id` FROM `projects` LEFT JOIN `project_assign` ON `project_assign`.`project_id` = `projects`.`id` WHERE `projects`.`id` = ?");
    $sql->execute([$_GET['id']]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result) {

        $team_leader = $conn->prepare("SELECT `user_id` FROM `team_leader_assign` WHERE `project_id` = ?");
        $team_leader->execute([$_GET['id']]);
        $team_leader = $team_leader->fetchAll(PDO::FETCH_ASSOC);
        
        $employee = $conn->prepare("SELECT `user_id` FROM `employee_assign` WHERE `project_id` = ?");
        $employee->execute([$_GET['id']]);
        $employee = $employee->fetchAll(PDO::FETCH_ASSOC);

        $check = $conn->prepare("SELECT * FROM `project_time` WHERE `project_id` = ?");
        $check->execute(params: [$_GET['id']]);
        $check = $check->fetch(PDO::FETCH_ASSOC);

        $result['project_manger'] = $result['user_id'];
        $result['team_leader'] = $team_leader;
        $result['employee'] = $employee;
        $result['time'] = $check;
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No project found', "status" => 404));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'deleteProject')) {

    if ($_POST['project_id'] != '') {


        // $deleteTask = $conn->prepare("DELETE FROM `tasks` WHERE `project_id` = ?");
        // $result = $deleteTask->execute([$_POST['project_id']]);

        $deletePro = $conn->prepare("DELETE FROM `projects` WHERE `id` = ?");
        $result = $deletePro->execute([$_POST['project_id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'successfull Project Delete.', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'updateProject')) {

    if (($_POST['project_name'] != '') && ($_POST['client_id'] != '') && ($_POST['description'] != '')  && ($_POST['area'] != '') && ($_POST['complexity'] != '') && ($_POST['start_date'] != '') && ($_POST['end_date'] != '') && ($_POST['project_id'] != '')) {

        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        if ($_POST['estimated_hour']) {
            $estimated_hour = $_POST['estimated_hour'];
        } else {
            $date1 = new DateTime($startDate);
            $date2 = new DateTime($endDate);
            $interval = $date1->diff($date2);
            $daysDifference = $interval->days;
            $estimated_hour = $daysDifference * 8;
        }

        $project = array($_POST['project_name'], $_POST['client_id'], $_POST['description'], $_POST['area'], $_POST['complexity'],  $startDate, $endDate, $estimated_hour, $_POST['project_id']);

        $sql = $conn->prepare("UPDATE `projects` SET `project_name` = ?, `client_id` = ?, `description`= ?, `area` = ? , `complexity`= ?, `start_date`= ?, `end_date` = ? , `estimated_hour` = ? WHERE `id` = ?");
        $result = $sql->execute($project);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'successfull Project Update.', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'completeProject')) {

    if ($_POST['id'] != '') {

        $deletePro = $conn->prepare("UPDATE `projects` SET `is_complete` = 1 WHERE `id` = ?");
        $result = $deletePro->execute([$_POST['id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'successfull Project Complete.', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'inCompleteProject')) {

    if ($_POST['id'] != '') {

        $deletePro = $conn->prepare("UPDATE `projects` SET `is_complete` = 0 WHERE `id` = ?");
        $result = $deletePro->execute([$_POST['id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'successfull Project inComplete.', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'projectPercentage')) {

    if ($_POST['pro'] != '' && $_POST['qc'] != '' && $_POST['qa'] != '' && $_POST['project_id'] != '') {

        $check = $conn->prepare("SELECT * FROM `project_time` WHERE `project_id` = ?");
        $check->execute([$_POST['project_id']]);
        $check = $check->fetch(PDO::FETCH_ASSOC);
        if ($check) {
            http_response_code(500);
            echo json_encode(array("message" => 'Time is already uploaded.', "status" => 500));
            exit;
        }

        $project = $conn->prepare("INSERT INTO `project_time` (`pro`, `qc`, `qa`, `project_id`) VALUES (? , ? , ? , ?)");
        $result = $project->execute([$_POST['pro'], $_POST['qc'], $_POST['qa'], $_POST['project_id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'Successful Project Time Upload.', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}
