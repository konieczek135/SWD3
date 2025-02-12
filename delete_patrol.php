<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
    echo json_encode(['success'=>false]);
    exit;
}
include 'common.php';
$id = $_POST['id'] ?? '';
if($id){
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM patrols WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>
