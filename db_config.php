<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_map";

// Utwórz połączenie
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdź połączenie
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
