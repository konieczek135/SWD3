<?php
header('Content-Type: application/json');

// Ustawienia bazy danych
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";


// Utworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Zapytanie SQL
$sql = "SELECT kryptonim, sklad FROM patrole";
$result = $conn->query($sql);

$patrols = array();
if ($result->num_rows > 0) {
    // Pobranie danych
    while($row = $result->fetch_assoc()) {
        $patrols[] = $row;
    }
} else {
    $patrols = ["message" => "No patrols found"];
}

// Zamknięcie połączenia
$conn->close();

// Wyświetlenie wyników w formacie JSON
echo json_encode($patrols);
?>
