<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
header("content-Type: application/json");
session_start();
$user_id = $_SESSION['userId'];


if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addShift')) {
    if (!empty($_POST['name']) && !empty($_POST['start_time']) && !empty($_POST['end_time'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("INSERT INTO `shift`(`name`, `start_time` , `end_time`) VALUES (? , ? , ?)");
            $result = $sql->execute([$_POST['name'], $_POST['start_time'], $_POST['end_time']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull add shift.', "status" => 200));
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

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'updateShift')) {
    if (!empty($_POST['name']) && !empty($_POST['id']) && !empty($_POST['start_time']) && !empty($_POST['end_time'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("UPDATE `shift` SET `name` = ? , `start_time` = ? , `end_time` = ? WHERE id = ?");
            $result = $sql->execute([$_POST['name'], $_POST['start_time'], $_POST['end_time'], $_POST['id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull update shift.', "status" => 200));
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

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getShift')) {
    $sql = $conn->prepare('SELECT * FROM `shift` WHERE `id` = ?');
    $sql->execute([$_GET['id']]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No Shift found', "status" => 404));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'deleteShift')) {
    $sql = $conn->prepare('DELETE FROM `shift` WHERE `id` = ?');
    $result = $sql->execute([$_GET['id']]);
    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => 'Delete Shift Succesfull']);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No Shift found', "status" => 404));
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'createRoster') {
    if (!empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['user_id']) && !empty($_POST['shift_id'])) {
        try {
            $conn->beginTransaction(); // Start transaction

            $startDate = date('Y-m-d', strtotime($_POST['start_date']));
            $endDate = date('Y-m-d', strtotime($_POST['end_date']));
            $shiftId = $_POST['shift_id'];
            $userIds = $_POST['user_id']; // This is an array

            // Check if a shift already exists in the given week range
            $sql = $conn->prepare('SELECT * FROM `weekly_roster` WHERE `week_start` = ? OR `week_end` = ?');
            $sql->execute([$startDate, $endDate]);
            $existingShift = $sql->fetch(PDO::FETCH_ASSOC);

            if ($existingShift) {
                http_response_code(200);
                echo json_encode(["message" => "Shift already exists for this week.", "status" => 200]);
            } else {
                // Insert each user into the roster
                $sql = $conn->prepare("INSERT INTO `weekly_roster`(`user_id`, `shift_id`, `week_start`, `week_end`) VALUES (?,?,?,?)");

                foreach ($userIds as $userId) {
                    $sql->execute([$userId, $shiftId, $startDate, $endDate]);
                }

                $conn->commit(); // Commit transaction
                http_response_code(200);
                echo json_encode(["message" => "Shift successfully added.", "status" => 200]);
            }
        } catch (Exception $e) {
            $conn->rollBack(); // Rollback if error
            http_response_code(500);
            echo json_encode(["message" => "An error occurred: " . $e->getMessage(), "status" => 500]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Fill all required fields", "status" => 400]);
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'updateRoster')) {
    if (!empty($_POST['name']) && !empty($_POST['id']) && !empty($_POST['start_time']) && !empty($_POST['end_time'])) {
        try {

            $conn->beginTransaction();

            $sql = $conn->prepare("UPDATE `shift` SET `name` = ? , `start_time` = ? , `end_time` = ? WHERE id = ?");
            $result = $sql->execute([$_POST['name'], $_POST['start_time'], $_POST['end_time'], $_POST['id']]);

            if ($result) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(array("message" => 'Successfull update shift.', "status" => 200));
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

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'getRoster')) {
    $sql = $conn->prepare('SELECT * FROM `weekly_roster` WHERE `id` = ?');
    $sql->execute([$_GET['id']]);
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No weekly_roster found', "status" => 404));
    }
}

if (($_SERVER['REQUEST_METHOD'] == 'GET') && ($_GET['type'] == 'deleteRoster')) {
    $sql = $conn->prepare('DELETE FROM `weekly_roster` WHERE `id` = ?');
    $result = $sql->execute([$_GET['id']]);
    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => 'Delete Shift Succesfull']);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => 'No Shift found', "status" => 404));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'FilterRoster') {
    header("Content-Type: text/html");
    // Get filter values from POST request
    $dateRange = $_POST['dateRange'] ?? '';

    // Start building the SQL query
    $sql = "SELECT 
    u.id, 
    u.name, 
    u.role_id, 
    r.name AS role_name, 
    u.profile, 
    wr.id AS roster_id,
    wr.week_start, 
    wr.week_end, 
    s.start_time, 
    s.end_time 
FROM users u
JOIN role r ON u.role_id = r.id
JOIN weekly_roster wr ON u.id = wr.user_id
JOIN shift s ON wr.shift_id = s.id
WHERE u.`is_terminated` = 0";
    $params = [];

    // Filter by date range if provided
    if (!empty($dateRange)) {
        // Split date range into start and end date
        [$startDate, $endDate] = explode(' - ', $dateRange);
        $sql .= ' AND `wr`.`week_start` BETWEEN ? AND ?';

        $params[] = date('Y-m-d', strtotime($startDate));
        $params[] = date('Y-m-d', strtotime($endDate));
    }

    // Add ordering by project ID
    $sql .= ' ORDER BY u.id, wr.week_start';

    // Prepare and execute the SQL query
    try {
        $query = $conn->prepare($sql);
        $query->execute($params);

        // Fetch all the results
        $sql = $query->fetchAll(PDO::FETCH_ASSOC);
        // Process the results
        $users = [];
        foreach ($sql as $row) {
            $users[$row['id']]['roster_id'] = $row['roster_id'];
            $users[$row['id']]['name'] = $row['name'];
            $users[$row['id']]['role_name'] = $row['role_name'];
            $users[$row['id']]['profile'] = $row['profile'] == '' ? 'assets/img/users/user-32.jpg' : $row['profile'];
            $users[$row['id']]['shifts'][] = [
                'week_start' => $row['week_start'],
                'week_end' => $row['week_end'],
                'start_time' => date("h:i A", strtotime($row['start_time'])),
                'end_time' => date("h:i A", strtotime($row['end_time']))
            ];
        }

        // Loop through the results and display the roster details
        http_response_code(200);
        foreach ($users as $userId => $user) {
            echo '<tr>
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center file-name-icon">
                        <a href="#" class="avatar avatar-md border avatar-rounded">
                            <img src="' . htmlspecialchars($user['profile']) . '" class="img-fluid" alt="img">
                        </a>
                        <div class="ms-2">
                            <h6 class="fw-medium"><a href="#">' . htmlspecialchars($user['name']) . '</a></h6>
                        </div>
                    </div>
                </td>
                <td>' . htmlspecialchars($user['role_name']) . '</td>
                <td>
                    <div>';
                        foreach ($user['shifts'] as $shift) {
                            echo '<p class="mb-0">
                                ' . date("d-m-Y", strtotime($shift['week_start'])) . ' - ' . date("d-m-Y", strtotime($shift['week_end'])) . '<br>
                                ' . htmlspecialchars($shift['start_time']) . ' - ' . htmlspecialchars($shift['end_time']) . '
                            </p>';
                        }
            echo '</div>
                </td>
                <td>
                    <div>
                        <a href="#" class="btn btn-dark" onclick="getDelete(' . htmlspecialchars($user['roster_id']) . ')">Delete</a>
                    </div>
                </td>
            </tr>';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
