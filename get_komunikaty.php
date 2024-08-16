<?php
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$odbiorca = $_GET['odbiorca'];

$sql = "SELECT id, nadawca, tresc, data_utworzenia FROM komunikaty WHERE odbiorca = ? ORDER BY data_utworzenia DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $odbiorca);
$stmt->execute();
$result = $stmt->get_result();

$komunikaty = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $komunikaty[] = $row;
    }
}

echo json_encode($komunikaty);
$stmt->close();
$conn->close();
?>
