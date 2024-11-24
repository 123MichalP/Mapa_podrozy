<?php
include 'db_config.php';  // Połączenie z bazą danych

// Pobranie danych z POST
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Zapytanie SQL, aby usunąć miejsce na podstawie szerokości i długości geograficznej
$sql = "DELETE FROM places WHERE latitude = ? AND longitude = ?";

// Przygotowanie zapytania
$stmt = $conn->prepare($sql);
$stmt->bind_param("dd", $latitude, $longitude);  // "dd" oznacza, że oba parametry są typu double

// Wykonanie zapytania
if ($stmt->execute()) {
    echo json_encode(["success" => true]);  // Jeśli usunięcie się udało
} else {
    echo json_encode(["success" => false]); // Jeśli wystąpił błąd
}

// Zamknięcie połączenia
$stmt->close();
$conn->close();
?>
