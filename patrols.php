<?php
header('Content-Type: application/json');

$servername = "sql.10.svpj.link";  // lub adres IP serwera bazy danych
$username = "db_105690";  // Twoja nazwa użytkownika MySQL
$password = "NVzd08ODhiBV";  // Twoje hasło MySQL
$dbname = "db_105690";  // Nazwa bazy danych

// Utworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die(json_encode(["error" => "Połączenie z bazą danych nie powiodło się: " . $conn->connect_error]));
}

$sql = "SELECT id, name, status FROM patrols";
$result = $conn->query($sql);

$patrols = [];

if ($result->num_rows > 0) {
    // Przetwarzanie wyników
    while($row = $result->fetch_assoc()) {
        $patrols[] = $row;
    }
    echo json_encode($patrols);
} else {
    echo json_encode([]);
}

$conn->close();
?>
