<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pobranie danych użytkownika
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

// Pobranie grup użytkownika
$stmt = $conn->prepare("SELECT id, name, color FROM groups WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$groups_result = $stmt->get_result();
$groups = [];
while ($row = $groups_result->fetch_assoc()) {
    $groups[] = $row;
}

// Pobranie znaczników (places) dla każdej grupy
$places_by_group = [];
foreach ($groups as $group) {
    $stmt_places = $conn->prepare("SELECT name, description, latitude, longitude FROM places WHERE group_id = ?");
    $stmt_places->bind_param("i", $group['id']);
    $stmt_places->execute();
    $places_result = $stmt_places->get_result();
    $places = [];
    while ($place = $places_result->fetch_assoc()) {
        $places[] = $place;
    }
    $places_by_group[$group['id']] = $places;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <link rel="stylesheet" href="style.css"/>
    <meta charset="UTF-8">
    <title>Panel użytkownika</title>
</head>
<body>
    <h1>Panel użytkownika</h1>
    <p>Imię: <?php echo htmlspecialchars($user['username']); ?></p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <h2>Twoje grupy wskaźników:</h2>
    <ul>
    <?php foreach ($groups as $group): ?>
        <li>
            <strong 
                class="delete-group" 
                data-group-id="<?php echo $group['id']; ?>" 
                style="color: <?php echo htmlspecialchars($group['color']); ?>; text-decoration: none; cursor: pointer;">
                <?php echo htmlspecialchars($group['name']); ?>
            </strong>

            <!-- Wyświetlanie znaczników w grupie -->
            <?php if (!empty($places_by_group[$group['id']])): ?>
                <ul>
                <?php foreach ($places_by_group[$group['id']] as $place): ?>
                <li>
                    <strong 
                        data-lat="<?php echo htmlspecialchars($place['latitude']); ?>" 
                        data-lng="<?php echo htmlspecialchars($place['longitude']); ?>">
                        <?php echo htmlspecialchars($place['name']); ?>
                    </strong>: 
                    <?php echo htmlspecialchars($place['description']); ?>
                </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Brak znaczników w tej grupie.</em></p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>



    <!-- Formularz tworzenia grupy -->
    <h2>Utwórz nową grupę:</h2>
    <form method="POST" action="create_group.php">
        <input type="text" name="group_name" placeholder="Nazwa grupy" required>
        <label for="group_color">Wybierz kolor:</label>
        <select name="group_color" id="group_color" required>
            <option value="red">Czerwony</option>
            <option value="blue">Niebieski</option>
            <option value="green">Zielony</option>
        </select>
        <button type="submit">Utwórz grupę</button>
    </form>

    <p>
        <button onclick="location.href='map.php'">Powrót do mapy</button>
        <button onclick="location.href='logout.php'">Wyloguj się</button>
    </p>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Obsługa usuwania grup po kliknięciu na nazwę
    const groupElements = document.querySelectorAll('.delete-group');

    groupElements.forEach(group => {
        group.addEventListener('click', function () {
            const groupName = this.textContent;
            const groupId = this.dataset.groupId;

            if (confirm(`Czy na pewno chcesz usunąć grupę "${groupName}" wraz ze wszystkimi znacznikami?`)) {
                fetch('delete_group.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `group_id=${encodeURIComponent(groupId)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Usuń element grupy z DOM
                        this.parentElement.remove();
                        alert('Grupa została usunięta.');
                    } else {
                        alert('Wystąpił błąd podczas usuwania grupy.');
                    }
                })
                .catch(() => {
                    alert('Nie udało się połączyć z serwerem.');
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Obsługa usuwania znaczników po kliknięciu na nazwę
    const markerElements = document.querySelectorAll(' ul ul > li strong');

    markerElements.forEach(marker => {
        marker.addEventListener('click', function () {
            const markerName = this.textContent;
            const latitude = this.dataset.lat;
            const longitude = this.dataset.lng;        

            if (confirm(`Czy na pewno chcesz usunąć znacznik "${markerName}" ?`)) {
                fetch('delete_place.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Usuń element grupy z DOM
                        this.parentElement.remove();
                        alert('Znacznik została usunięty.');
                    } else {
                        alert('Wystąpił błąd podczas usuwania Znacznika.');
                    }
                })
                .catch(() => {
                    alert('Nie udało się połączyć z serwerem.');
                });
            }
        });
    });
});
    </script>
</body>
</html>
