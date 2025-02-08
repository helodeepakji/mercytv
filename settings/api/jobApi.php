<?php

include __DIR__ . '/../database/conn.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
session_start();

$user_id = $_SESSION['userId'];
$role_id = $_SESSION['roleId'];

$requestMethod = $_SERVER['REQUEST_METHOD'];
$type = $_REQUEST['type'] ?? '';

switch ($type) {

    case 'addJob':
        if ($requestMethod == 'POST') {
            $jobTitle = $_POST['job_title'] ?? '';
            $roleId = $_POST['role_id'] ?? '';
            $vacancies = $_POST['vacancies'] ?? '';
            $desc = $_POST['desc'] ?? '';
            $minSalary = $_POST['min_salary'] ?? '';

            if (!empty($jobTitle) && !empty($roleId) && !empty($desc) && !empty($minSalary)) {
                $stmt = $conn->prepare("INSERT INTO `job` (`job-title`, `role_id`, `desc`, `min_salary`, `status`, `vacancies`) VALUES (?, ?, ?, ?, 1, ?)");
                $result = $stmt->execute([$jobTitle, $roleId, $desc, $minSalary, $vacancies]);

                if ($result) {
                    http_response_code(201);
                    echo json_encode(["message" => "Job added successfully", "status" => 201]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to add job", "status" => 500]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "All fields are required", "status" => 400]);
            }
        }
        break;

    case 'updateJob':
        if ($requestMethod == 'POST') {
            $jobId = $_POST['id'] ?? '';
            $jobTitle = $_POST['job_title'] ?? '';
            $roleId = $_POST['role_id'] ?? '';
            $vacancies = $_POST['vacancies'] ?? '';
            $desc = $_POST['desc'] ?? '';
            $minSalary = $_POST['min_salary'] ?? '';
            $status = $_POST['status'] ?? '';

            if (!empty($jobId) && !empty($jobTitle) && !empty($roleId) && !empty($desc) && !empty($minSalary)) {
                $stmt = $conn->prepare("UPDATE `job` SET `job-title` = ?, `vacancies` = ? , `role_id` = ?, `desc` = ?, `min_salary` = ? , `status` = ? WHERE `id` = ?");
                $result = $stmt->execute([$jobTitle, $vacancies, $roleId, $desc, $minSalary, $status,$jobId]);

                if ($result) {
                    http_response_code(200);
                    echo json_encode(["message" => "Job updated successfully", "status" => 200]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to update job", "status" => 500]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "All fields are required", "status" => 400]);
            }
        }
        break;

    case 'activateJob':
        if ($requestMethod == 'POST') {
            $jobId = $_POST['id'] ?? '';

            if (!empty($jobId)) {
                $stmt = $conn->prepare("UPDATE `job` SET `status` = 1 WHERE `id` = ?");
                $result = $stmt->execute([$jobId]);

                if ($result) {
                    http_response_code(200);
                    echo json_encode(["message" => "Job activated successfully", "status" => 200]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to activate job", "status" => 500]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Job ID is required", "status" => 400]);
            }
        }
        break;

    case 'deactivateJob':
        if ($requestMethod == 'POST') {
            $jobId = $_POST['id'] ?? '';

            if (!empty($jobId)) {
                $stmt = $conn->prepare("UPDATE `job` SET `status` = 0 WHERE `id` = ?");
                $result = $stmt->execute([$jobId]);

                if ($result) {
                    http_response_code(200);
                    echo json_encode(["message" => "Job deactivated successfully", "status" => 200]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to deactivate job", "status" => 500]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Job ID is required", "status" => 400]);
            }
        }
        break;

    case 'getJob':
        if ($requestMethod == 'GET') {
            $jobId = $_GET['id'] ?? '';
            if (!empty($jobId)) {
                $stmt = $conn->prepare("SELECT * FROM `job` WHERE `id` = ?");
                $stmt->execute([$jobId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Job ID is required", "status" => 400]);
            }
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(["message" => "Invalid request type", "status" => 400]);
        break;
}
