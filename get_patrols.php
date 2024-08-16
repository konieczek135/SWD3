<?php
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT kryptonim, sklad FROM patrole";
$result = $conn->query($sql);

$patrole = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patrole[] = $row;
    }
}

echo json_encode($patrole);
$conn->close();
?>
