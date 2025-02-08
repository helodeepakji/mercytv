<?php
include __DIR__ . '/../database/conn.php';
session_start();
header("Access-Control-Allow-Origin: *");
$user_id = $_SESSION['userId'];

function validateAndFormatDate($date)
{
    $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d']; // Possible date formats
    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $d->format('Y-m-d'); // Return in standard format
        }
    }
    return false; // Invalid date
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'FilterEmployee') {
    header("Content-Type: text/html");

    $status = $_POST['status'] ?? '';

    // Start building the SQL query
    $sql = "SELECT * FROM `users` WHERE 1 = 1";
    $params = [];

    if (!empty($status)) {
        $sql .= ' AND `is_active` = ?';
        $params[] = $status;
    }else{
        $sql .= ' AND `is_active` = 0';
    }

    $sql .= ' ORDER BY `users`.`name` ASC';

    // Prepare and execute the SQL query
    try {
        $query = $conn->prepare($sql);
        $query->execute($params);

        // Fetch all the results
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        // Loop through the results and display the project details
        http_response_code(200);
        foreach ($users as $user) {

            echo '
            <tr>
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </td>
                <td><a
                        href="employee-details.php?id=' . base64_encode($user['id']) . '">' . $user['employee_id'] . '</a>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <a href="employee-details.php?id=' . base64_encode($user['id']) . '"
                            class="avatar avatar-md" data-bs-toggle="modal"
                            data-bs-target="#view_details"><img
                                src="assets/img/users/user-32.jpg"
                                class="img-fluid rounded-circle" alt="img"></a>
                        <div class="ms-2">
                            <p class="text-dark mb-0"><a
                                    href="employee-details.php?id=' . base64_encode($user['id']) . '"
                                    data-bs-toggle="modal"
                                    data-bs-target="#view_details">' . $user['name'] . '</a>
                            </p>
                        </div>
                    </div>
                </td>
                <td>' . ucfirst($user['gender']) . '</td>
                <td>' . $user['email'] . '</td>
                <td>' . $user['phone'] . '</td>
                <td>' . date('d M, Y h:i A', strtotime($user['created_at'])) . '</td>
                <td>
                    <span
                        class="badge badge-' . ($user['is_active'] == 0 ? 'danger' : 'success') . ' d-inline-flex align-items-center badge-xs">
                        <i
                            class="ti ti-point-filled me-1"></i>' . ($user['is_active'] == 1 ? 'Active' : 'Inactive') . '
                    </span>
                </td>
                <td><div class="action-icon d-inline-flex">
                        <a href="#" class="me-2" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"
                            onclick="getEmployee(' . $user['id'] . ')"><i
                                class="ti ti-edit"></i></a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"
                            onclick="deleteEmployee(' . $user['id'] . ')"><i
                                class="ti ti-trash"></i></a>
                    </div>
                </td>
            </tr>';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] == "addEmployee") {

    // Required fields
    $requiredFields = ['name', 'phone', 'email', 'gender', 'password', 'cpassword'];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['message' => "The field '{$field}' is required.", "status" => 400]);
            exit;
        }
    }

    // Extract and sanitize data
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $gender = trim($_POST['gender']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Password validation
    if ($password !== $cpassword) {
        http_response_code(400);
        echo json_encode(['message' => 'Password and Confirm Password do not match.', "status" => 400]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if user already exists
        $check = $conn->prepare("SELECT * FROM `users` WHERE phone = ? OR email = ?");
        $check->execute([$phone, $email]);

        if ($check->fetch(PDO::FETCH_ASSOC)) {
            http_response_code(400);
            echo json_encode(['message' => 'User already exists. Please check Phone No or Email.', "status" => 400]);
            exit;
        }

        // Insert new employee
        $sql = $conn->prepare("
            INSERT INTO `users` (`name`, `phone`, `email`, `gender`, `password`, `is_active`, `is_verify_email`, `is_verify_number`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $result = $sql->execute([$name, $phone, $email, $gender, $hashedPassword, 1, 1, 1]);

        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => 'User added successfully!', 'status' => 200]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Something went wrong while adding the User.', 'status' => 500]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage(), 'status' => 500]);
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['type'] == "changePassword")) {
    if ($_POST['password'] != '' && $_POST['cpassword'] != '') {

        if ($_POST['password'] == $_POST['cpassword']) {
            http_response_code(500);
            echo json_encode(['message' => 'Password And Confirm Password is not same.', 'status' => 500]);
            exit;
        }

        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $result = $sql->execute([$password, $user_id]);

        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => 'User added successfully!', 'status' => 200]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Something went wrong while adding the user.', 'status' => 500]);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Fill All Required Fields.', 'status' => 500]);
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['type'] == "getEmployee")) {
    if ($_GET['id'] == '') {
        http_response_code(400);
        echo json_encode(['message' => "The field id is required.", "status" => 400]);
        exit;
    }
    $check = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $check->execute([$_GET['id']]);
    $result = $check->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Employee not found!', 'status' => 400]);
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['type'] == "editEmployee")) {
    // Required fields
    $requiredFields = ['name', 'phone', 'email', 'gender', 'id'];

    // Check for missing fields
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['message' => "The field '{$field}' is required.", "status" => 400]);
            exit;
        }
    }

    // Extract data
    $name = $_POST['name'];
    $is_active = $_POST['status'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $gender = $_POST['gender'];
    $id = $_POST['id'];

    if ($password !== $cpassword) {
        http_response_code(400);
        echo json_encode(['message' => 'Password and Confirm Password do not match.', "status" => 400]);
        exit;
    }

    $check = $conn->prepare("SELECT * FROM `users` WHERE phone = ? OR email = ?");
    $check->execute([$phone, $email]);
    if (!$check->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode(['message' => 'User not exists. Please check.', "status" => 400]);
        exit;
    }

    // Insert new employee
    $sql = $conn->prepare("
        UPDATE `users`
        SET 
            `name` = ?,
            `phone` = ?, 
            `email` = ?, 
            `is_active` = ?,
            `gender` = ?, 
            `password` = ?
        WHERE `id` = ?
    ");
    $result = $sql->execute([
        $name,
        $phone,
        $email,
        $is_active,
        $gender,
        $cpassword,
        $id
    ]);

    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully!', 'status' => 200]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Something went wrong while adding the User.', 'status' => 500]);
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['type'] == "deleteEmployee")) {
    if ($_GET['user_id'] == '') {
        http_response_code(400);
        echo json_encode(['message' => "The field id is required.", "status" => 400]);
        exit;
    }

    $delete = $conn->prepare('DELETE FROM `users` WHERE `id` = ?');
    $result = $delete->execute([$_GET['user_id']]);
    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => "Successfull Delete.", "status" => 400]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['message' => "User not found.", "status" => 400]);
        exit;
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['type'] == "changeStatus")) {
    if ($_GET['user_id'] == '') {
        http_response_code(400);
        echo json_encode(['message' => "The field id is required.", "status" => 400]);
        exit;
    }

    $check = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $check->execute([$_GET['user_id']]);
    $result = $check->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        http_response_code(400);
        echo json_encode(['message' => 'User not exists. Please check.', "status" => 400]);
        exit;
    }
    $status = $result['is_terminated'] == 1 ? 0 : 1;
    $user = $conn->prepare('UPDATE `users` SET `is_terminated` = ? WHERE `id` = ?');
    $result = $user->execute([$status, $_GET['user_id']]);
    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => "Successfull Change Status.", "status" => 400]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['message' => "Employee not found.", "status" => 400]);
        exit;
    }
}
