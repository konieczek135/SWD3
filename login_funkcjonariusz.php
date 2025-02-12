<?php
session_start();
include 'common.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $call_sign = trim($_POST['call_sign']);
    $composition = trim($_POST['composition']);
    if(empty($call_sign) || empty($composition)) {
         $_SESSION['error'] = "Wszystkie pola są wymagane.";
         header("Location: index.php");
         exit;
    }
    // Wstawienie do bazy – INSERT OR IGNORE zapobiegnie powielaniu
    $db = getDB();
    $stmt = $db->prepare("INSERT OR IGNORE INTO patrols (call_sign, composition) VALUES (?, ?)");
    $stmt->execute([$call_sign, $composition]);

    $_SESSION['role'] = 'funkcjonariusz';
    $_SESSION['call_sign'] = $call_sign;
    header("Location: officer.php");
    exit;
}
?>
