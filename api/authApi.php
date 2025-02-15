<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . '/../settings/database/conn.php';
include __DIR__ . '/../settings/smtp/mailfy.php';

require_once '../jwt/src/BeforeValidException.php';
require_once '../jwt/src/ExpiredException.php';
require_once '../jwt/src/SignatureInvalidException.php';
require_once '../jwt/src/JWT.php';
require_once '../jwt/src/Key.php';

use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'helodeepakji';

function sendOtp($email, $name, $conn)
{
    // $otp = rand(1000, 9999);

    $otp = 1234;

    $check = $conn->prepare("INSERT INTO `login`(`otp`, `email`) VALUES (?, ?)");
    $check->execute([$otp, $email]);

    // $html_content = '
    //     <!DOCTYPE html>
    //     <html lang="en">
    //     <head>
    //         <meta charset="UTF-8">
    //         <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //         <title>Thank You</title>
    //         <style>
    //             body {
    //                 font-family: Arial, sans-serif;
    //                 line-height: 1.6;
    //                 background-color: #f4f4f4;
    //                 margin: 0;
    //                 padding: 0;
    //             }
    //             .container {
    //                 max-width: 600px;
    //                 margin: 0 auto;
    //                 padding: 20px;
    //                 background: #fff;
    //                 border-radius: 5px;
    //                 box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    //             }
    //             h2 {
    //                 color: #333;
    //             }
    //             p {
    //                 color: #666;
    //             }
    //             .logo {
    //                 max-width: 200px;
    //             }
    //         </style>
    //     </head>
    //     <body>
    //         <div class="container">
    //             <img src="https://ott.mercytv.tv/assets/img/LOGO.png" alt="Company Logo" class="logo">
    //             <h2>MercyTV OTT</h2>
    //             <p><strong>Dear </strong>' . $name . '</p>
    //             <p>Your OTP is ' . $otp . '.<br>We appreciate your interest in our services/products. Our team will review your inquiry and get back to you as soon as possible.</p>
    //             <p>If you have any further questions or concerns, please feel free to contact us.</p>
    //             <p>Best regards,<br>MercyTV OTT</p>
    //         </div>
    //     </body>
    //     </html>
    // ';

    // $modive = 'OTP For Login MercyTV OTT';

    // smtp_mailer($email, $modive, $html_content);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'createAccount') {
    if ($_POST['phone'] != '' || $_POST['email'] != '') {

        if ($_POST['phone'] != '') {
            $sql = $conn->prepare("INSERT INTO `users`(`phone`, `is_active`, `is_verify_number`) VALUES (? ,? , ? )");
            $result = $sql->execute([$_POST['phone'], 1, 1]);
            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Account is successfull created."]);
                exit;
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Something went wrong."]);
                exit;
            }
        }

        if ($_POST['email'] != '') {

            $check = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
            $check->execute([$_POST['email']]);
            $check = $check->fetch(PDO::FETCH_ASSOC);
            if ($check) {

                sendOtp($check['email'], $check['name'], $conn);
                http_response_code(200);
                echo json_encode(["message" => "Otp Send Successfully. Please Verify Mail.", "email" => $_POST['email'], "data" => "email"]);
            } else {
                $username = strstr($_POST['email'], '@', true);
                $sql = $conn->prepare("INSERT INTO `users`(`name`,`email`, `is_active`, `is_verify_email`) VALUES (? ,? , ?, ?)");
                $result = $sql->execute([$username, $_POST['email'], 0, 0]);
                if ($result) {
                    sendOtp($_POST['email'], $username, $conn);
                    http_response_code(200);
                    echo json_encode(["message" => "Otp Send Successfully. Please Verify Mail.", "email" => $_POST['email'], "data" => "email"]);
                    exit;
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Something went wrong."]);
                    exit;
                }
            }
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Phone no or Email Id is required to login."]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type'] == 'verifyOtp') {
    if ($_POST['otp'] != '' || $_POST['data'] != '' || $_POST['value'] != '') {

        if ($_POST['data'] == 'email') {
            $sql = $conn->prepare("SELECT * FROM `login` WHERE `email` = ? ORDER BY `login`.`created_at` DESC");
            $sql->execute([$_POST['value']]);
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                if ($_POST['otp'] == $sql['otp']) {

                    $check = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
                    $check->execute([$_POST['email']]);
                    $check = $check->fetch(PDO::FETCH_ASSOC);

                    if(!$check){
                        http_response_code(400);
                        echo json_encode(["error" => "User is not exists."]);
                        exit;
                    }

                    $payload = [
                        'email' => $check['email'],
                        'phone' => $check['phone'],
                        'id' =>  $check['id'],
                        'exp' => time() + (30 * 24 * 60 * 60)
                    ];

                    $jwt = JWT::encode($payload, $secretKey, 'HS256');
                    echo json_encode(array("message" => "Login Successfull", "data" => ['auth_token' => $jwt]));

                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Otp is incorrect. Please check otp."]);
                    exit;
                }
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Something went wrong."]);
                exit;
            }
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Fill All Required Fields."]);
        exit;
    }
}
