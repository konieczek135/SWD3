<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$type = $_POST['type'] ?? '';
$id = $_POST['id'] ?? '';
if($type && $id){
    $db = getDB();
    if($type == 'intervention'){
        $stmt = $db->prepare("DELETE FROM interventions WHERE id = ?");
        $stmt->execute([$id]);
    } elseif($type == 'message'){
        $stmt = $db->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$id]);
    }
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>
