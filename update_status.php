<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'funkcjonariusz'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'];
$status = $_POST['status'] ?? '';
if($status){
    $db = getDB();
    $stmt = $db->prepare("UPDATE patrols SET status = ? WHERE call_sign = ?");
    $stmt->execute([$status, $call_sign]);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>
