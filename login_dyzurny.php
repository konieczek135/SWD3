<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if ($password === 'zbiornikpl') {
         $_SESSION['role'] = 'dyzurny';
         header("Location: duty_officer.php");
         exit;
    } else {
         $_SESSION['error'] = "Niepoprawne hasło.";
         header("Location: index.php");
         exit;
    }
}
?>
