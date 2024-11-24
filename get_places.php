<?php
// Połączenie z bazą danych
require 'db_config.php';

$groupIds = json_decode($_POST['group_ids'], true);

$groupFilter = '';
if (!empty($groupIds)) {
    $groupFilter = 'WHERE group_id IN (' . implode(',', array_map('intval', $groupIds)) . ')';
}

// Pobieranie miejsc z kolorami grup
$sql = "SELECT places.*, groups.color AS group_color 
        FROM places 
        JOIN groups ON places.group_id = groups.id 
        $groupFilter";

$result = $conn->query($sql);

$places = [];
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

echo json_encode($places);
?>
