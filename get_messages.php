<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    exit;
}
include 'common.php';
$db = getDB();
$stmt = $db->query("SELECT * FROM messages ORDER BY timestamp DESC");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo '<div class="item" data-id="'.$row['id'].'" style="padding:5px; margin:5px; cursor:pointer;">';
    echo '<strong>Patrol:</strong> '.htmlspecialchars($row['call_sign']).'<br>';
    echo '<strong>Od:</strong> '.htmlspecialchars($row['sender']).'<br>';
    echo '<strong>Treść:</strong> '.htmlspecialchars($row['content']);
    echo '</div>';
}
?>
