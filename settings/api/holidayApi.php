<?php
    include __DIR__ . '/../database/conn.php';
    header("content-Type: application/json");

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'updateHoliday')) {

        $id = $_POST['holiday_id'];
        $newDate = $_POST['date'];
        $newDate = date('Y-m-d', strtotime($newDate));
        $newSummary = $_POST['name'];
        if(isset($_FILES["image"]["name"]) && $_FILES["image"]["name"] != ''){
            $image = basename($_FILES["image"]["name"]);
            $uploadPath = '../../assets/images/holiday/' . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
            $sql = $conn->prepare("UPDATE `holiday` SET `image` = ? WHERE `id` = ?");
            $result = $sql->execute([$image, $id]);
        }    

        $sql = $conn->prepare("UPDATE `holiday` SET `date` = ?, `holiday` = ? WHERE `id` = ?");
        $result = $sql->execute([$newDate, $newSummary, $id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Holiday updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update holiday']);
        }

    }
    
    if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['type'] == 'addHoliday')) {

        $newDate = $_POST['date'];
        $newDate = date('Y-m-d', strtotime($newDate));
        $newSummary = $_POST['name'];
        if($_FILES["image"]["name"]){
            $image = basename($_FILES["image"]["name"]);
            $uploadPath = '../../assets/images/holiday/' . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        }else{
            $image = null;
        }
    

        $sql = $conn->prepare("INSERT INTO `holiday`(`holiday`, `date`, `image`) VALUES (? , ? , ? )");
        $result = $sql->execute([$newSummary, $newDate,  $image]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Holiday Add successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update holiday']);
        }

    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['type'] == 'getHoliday') {
        $id = $_GET['id'];
        $sql = $conn->prepare("SELECT * FROM `holiday` WHERE `id` = ?");
        $sql->execute([$id]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete holiday']);
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['type'] == 'deleteHoliday') {
        $id = $_POST['id'];
    
        $sql = $conn->prepare("DELETE FROM `holiday` WHERE `id` = ?");
        $result = $sql->execute([$id]);
        
    
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Holiday deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete holiday']);
        }
    }
?>