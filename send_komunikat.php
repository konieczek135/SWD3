<?php
header('Content-Type: application/json');

// Ustawienia bazy danych
$servername = "sql.10.svpj.link";
$username = "db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";


// Pobranie danych z POST
$nadawca = isset($_POST['nadawca']) ? $_POST['nadawca'] : '';
$odbiorca = isset($_POST['odbiorca']) ? $_POST['odbiorca'] : '';
$tresc = isset($_POST['tresc']) ? $_POST['tresc'] : '';

if (empty($nadawca) || empty($odbiorca) || empty($tresc)) {
    echo json_encode(["error" => "All fields are required"]);
    exit;
}

// Utworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Zapytanie SQL
$sql = "INSERT INTO komunikaty (nadawca, odbiorca, tresc) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $nadawca, $odbiorca, $tresc);
if ($stmt->execute()) {
    $response = ["success" => "Message sent successfully"];
} else {
    $response = ["error" => "Failed to send message"];
}

// Zamknięcie połączenia
$conn->close();

// Wyświetlenie wyników w formacie JSON
echo json_encode($response);
?>
