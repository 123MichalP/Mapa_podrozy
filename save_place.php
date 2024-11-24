<?php
session_start();
require 'db_config.php';

if (isset($_POST['name'], $_POST['description'], $_POST['latitude'], $_POST['longitude'], $_POST['group_id']) && isset($_SESSION['user_id'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $user_id = $_SESSION['user_id'];
    $group_id = $_POST['group_id'];

    $stmt = $conn->prepare("INSERT INTO places (name, description, latitude, longitude, user_id, group_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddii", $name, $description, $latitude, $longitude, $user_id, $group_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu w bazie danych']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Brak wymaganych danych w odpowiedzi']);
}
?>
