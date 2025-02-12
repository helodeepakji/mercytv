<?php
include __DIR__ . '/../database/conn.php';
session_start();

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'filterProgram')) {

    $dateRange = $_POST['dateRange'] ?? '';
    $sql = "SELECT * FROM `program` WHERE 1 = 1";
    $params = [];

    if (!empty($dateRange)) {
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND `date` BETWEEN ? AND ?';
        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    } else {
        $sql .= 'AND `date` >= CURDATE()';
    }

    $query = $conn->prepare($sql);
    $query->execute($params);
    $efficincy = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($efficincy as $value) {
        $test = 'assets/img/users/user-32.jpg';
        $image = $value['image'];
        echo '<tr>
            <td>
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox">
                </div>
            </td>
            <td><a>' . ++$i . '</a>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <a class="avatar avatar-md" data-bs-toggle="modal"
                        data-bs-target="#view_details"><img
                            src="'.($image ?? $test).'"
                            class="img-fluid rounded-circle" alt="img"></a>
                    <div class="ms-2">
                        <p class="text-dark mb-0"><a data-bs-toggle="modal"
                                data-bs-target="#view_details">' . $value['program'] . '</a>
                        </p>
                    </div>
                </div>
            </td>
            <td>' . ucfirst($value['desc']) . '</td>
            <td>' . date('h:i A', strtotime($value['time'])) . '</td>
            <td>' . date('d M, Y', strtotime($value['date'])) . '</td>
             <td>' . ucfirst($value['duration']) . '</td>
            <td>
                <div class="action-icon d-inline-flex">
                    <a href="#" class="me-2" data-bs-toggle="modal"
                        data-bs-target="#edit_employee"
                        onclick="getEmployee(' . $value['id'] . ')"><i
                            class="ti ti-edit"></i></a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"
                        onclick="deleteEmployee(' . $value['id'] . ')"><i
                            class="ti ti-trash"></i></a>
                </div>
            </td>
        </tr>';
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getProgram')) {
    if ($_GET['id'] != '') {
        $sql = "SELECT * FROM `program` WHERE id = ?";
        $query = $conn->prepare($sql);
        $query->execute([$_GET['id']]);
        $efficincy = $query->fetch(PDO::FETCH_ASSOC);
        echo json_encode($efficincy);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'deleteProgram')) {
    if ($_GET['id'] != '') {
        $sql = "DELETE FROM `program` WHERE id = ?";
        $query = $conn->prepare($sql);
        $result = $query->execute([$_GET['id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => "Successfull Delete Program", "status" => 200));
        }else {
            http_response_code(500);
            echo json_encode(array("message" => "Somethig Went Wrong", "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'editProgram') {
    if (($_POST['id'] != '') && ($_POST['program'] != '') && ($_POST['date'] != '') && ($_POST['time'] != '') && ($_POST['duration'] != '')) {

        $date = date('Y-m-d', strtotime($_POST['date']));

        $program = $conn->prepare("SELECT `id` FROM `program` WHERE `id` = ?");
        $program->execute([$_POST['id']]);
        $program = $program->fetch(PDO::FETCH_ASSOC);
        if ($program) {

            $check = $conn->prepare('UPDATE `program` SET `program` = ?, `desc` = ?, `date` = ?, `time` = ? , `duration` = ? WHERE `id` = ?');
            $result = $check->execute([$_POST['program'], $_POST['desc'], $date, $_POST['time'], $_POST['duration'], $_POST['id']]);

            if($_FILES['image']['name'] != ''){
                $uploadDir = '../../uploads/';
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = str_replace(' ', '', strtolower($_POST['program'])) .'.' . $fileExt;
                $uploadFile = $uploadDir . $imageName;

                str_replace(' ','',strtolower($program['program']));
                $check = $conn->prepare('UPDATE `program` SET `image` = ? WHERE `program` = ?');
                $result = $check->execute(['uploads/'.$imageName,  $_POST['program']]);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
            }

            if ($result) {
                http_response_code(200);
                echo json_encode(array("message" => 'successfull Program Update.', "status" => 200));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => 'Something went wrong', "status" => 500));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "This Program is not found", "status" => 400));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Fill all required fields", "status" => 400));
    }
}
