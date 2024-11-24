<?php
session_start();
require 'db_config.php';

// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pobieranie grup użytkownika
$stmt = $conn->prepare("SELECT id, name, color FROM groups WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$groups_result = $stmt->get_result();
$groups = [];
while ($row = $groups_result->fetch_assoc()) {
    $groups[] = $row;
}

// Pobranie danych użytkownika
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Mapa podróży - Mapa</title>
    <link rel="stylesheet" href="style.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
    <h1>Mapa podróży</h1>

    <!-- Wylogowanie i panel użytkownika -->
    <div class="user-panel">
    <p>Witaj, <?php echo htmlspecialchars($user['username']); ?>!</p>
    <p>
        <button onclick="location.href='user_panel.php'">Przejdź do panelu użytkownika</button><br><br>
        <button onclick="location.href='logout.php'">Wyloguj się</button>
    </p>
    </div>



    <!-- Sekcja z check-boxami do wyboru grup -->
    <h2>Wybierz grupy do wyświetlenia:</h2>
    <form id="group-selection-form">
    <?php foreach ($groups as $group): ?>
        <label>
            <input type="checkbox" class="group-checkbox" value="<?php echo $group['id']; ?>" data-color="<?php echo htmlspecialchars($group['color']); ?>" unchecked>
            <?php echo htmlspecialchars($group['name']); ?>
        </label><br>
    <?php endforeach; ?>
    </form>

    <!-- Mapa -->
    <div id="map"></div>

    <!-- Formularz dodawania nowego miejsca -->
    <form id="place-form">
        <label for="place-name">Nazwa miejsca:</label>
        <input type="text" id="place-name" required>
        
        <label for="place-description">Opis:</label>
        <textarea id="place-description" required></textarea>
        

        <label for="place-group">Wybierz grupę:</label>
        <select id="place-group" required>
            <?php foreach ($groups as $group): ?>
                <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <button type="button" id="save-place">Zapisz</button>
    </form>

        <!-- Tworzenie grupy -->
    <h2>Tworzenie nowej grupy:</h2>
    <form id="create-group-form">
    <label for="group-name">Nazwa grupy:</label>
    <input type="text" id="group-name" name="group_name" required>

    <label for="group-color">Kolor grupy:</label>
    <select id="group-color" name="group_color">
        <option value="red">Czerwony</option>
        <option value="blue">Niebieski</option>
        <option value="green">Zielony</option>
    </select>

    <button type="button" id="create-group-btn">Utwórz grupę</button>
    </form>

</body>
</html>