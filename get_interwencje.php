<?php
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$kryptonim = $_GET['kryptonim'];

$sql = "SELECT i.id, i.nazwa, i.zgłaszający, i.lokalizacja, i.miernik, i.tresc, i.data_utworzenia 
        FROM interwencje i 
        JOIN patrole p ON i.patrol_id = p.id 
        WHERE p.kryptonim = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kryptonim);
$stmt->execute();
$result = $stmt->get_result();

$interwencje = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $interwencje[] = $row;
    }
}

echo json_encode($interwencje);
$stmt->close();
$conn->close();
?>
