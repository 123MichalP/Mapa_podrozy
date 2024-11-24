<?php
include 'db_config.php'; // Połączenie z bazą danych

// Pobranie danych z POST
$group_id = $_POST['group_id'];

// Rozpoczęcie transakcji
$conn->begin_transaction();

try {
    // Usuń wszystkie miejsca przypisane do grupy
    $stmt_places = $conn->prepare("DELETE FROM places WHERE group_id = ?");
    $stmt_places->bind_param("i", $group_id);
    $stmt_places->execute();

    // Usuń grupę
    $stmt_group = $conn->prepare("DELETE FROM groups WHERE id = ?");
    $stmt_group->bind_param("i", $group_id);
    $stmt_group->execute();

    // Zatwierdzenie transakcji
    $conn->commit();

    echo json_encode(["success" => true]); // Sukces
} catch (Exception $e) {
    // Wycofanie transakcji w przypadku błędu
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

// Zamknięcie połączenia
$conn->close();
?>
