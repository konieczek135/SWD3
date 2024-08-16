<?php
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nadawca = $_POST['nadawca'];
$odbiorca = $_POST['odbiorca'];
$tresc = $_POST['tresc'];

$sql = "INSERT INTO komunikaty (nadawca, odbiorca, tresc) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nadawca, $odbiorca, $tresc);

if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
