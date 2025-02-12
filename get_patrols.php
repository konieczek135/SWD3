<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    exit;
}
include 'common.php';
$db = getDB();
$stmt = $db->query("SELECT * FROM patrols ORDER BY created_at DESC");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    // Ustalanie koloru przycisku w zależności od statusu
    $color = 'grey';
    switch($row['status']){
         case 'Wolny': $color = 'green'; break;
         case 'W drodze': $color = 'yellow'; break;
         case 'Interwencja': $color = 'red lighten-2'; break;
         case 'Konwój, doprowadzenie': $color = 'red darken-4'; break;
         case 'Dokumentacja': $color = 'purple'; break;
         default: $color = 'grey';
    }
    echo '<div class="patrol-item" data-id="'.$row['id'].'" style="background-color:'.$color.'; padding:5px; margin:5px; cursor:pointer;">';
    echo '<strong>'.htmlspecialchars($row['call_sign']).'</strong><br>';
    echo htmlspecialchars($row['composition']);
    echo '</div>';
}
?>
