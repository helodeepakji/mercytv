<?php

include __DIR__ . '/../settings/database/conn.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

// Set the directory containing videos
$video_directory = 'catchup/recordings/channel1/'; 
$directory_path = __DIR__ . '/../' . $video_directory;

// Check if directory exists
if (!is_dir($directory_path)) {
    http_response_code(404);
    echo json_encode(["error" => "Video directory not found"]);
    exit;
}

// Get video files (MP4, AVI, MKV, etc.)
$video_files = array_values(array_filter(scandir($directory_path), function ($file) use ($directory_path) {
    return is_file($directory_path . '/' . $file) && preg_match('/\.(mp4|avi|mkv|mov|flv|wmv)$/i', $file);
}));

// Generate full URLs and fetch video details from the database
$base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/$video_directory";
$video_list = array_reverse(array_filter(array_map(function ($file) use ($base_url, $conn) {
    preg_match('/^(\d+)_/', $file, $matches); // Extract video ID
    $video_id = $matches[1] ?? null;

    $program = null;
    if ($video_id) {
        $query = $conn->prepare("SELECT * FROM program WHERE id = ?");
        $query->execute([$video_id]);
        $program = $query->fetch(PDO::FETCH_ASSOC);

        if ($program) {
            $program_datetime = new DateTime($program['date'] . ' ' . $program['time']);
            $program_datetime->modify("+{$program['duration']} minutes"); // Add duration
            
            $current_datetime = new DateTime(); // Current time

            if ($current_datetime <= $program_datetime) {
                return null;
            }
        }
    }

    return [
        "video_id" => $video_id,
        "name" => $file,
        "url" => $base_url . $file,
        "program" => $program ?: null
    ];
}, $video_files)));

// Return JSON response
echo json_encode($video_list, JSON_PRETTY_PRINT);

?>
