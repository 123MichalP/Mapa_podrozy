<?php
// Uruchomienie sesji
session_start();

// Połączenie z bazą danych
include 'db_config.php';

// Odbieranie danych z formularza
if (isset($_POST['group_name'], $_POST['group_color'])) {
    $groupName = $_POST['group_name'];
    $groupColor = $_POST['group_color'];

    // Sprawdzenie, czy użytkownik jest zalogowany
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Użytkownik nie jest zalogowany.']);
        exit;
    }

    $userId = $_SESSION['user_id']; // Pobranie ID użytkownika

    if (empty($groupName) || empty($groupColor)) {
        echo json_encode(['status' => 'error', 'message' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    // Zapytanie do bazy danych
    $sql = "INSERT INTO groups (name, color, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $groupName, $groupColor, $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Błąd podczas tworzenia grupy.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Brak wymaganych danych.']);
}
?>
