<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$patrol_id = $_POST['patrol_id'] ?? '';
$content = $_POST['content'] ?? '';
if($patrol_id && $content){
    $db = getDB();
    $stmt = $db->prepare("SELECT call_sign FROM patrols WHERE id = ?");
    $stmt->execute([$patrol_id]);
    $patrol = $stmt->fetch(PDO::FETCH_ASSOC);
    if($patrol){
        $call_sign = $patrol['call_sign'];
        $stmt = $db->prepare("INSERT INTO messages (call_sign, content, sender) VALUES (?, ?, ?)");
        $stmt->execute([$call_sign, $content, 'dyzurny']);
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
} else {
    echo json_encode(['success'=>false]);
}
?>
