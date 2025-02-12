<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$call_sign = $_POST['call_sign'] ?? '';
$composition = $_POST['composition'] ?? '';
if($call_sign && $composition){
    $db = getDB();
    $stmt = $db->prepare("INSERT OR IGNORE INTO patrols (call_sign, composition) VALUES (?, ?)");
    $stmt->execute([$call_sign, $composition]);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>
