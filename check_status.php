<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'funkcjonariusz'){
    echo json_encode(['status' => null]);
    exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'];
$db = getDB();
$stmt = $db->prepare("SELECT status FROM patrols WHERE call_sign = ?");
$stmt->execute([$call_sign]);
$patrol = $stmt->fetch(PDO::FETCH_ASSOC);
if($patrol){
    echo json_encode(['status' => $patrol['status']]);
} else {
    echo json_encode(['status' => null]);
}
?>
