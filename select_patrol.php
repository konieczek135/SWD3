<?php
session_start();
include 'common.php';
if(isset($_GET['id'])){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM patrols WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $patrol = $stmt->fetch(PDO::FETCH_ASSOC);
    if($patrol){
        $_SESSION['role'] = 'funkcjonariusz';
        $_SESSION['call_sign'] = $patrol['call_sign'];
        header("Location: officer.php");
        exit;
    }
}
header("Location: index.php");
exit;
?>
