<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
// header('Content-Type: application/json');
session_start();
$user_id = $_SESSION['userId'];

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'clockOut')) {
    $currentTime = new DateTime();
    
    $sql = $conn->prepare('SELECT * FROM `attendance` WHERE date = CURDATE() AND `user_id` = ?');
    $sql->execute([$user_id]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $sql = $conn->prepare('SELECT * FROM `attendance` WHERE date = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND `user_id` = ?');
        $sql->execute([$user_id]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);
    }

    if ($result) {
        if ($result['clock_out_time']) {
            http_response_code(404);
            echo json_encode(array("message" => 'Already clocked out', "status" => 404));
            exit;
        } else {
            $TclockInTime = strtotime($result['clock_in_time']);

            $TclockOutTime = time();
            $formattedClockOutTime = date('Y-m-d H:i:s', $TclockOutTime);

            $timeDifferenceSeconds = $TclockOutTime - $TclockInTime;

            $timeDifferenceHours = round($timeDifferenceSeconds / 3600, 2);

            $not_allowed = ($timeDifferenceHours < 5) ? 1 : 0;

            $sql = $conn->prepare(
                'UPDATE attendance SET clock_out_time = ?, `not_allowed` = ?, `hours` = ? WHERE `id` = ?'
            );
            $sql->execute([$formattedClockOutTime, $not_allowed, $timeDifferenceHours, $result['id']]);

            http_response_code(200);
            echo json_encode(array("message" => 'Clock out successful', "status" => 200));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'Please clock in first', "status" => 404));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'filterAttandace')) {
    $dateRange = $_POST['dateRange'] ?? '';
    $status = $_POST['status'] ?? '';
    $role = $_POST['role'] ?? '';

    $sql = '
    SELECT 
        `attendance`.*, 
        `users`.`name` AS `user_name`, 
        `users`.`employee_id`, 
        `role`.`name` AS `role_name`,
        `shift`.`name` AS `shift_name`
    FROM 
        `attendance`
    JOIN 
        `users` ON `attendance`.`user_id` = `users`.`id`
    LEFT JOIN
        `shift` ON `attendance`.`shift_id` = `shift`.`id`
    JOIN 
        `role` ON `users`.`role_id` = `role`.`id`
    WHERE 
        1 = 1
';

    $params = [];

    // Filter by date range
    if (!empty($dateRange)) {
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND `attendance`.`date` BETWEEN ? AND ?';
        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }

    // Filter by status
    if (!empty($status)) {
        if ($status === 'absent') {
            $sql .= ' AND `attendance`.`not_allowed` = 1';
        } elseif ($status === 'present') {
            $sql .= ' AND `attendance`.`not_allowed` = 0';
        }
    }

    if (!empty($role)) {
        if ($role != '') {
            $sql .= ' AND `users`.`role_id` = ?';
            $params[] = $role;
        }
    }

    $sql .= ' ORDER BY `attendance`.`date` DESC';
    $query = $conn->prepare($sql);
    $query->execute($params);

    $attendance = $query->fetchAll(PDO::FETCH_ASSOC);

    $i = 0;
    // Generate table rows dynamically
    foreach ($attendance as $row) {

        if ($row['clock_out_time'] != '') {
            $attendance_clock_out = date('h:i A', strtotime($row['clock_out_time']));
        } else {
            $attendance_clock_out = '';
        }


        if ($row['regularisation'] == 1) {
            $status = '<span class="text-danger" data-bs-toggle="modal" data-bs-target="#regularisation" onclick="addRegularisation(' . $row['id'] . ',\'' . $row['clock_out_time'] . '\')" style="cursor: pointer">Regularization Accept </span>';
        } else {
            $status = '';
        }
        if ($roleId != 1 && !(in_array('attendance-regulation', $pageAccessList))) {
            $status = '';
        }

        $eff = $conn->prepare("SELECT SUM(taken_time) as taken_time , SUM(total_time) as total_time FROM `efficiency` WHERE user_id = ? AND DATE(created_at) = ?");
        $eff->execute([$row['user_id'], $row['date']]);
        $eff = $eff->fetch(PDO::FETCH_ASSOC);

        echo '<tr>';
        echo '<td>' . ++$i . '</td>';
        echo '<td>' . date('d M, Y', strtotime($row['date'])) . '</td>';
        echo '<td>' . $row['user_name'] . '<br>';
        echo '<span class="badge badge-success-transparent d-inline-flex align-items-center">' . ucfirst($row['role_name']) . '</span>';
        if ($row['not_allowed'] == 1) {
            echo '<span class="badge badge-danger-transparent d-inline-flex align-items-center">Absent</span>';
        }
        echo '</td>';
        echo '<td>' . date('h:i A', strtotime($row['clock_in_time'])) . '</td>';
        echo '<td>' .$attendance_clock_out  . ' <br> '.$status.'</td>';
        echo '<td>'.$row['shift_name'].'</td>';
        echo '<td>'.round($eff['taken_time'],2).'Min / '.round($eff['total_time'],2).'Min</td>';
        echo '<td>';
        echo '<span class="badge badge-success d-inline-flex align-items-center">';
        echo '<i class="ti ti-clock-hour-11 me-1"></i>' . $row['hours'] . ' Hrs</span>';
        echo '</td>';
        echo '</tr>';
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'myFilterAttandace')) {
    $dateRange = $_POST['dateRange'] ?? '';
    $status = $_POST['status'] ?? '';
    $role = $_POST['role'] ?? '';

    $sql = '
    SELECT 
        `attendance`.*, 
        `users`.`name` AS `user_name`, 
        `users`.`employee_id`, 
        `role`.`name` AS `role_name`,
        `shift`.`name` AS `shift_name`
    FROM 
        `attendance`
    JOIN 
        `users` ON `attendance`.`user_id` = `users`.`id`
    LEFT JOIN
        `shift` ON `attendance`.`shift_id` = `shift`.`id`
    JOIN 
        `role` ON `users`.`role_id` = `role`.`id`
    WHERE 
        `users`.`id` = ?
';

    $params = [];
    $params[] = $user_id;

    // Filter by date range
    if (!empty($dateRange)) {
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND `attendance`.`date` BETWEEN ? AND ?';
        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }

    // Filter by status
    if (!empty($status)) {
        if ($status === 'absent') {
            $sql .= ' AND `attendance`.`not_allowed` = 1';
        } elseif ($status === 'present') {
            $sql .= ' AND `attendance`.`not_allowed` = 0';
        }
    }

    if (!empty($role)) {
        if ($role != '') {
            $sql .= ' AND `users`.`role_id` = ?';
            $params[] = $role;
        }
    }

    $sql .= ' ORDER BY `attendance`.`date` DESC';
    $query = $conn->prepare($sql);
    $query->execute($params);

    $attendance = $query->fetchAll(PDO::FETCH_ASSOC);

    $i = 0;
    // Generate table rows dynamically
    foreach ($attendance as $row) {
        if ($row['clock_out_time'] != '') {
            $attendance_clock_out = date('h:i A', strtotime($row['clock_out_time']));
        } else {
            $attendance_clock_out = '';
            if ($row['date'] != date('Y-m-d')) {
                $regulazation = '<a href="#" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#regularisation" onclick="addRegularisation(' . $row['id'] . ')">Regularisation</a>';
            }
        }


        if ($row['regularisation'] == 1) {
            $status = '<span class="text-danger">Regularization Pending </span>';
        } else {
            $status = '';
        }

        $eff = $conn->prepare("SELECT SUM(taken_time) as taken_time , SUM(total_time) as total_time FROM `efficiency` WHERE user_id = ? AND DATE(created_at) = ?");
        $eff->execute([$row['user_id'], $row['date']]);
        $eff = $eff->fetch(PDO::FETCH_ASSOC);

        echo '<tr>';
        echo '<td>' . ++$i . '</td>';
        echo '<td>' . date('d M, Y', strtotime($row['date'])) . '</td>';
        echo '<td>' . $row['user_name'] . '<br>';
        echo '<span class="badge badge-success-transparent d-inline-flex align-items-center">' . ucfirst($row['role_name']) . '</span>';
        if ($row['not_allowed'] == 1) {
            echo '<span class="badge badge-danger-transparent d-inline-flex align-items-center">Absent</span>';
        }
        echo '</td>';
        echo '<td>' . date('h:i A', strtotime($row['clock_in_time'])) . '</td>';
        echo '<td>' . ($row['clock_out_time'] != '' ?  $attendance_clock_out : ($attendance_clock_out == '' ? $regulazation : $status)) . '</td>';
        echo '<td>'.$row['shift_name'].'</td>';
        echo '<td>'.round($eff['taken_time'],2).'Min / '.round($eff['total_time'],2).'Min</td>';
        echo '<td>';
        echo '<span class="badge badge-success d-inline-flex align-items-center">';
        echo '<i class="ti ti-clock-hour-11 me-1"></i>' . $row['hours'] . ' Hrs</span>';
        echo '</td>';
        echo '</tr>';
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addRegularisation')) {
    if ($_POST['clockout_time'] != '' && $_POST['attendance_id'] != '') {
        $sql = $conn->prepare("UPDATE `attendance` SET `clock_out_time` = ? , `remark` = ?, `regularisation` = 1 WHERE `id` = ? AND `user_id` = ?");
        $result = $sql->execute([$_POST['clockout_time'], $_POST['remark'], $_POST['attendance_id'], $user_id]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'Add Regularisation successful', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Add ClockOut Time.', "status" => 500));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'approveAttendance')) {

    $sql = $conn->prepare("SELECT * FROM `attendance` WHERE `id` = ?");
    $sql->execute([$_POST['id']]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $TclockInTime = strtotime($result['clock_in_time']);
        $TclockOutTime = strtotime($_POST['clockout_time']);
        $formattedClockOutTime = date('Y-m-d H:i:s', $TclockOutTime);
        $timeDifferenceSeconds = $TclockOutTime - $TclockInTime;
        $timeDifferenceHours = round($timeDifferenceSeconds / 3600, 2);


        $sql = $conn->prepare("UPDATE `attendance` SET `clock_out_time` = ? ,  `regularisation` = 0 , hours = ? WHERE `id` = ?");
        $result = $sql->execute([$_POST['clockout_time'], $timeDifferenceHours, $_POST['id']]);
        if ($result) {
            http_response_code(200);
            echo json_encode(array("message" => 'Approve Regularisation successful', "status" => 200));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => 'Something went wrong', "status" => 500));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => 'Attandance not found', "status" => 400));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getMonth')) {
    if ($_GET['startDate'] != '' && $_GET['endDate'] != '') {
        $startdate = $_GET['startDate'];
        $enddate = $_GET['endDate'];

        if ($startdate > $enddate) {
            http_response_code(400);
            echo json_encode(["message" => "First Date is always Greater then Second Date.", "status" => 400]);
            exit;
        }

        $startDateObj = new DateTime($startdate);
        $endDateObj = new DateTime($enddate);
        $currentDateObj = $startDateObj;
        $attendanceArray = [];

        $users = $conn->prepare('SELECT * FROM `users` ORDER BY `id` DESC');
        $users->execute();
        $users = $users->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            $data = [];
            $currentDateObj = new DateTime($startdate);

            $date = [];
            $date[] = 'Date';
            $data[] = $user['first_name'] . ' ' . $user['last_name'];
            while ($currentDateObj <= $endDateObj) {
                $currentDate = $currentDateObj->format('Y-m-d');

                $date[] = $currentDate;

                $attendances = $conn->prepare("SELECT * FROM `attendance` WHERE `user_id` = ? AND `date` = ?");
                $attendances->execute([$user['id'], $currentDate]);
                $attendance = $attendances->fetch(PDO::FETCH_ASSOC);

                if ($attendance) {
                    // $data[] = '1';
                    if ($attendance['clock_in_time'] != '' && $attendance['clock_out_time'] != '') {
                        $data[] = date('h:i A', strtotime($attendance['clock_in_time'])) . ' - ' . date('h:i A', strtotime($attendance['clock_out_time']));
                    } else {
                        $data[] = date('h:i A', strtotime($attendance['clock_in_time'])) . ' - ';
                    }
                } else {
                    $holiday = $conn->prepare("SELECT * FROM `holiday` WHERE `date` = ?");
                    $holiday->execute([$currentDate]);
                    $holiday = $holiday->fetch(PDO::FETCH_ASSOC);

                    if ($holiday) {
                        $data[] = 'holiday';
                    } else {
                        $leave = $conn->prepare("SELECT * FROM `leaves` WHERE `form_date` <= ? AND `end_date` >= ? AND `user_id` = ? AND `status` = 'approve'");
                        $leave->execute([$currentDate, $currentDate, $user['id']]);
                        $leave = $leave->fetch(PDO::FETCH_ASSOC);

                        if ($leave) {
                            $data[] = 'leave';
                        } else {
                            if (date("w", strtotime($currentDate)) == 0) {
                                $data[] = 'week off';
                            } else {
                                $data[] = '0';
                            }
                        }
                    }
                }

                $currentDateObj->modify('+1 day');
            }
            $attendanceArray['attendance'][] = $data;
        }
        $attendanceArray['date'] = $date;
        echo json_encode($attendanceArray);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Start Date and End Date is required.", "status" => 400]);
    }
}
