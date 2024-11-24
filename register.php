<?php
include 'db_config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php?registered=true");
        exit();
    } else {
        echo "Wystąpił błąd podczas rejestracji.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Rejestracja</h2>
    <form action="register.php" method="POST">
        <label>Nazwa użytkownika:</label>
        <input type="text" name="username" required>
        <label>E-mail:</label>
        <input type="email" name="email" required>
        <label>Hasło:</label>
        <input type="password" name="password" required>
        <button type="submit">Zarejestruj się</button>
    </form>
    <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
</body>
</html>
