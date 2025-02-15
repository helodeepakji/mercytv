<?php
// Include the Composer autoloader to load the JWT library
// require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'src/BeforeValidException.php';
require_once 'src/ExpiredException.php';
require_once 'src/SignatureInvalidException.php';
require_once 'src/JWT.php';
require_once 'src/Key.php';
include '../settings/conn.php';

use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;


require '../pushNotification/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

function sendFCMNotification($deviceToken, $title, $body, $imageUrl, $data = [])
{
    // Path to your service account JSON file
    $serviceAccountPath = '/var/www/html/jwt/onatrip.json';

    // Initialize Firebase Factory
    $factory = (new Factory)->withServiceAccount($serviceAccountPath);

    // Get the Messaging instance from the factory
    $messaging = $factory->createMessaging();

    // Create the notification with an image
    $notification = Notification::create($title, $body)
        ->withImageUrl($imageUrl);

    // Create the message with the target token, notification, and optional data
    $message = CloudMessage::withTarget('token', $deviceToken)
        ->withNotification($notification)
        ->withData($data);

    // Send the message
    $response = $messaging->send($message);

    // Return the response
    return $response;
}

// Your secret key (should be the same as the one used for signing the token)
$secretKey = 'helodeepakji';

$auth = false;

// JWT received from the client (in this example, we assume it's passed via the 'Authorization' header)
$jwtFromClient = null;
$headers = apache_request_headers();
if (isset($headers['Authorization']) || isset($headers['authorization'])) {
    $authHeader = $headers['Authorization'] ?? $headers['authorization'];
    // Check if the Authorization header starts with 'Bearer '
    if (substr($authHeader, 0, 7) === 'Bearer ') {
        $jwtFromClient = substr($authHeader, 7);
    }
}

try {
    if ($jwtFromClient) {
        // Attempt to decode the JWT
        // $decodedToken = JWT::decode($jwtFromClient,array($secretKey,'HS256'));
        $decoded = JWT::decode($jwtFromClient, new Key($secretKey, 'HS256'));


        // Token is valid
        // Access the payload data as an associative array
        $authUserId = $decoded->id;
        $email = $decoded->email;
        $usertype = $decoded->usertype;
        $exp = $decoded->exp;


        $check = $conn->prepare("SELECT `id` , `name` , `email` , `phone` , `profile` , `user_type` , `fcm_token` , `credit` , `membership` FROM `users` WHERE `id` = ? AND `user_type` = ?");
        $check->execute([$authUserId, $usertype]);
        $authData = $check->fetch(PDO::FETCH_ASSOC);

        if ($authData) {
            switch ($authData['user_type']) {
                case 'transporter':
                    $data = $conn->prepare("SELECT * FROM `transpoter_details` WHERE `user_id` = ?");
                    $data->execute([$authData['id']]);
                    $data = $data->fetch(PDO::FETCH_ASSOC);
                    $authData['other_details'] = $data;
                    break;

                case 'hotelier':
                    $data = $conn->prepare("SELECT * FROM `hotelier_details` WHERE `user_id` = ?");
                    $data->execute([$authData['id']]);
                    $data = $data->fetch(PDO::FETCH_ASSOC);
                    $authData['other_details'] = $data;
                    break;

                case 'travel_agent':
                    $data = $conn->prepare("SELECT * FROM `agent_details` WHERE `user_id` = ?");
                    $data->execute([$authData['id']]);
                    $data = $data->fetch(PDO::FETCH_ASSOC);
                    $authData['other_details'] = $data;
                    break;

                default:
                    $authData['other_details'] = false;
                    break;
            }

            $membership = $conn->prepare("SELECT * FROM `MembershipPackages` WHERE `id` = ?");
            $membership->execute([$authData['membership']]);
            $membership = $membership->fetch(PDO::FETCH_ASSOC);
            $authData['membership'] = $membership;

            $authData['other_details']['aadhar_card'] = $authData['other_details']['aadhar_card'] != '' ? "**** **** " . substr($authData['other_details']['aadhar_card'], -4) : '';

            $authData['other_details']['pan_card'] = $authData['other_details']['pan_card'] != '' ? "****** " . substr($authData['other_details']['pan_card'], -4) : '';

            http_response_code(200);
            $auth = true;
        } else {
            http_response_code(401);
            echo json_encode(array("isSuccess" => false, "successMessage" => null, "errorMessage"  => 'user not exist', "data" => null));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("isSuccess" => false, "successMessage" => null, "errorMessage"  => "Token not found", "data" => null));
    }
} catch (ExpiredException $e) {

    // print_r($e);

    http_response_code(200);
    echo json_encode(array("isSuccess" => false, "successMessage" => null, "errorMessage"  => "Expired token", "data" => null));
} catch (Exception $e) {

    // print_r($e);

    http_response_code(401);
    echo json_encode(array("isSuccess" => false, "successMessage" => null, "errorMessage"  => "Invalid token", "data" => null));
}

if (!$auth) {
    die();
}
