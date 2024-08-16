<?php
header('Content-Type: application/json');

// Ustawienia bazy danych
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";


// Pobranie parametru kryptonimu z zapytania GET
$kryptonim = isset($_GET['kryptonim']) ? $_GET['kryptonim'] : '';

if (empty($kryptonim)) {
    echo json_encode(["error" => "Kryptonim is required"]);
    exit;
}

// Utworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Zapytanie SQL
$sql = "SELECT id, nazwa, zgłaszający, lokalizacja, miernik, tresc, data_utworzenia 
        FROM interwencje 
        WHERE patrol_id = (SELECT id FROM patrole WHERE kryptonim = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $kryptonim);
$stmt->execute();
$result = $stmt->get_result();

$interventions = array();
if ($result->num_rows > 0) {
    // Pobranie danych
    while($row = $result->fetch_assoc()) {
        $interventions[] = $row;
    }
} else {
    $interventions = ["message" => "No interventions found"];
}

// Zamknięcie połączenia
$conn->close();

// Wyświetlenie wyników w formacie JSON
echo json_encode($interventions);
?>
