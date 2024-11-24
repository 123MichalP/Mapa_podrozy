<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Zaloguj się, aby móc tworzyć grupy.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = $_POST['group_name'];
    $user_id = $_SESSION['user_id'];

    // Pobieranie grup użytkownika z kolorem
    $stmt = $conn->prepare("SELECT id, name, color FROM groups WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $groups_result = $stmt->get_result();
    $groups = [];
    while ($row = $groups_result->fetch_assoc()) {
        $groups[] = $row;
    }
    echo "Grupa $group_name została utworzona!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tworzenie grupy</title>
</head>
<body>
    <h2>Twórz nową grupę</h2>
    <form method="POST" action="groups.php">
        <input type="text" name="group_name" placeholder="Nazwa grupy" required>
        <button type="submit">Utwórz grupę</button>
    </form>
</body>
</html>
