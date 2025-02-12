<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'funkcjonariusz'){
    exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'];
$db = getDB();
$stmt = $db->prepare("SELECT * FROM messages WHERE call_sign = ? ORDER BY timestamp DESC");
$stmt->execute([$call_sign]);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo '<div style="padding:5px; margin:5px; border-bottom:1px solid #ccc;">';
    echo '<strong>Od: </strong>' . htmlspecialchars($row['sender']) . '<br>';
    echo '<strong>Treść: </strong>' . htmlspecialchars($row['content']);
    echo '</div>';
}
?>
