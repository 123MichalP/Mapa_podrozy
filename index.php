<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Witaj na Interaktywnej Mapie Podróży!</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Zalogowany jako: <?php echo $_SESSION['role'] === 'admin' ? 'Administrator' : 'Użytkownik'; ?></p>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_panel.php">Przejdź do panelu administratora</a>
        <?php else: ?>
            <button onclick="location.href='user_panel.php'">Przejdź do panelu użytkownika</button>
        <?php endif; ?>
        
        <br>
        <button onclick="location.href='map.php'">Przejdź do mapy</button>

        <br>
        <button  onclick="location.href='logout.php'">Wyloguj się</button>
    <?php else: ?>
        <button onclick="location.href='register.php'">Zarejestruj się</button> <br> <button onclick="location.href='login.php'">Zaloguj się</button>
    <?php endif; ?>
</body>
</html>
