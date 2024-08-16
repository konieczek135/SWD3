<?php
header('Content-Type: application/json');

$servername = "sql.10.svpj.link"; // Zmień na odpowiednie dane serwera
$username = "db_105690"; // Zmień na odpowiednie dane
$password = "NVzd08ODhiBV"; // Zmień na odpowiednie dane
$dbname = "db_105690"; // Zmień na odpowiednią nazwę bazy danych

// Tworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzanie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pobranie akcji z żądania
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_patrols':
        get_patrols($conn);
        break;
    case 'update_patrol_status':
        update_patrol_status($conn);
        break;
    case 'send_message':
        send_message($conn);
        break;
    case 'get_messages':
        get_messages($conn);
        break;
    case 'get_interwencje':
        get_interwencje($conn);
        break;
    default:
        echo json_encode(array('error' => 'Invalid action'));
        break;
}

$conn->close();

function get_patrols($conn) {
    $sql = "SELECT kryptonim FROM patrols"; // Zmień na odpowiednią tabelę
    $result = $conn->query($sql);

    $patrols = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $patrols[] = $row;
        }
    }

    echo json_encode($patrols);
}

function update_patrol_status($conn) {
    $kryptonim = $_GET['kryptonim'];
    $status = $_GET['status'];

    $sql = "UPDATE patrols SET status = ? WHERE kryptonim = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $kryptonim);
    $stmt->execute();

    $response = array('success' => $stmt->affected_rows > 0);
    echo json_encode($response);

    $stmt->close();
}

function send_message($conn) {
    $nadawca = $_POST['nadawca'];
    $odbiorca = $_POST['odbiorca'];
    $tresc = $_POST['tresc'];

    $sql = "INSERT INTO messages (nadawca, odbiorca, tresc, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nadawca, $odbiorca, $tresc);
    $stmt->execute();

    $response = array('success' => $stmt->affected_rows > 0);
    echo json_encode($response);

    $stmt->close();
}

function get_messages($conn) {
    $kryptonim = $_GET['kryptonim'];

    $sql = "SELECT id, nadawca, tresc FROM messages WHERE odbiorca = ? ORDER BY timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kryptonim);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = array();
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);

    $stmt->close();
}

function get_interwencje($conn) {
    $kryptonim = $_GET['kryptonim'];

    $sql = "SELECT id FROM interwencje WHERE kryptonim = ? AND status = 'Nowa'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kryptonim);
    $stmt->execute();
    $result = $stmt->get_result();

    $interwencje = array();
    while($row = $result->fetch_assoc()) {
        $interwencje[] = $row;
    }

    echo json_encode($interwencje);

    $stmt->close();
}
?>
