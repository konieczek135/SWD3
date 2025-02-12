<?php
session_start();
if(!isset($_SESSION['role'])){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'] ?? '';
$content = $_POST['content'] ?? '';
if($content && $call_sign){
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO messages (call_sign, content, sender) VALUES (?, ?, ?)");
    $stmt->execute([$call_sign, $content, 'funkcjonariusz']);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>
