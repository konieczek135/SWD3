<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny') {
    echo json_encode(['new' => false]);
    exit;
}
include 'common.php';

// Jeśli to pierwszy polling, ustawiamy domyślnie na 0
$last_id = $_SESSION['dyzurny_last_message_id'] ?? 0;

$db = getDB();
// Pobieramy tylko komunikaty, których nadawcą jest 'funkcjonariusz'
$stmt = $db->prepare("SELECT * FROM messages
                      WHERE sender = 'funkcjonariusz'
                        AND id > ?
                      ORDER BY id ASC LIMIT 1");
$stmt->execute([$last_id]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if ($message) {
    $_SESSION['dyzurny_last_message_id'] = $message['id'];
    echo json_encode(['new' => true, 'content' => $message['content']]);
} else {
    echo json_encode(['new' => false]);
}
?>
