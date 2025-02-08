<?php
include __DIR__ . '/../database/conn.php';
session_start();

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'filterEfficiency')) {
    $dateRange = $_POST['dateRange'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    $sql = "SELECT `efficiency`.* , `projects`.`project_name` , `tasks`.`task_id` , `users`.`name` as `user_name` , `users`.`profile` as `user_profile` , `role`.`name` as `role_name` FROM `efficiency` JOIN `users` ON `users`.`id` = `efficiency`.`user_id` JOIN `role` ON `role`.`id` = `users`.`role_id` JOIN `tasks` ON `tasks`.`id` = `efficiency`.`task_id` JOIN `projects` ON `projects`.`id` = `efficiency`.`project_id` WHERE 1 = 1";

    $params = [];

    // Filter by date range
    if (!empty($dateRange)) {
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND DATE(efficiency.created_at) BETWEEN ? AND ?';
        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }else{
        $sql .= '`efficiency`.`created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY) ';
    }

    // Filter by leave type
    if (!empty($user_id)) {
        $sql .= ' AND `efficiency`.`user_id` = ?';
        $params[] = $user_id;
    }

    $sql .= ' ORDER BY `efficiency`.`id` DESC';
    $query = $conn->prepare($sql);
    $query->execute($params);

    $efficincy = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($efficincy as $value) {
        echo '<tr>
        <td>
            <div class="form-check form-check-md">
                <input class="form-check-input" type="checkbox">
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center file-name-icon">
                <a href="javascript:void(0);" class="avatar avatar-md border avatar-rounded">
                    <img src="' . ($value['user_profile'] == '' ? 'assets/img/users/user-32.jpg' : $value['user_profile']) . '" class="img-fluid" alt="img">
                </a>
                <div class="ms-2">
                    <h6 class="fw-medium"><a href="javascript:void(0);">' . $value['user_name'] . '</a></h6>
                    <span class="fs-12 fw-normal ">' . ucfirst($value['role_name']) . '</span>
                </div>
            </div>
        </td>
        <td>'.$value['task_id'].'</td>
        <td>
            '.$value['project_name'].'
        </td>
        <td>
            '.strtoupper($value['profile']).'
        </td>
        <td>
            '.$value['taken_time'].'min
        </td>
        <td>
            '.$value['total_time'].'min
        </td>
        <td>
            <span class="fs-12 mb-1">Completed '.round($value['efficiency'],2).'%</span>
            <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 87px;height: 5px;">
                <div class="progress-bar bg-primary" style="width: '.round($value['efficiency'],2).'%"></div>
            </div>
        </td>
         <td>
            ' . date('d M, Y h:i A', strtotime($value['created_at'])) . '
        </td>
        <td>
            ' . date('d M, Y  h:i A', strtotime($value['updated_at'])) . '
        </td>
    </tr>';
    } 
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'myFilterEfficiency')) {
    $dateRange = $_POST['dateRange'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    $sql = "SELECT `efficiency`.* , `projects`.`project_name` , `tasks`.`task_id` , `users`.`name` as `user_name` , `users`.`profile` as `user_profile` , `role`.`name` as `role_name` FROM `efficiency` JOIN `users` ON `users`.`id` = `efficiency`.`user_id` JOIN `role` ON `role`.`id` = `users`.`role_id` JOIN `tasks` ON `tasks`.`id` = `efficiency`.`task_id` JOIN `projects` ON `projects`.`id` = `efficiency`.`project_id` WHERE `efficiency`.`user_id` = ?";

    $params = [];
    $params[] = $_SESSION['userId'];

    // Filter by date range
    if (!empty($dateRange)) {
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND DATE(efficiency.created_at) BETWEEN ? AND ?';
        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }else{
        $sql .= '`efficiency`.`created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY) ';
    }

    $sql .= ' ORDER BY `efficiency`.`id` DESC';
    $query = $conn->prepare($sql);
    $query->execute($params);

    $efficincy = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($efficincy as $value) {
        echo '<tr>
        <td>
            <div class="form-check form-check-md">
                <input class="form-check-input" type="checkbox">
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center file-name-icon">
                <a href="javascript:void(0);" class="avatar avatar-md border avatar-rounded">
                    <img src="' . ($value['user_profile'] == '' ? 'assets/img/users/user-32.jpg' : $value['user_profile']) . '" class="img-fluid" alt="img">
                </a>
                <div class="ms-2">
                    <h6 class="fw-medium"><a href="javascript:void(0);">' . $value['user_name'] . '</a></h6>
                    <span class="fs-12 fw-normal ">' . ucfirst($value['role_name']) . '</span>
                </div>
            </div>
        </td>
        <td>'.$value['task_id'].'</td>
        <td>
            '.$value['project_name'].'
        </td>
        <td>
            '.strtoupper($value['profile']).'
        </td>
        <td>
            '.$value['taken_time'].'min
        </td>
        <td>
            '.$value['total_time'].'min
        </td>
        <td>
            <span class="fs-12 mb-1">Completed '.round($value['efficiency'],2).'%</span>
            <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 87px;height: 5px;">
                <div class="progress-bar bg-primary" style="width: '.round($value['efficiency'],2).'%"></div>
            </div>
        </td>
         <td>
            ' . date('d M, Y h:i A', strtotime($value['created_at'])) . '
        </td>
        <td>
            ' . date('d M, Y  h:i A', strtotime($value['updated_at'])) . '
        </td>
    </tr>';
    } 
}
