<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDB() {
    $db = new PDO('sqlite:' . __DIR__ . '/swd.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function initializeDB() {
    $db = getDB();
    // Tabela patroli
    $db->exec("CREATE TABLE IF NOT EXISTS patrols (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        call_sign TEXT UNIQUE,
        composition TEXT,
        status TEXT DEFAULT 'Wolny',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    // Tabela komunikatów
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        call_sign TEXT,
        content TEXT,
        sender TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    // Tabela interwencji
    $db->exec("CREATE TABLE IF NOT EXISTS interventions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        call_sign TEXT,
        type TEXT,
        location TEXT,
        reporter TEXT,
        urgency INTEGER DEFAULT 0,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}
initializeDB();
?>
