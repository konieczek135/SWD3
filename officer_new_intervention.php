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
$type = trim($_POST['type'] ?? '');
$location = trim($_POST['location'] ?? '');
$reporter = trim($_POST['reporter'] ?? '');
$urgency = intval($_POST['urgency'] ?? 0);
if($type && $location && $reporter){
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO interventions (call_sign, type, location, reporter, urgency) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$call_sign, $type, $location, $reporter, $urgency]);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>
