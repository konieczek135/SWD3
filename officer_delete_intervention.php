<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'funkcjonariusz'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'];
$db = getDB();
$stmt = $db->prepare("DELETE FROM interventions WHERE call_sign = ?");
$stmt->execute([$call_sign]);
echo json_encode(['success'=>true]);
?>
