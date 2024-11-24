<?php
include 'db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            echo "Nieprawidłowe hasło.";
        }
    } else {
        echo "Użytkownik nie istnieje.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Logowanie</h2>
    <form action="login.php" method="POST">
        <label>Nazwa użytkownika:</label>
        <input type="text" name="username" required>
        <label>Hasło:</label>
        <input type="password" name="password" required>
        <button type="submit">Zaloguj się</button>

        
    </form>
    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</body>
</html>
