<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'common.php';

if(isset($_SESSION['role'])) {
    if($_SESSION['role'] === 'funkcjonariusz') {
        $call_sign = $_SESSION['call_sign'];
        $db = getDB();
        // Usuwamy rekord patrolu dla funkcjonariusza
        $stmt = $db->prepare("DELETE FROM patrols WHERE call_sign = ?");
        $stmt->execute([$call_sign]);
    }
    // Dla obu ról zamykamy sesję
    session_destroy();
}

header("Location: index.php");
exit;
?>
