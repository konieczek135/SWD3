<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$patrol_id = $_POST['patrol_id'] ?? '';
$type = $_POST['type'] ?? '';
$location = $_POST['location'] ?? '';
$reporter = $_POST['reporter'] ?? '';
$urgency = $_POST['urgency'] ?? 0;
if($patrol_id && $type && $location && $reporter){
    $db = getDB();
    $stmt = $db->prepare("SELECT call_sign FROM patrols WHERE id = ?");
    $stmt->execute([$patrol_id]);
    $patrol = $stmt->fetch(PDO::FETCH_ASSOC);
    if($patrol){
        $call_sign = $patrol['call_sign'];
        $stmt = $db->prepare("INSERT INTO interventions (call_sign, type, location, reporter, urgency) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$call_sign, $type, $location, $reporter, $urgency]);
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
} else {
    echo json_encode(['success'=>false]);
}
?>
