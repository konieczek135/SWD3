<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    exit;
}
include 'common.php';
$db = getDB();
$stmt = $db->query("SELECT * FROM interventions ORDER BY timestamp DESC");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $color = $row['urgency'] ? 'red' : 'inherit';
    echo '<div class="item" data-id="'.$row['id'].'" style="background-color:'.$color.'; padding:5px; margin:5px; cursor:pointer;">';
    echo '<strong>Patrol:</strong> '.htmlspecialchars($row['call_sign']).'<br>';
    echo '<strong>Typ:</strong> '.htmlspecialchars($row['type']).'<br>';
    echo '<strong>Lokalizacja:</strong> '.htmlspecialchars($row['location']).'<br>';
    echo '<strong>Zgłaszający:</strong> '.htmlspecialchars($row['reporter']);
    echo '</div>';
}
?>
