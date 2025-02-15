<?php
// require 'vendor/autoload.php';
require_once 'src/BeforeValidException.php';
require_once 'src/ExpiredException.php';
require_once 'src/SignatureInvalidException.php';
require_once 'src/JWT.php';
include "../admin/settings/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if($_POST['email']){
        $check = $conn->prepare("SELECT `id`, `email`, `phone` , `user_type` ,`name`  FROM `users` WHERE `email` = ?");
        $check->execute([$_POST['email']]);
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($result){
            $usertype = $result['user_type'];
            $user_id = $result['id'];
        }else{
            http_response_code(400);
            echo json_encode(array("message" => 'User not exist', "status" => 400));
            die();
        }
    }else{
        http_response_code(400);
        echo json_encode(array("message" => 'Phone is not recived', "status" => 400));
        die();
    }
}else{
    die();
}

use \Firebase\JWT\JWT;

// Your secret key to sign the token (keep this secret)
$secretKey = 'your_secret_key';

// Sample payload data (you can customize this as per your requirements)
$payload = [
    'user_id' => $user_id,
    'role' => $usertype,
    'exp' => time() + (30 * 24 * 60 * 60) // Token expiration time (1 hour from now)
];

// Generate the JWT
$jwt = JWT::encode($payload, $secretKey, 'HS256');

// Output the JWT
echo $jwt;
