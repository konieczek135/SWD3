<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$servername = "sql.10.svpj.link";
$username = "db_105690db_105690";
$password = "NVzd08ODhiBV";
$dbname = "db_105690";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_patrols':
        getPatrols($conn);
        break;
    case 'update_patrol_status':
        updatePatrolStatus($conn);
        break;
    case 'send_message':
        sendMessage($conn);
        break;
    case 'get_messages':
        getMessages($conn);
        break;
    case 'get_interwencje':
        getInterwencje($conn);
        break;
    default:
        echo json_encode(["error" => "Invalid action"]);
        break;
}

$conn->close();

function getPatrols($conn) {
    $sql = "SELECT kryptonim FROM patrole";
    $result = $conn->query($sql);

    if ($result === false) {
        echo json_encode(["error" => "Query failed: " . $conn->error]);
        return;
    }

    if ($result->num_rows > 0) {
        $patrols = [];
        while ($row = $result->fetch_assoc()) {
            $patrols[] = $row['kryptonim'];
        }
        echo json_encode($patrols);
    } else {
        echo json_encode([]);
    }
}

function updatePatrolStatus($conn) {
    $kryptonim = $_POST['kryptonim'] ?? '';
    $status = $_POST['status'] ?? '';

    if ($kryptonim && $status) {
        $sql = "UPDATE patrole SET status = ? WHERE kryptonim = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Prepare failed: " . $conn->error]);
            return;
        }

        $stmt->bind_param("ss", $status, $kryptonim);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "No rows updated"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
    }
}

function sendMessage($conn) {
    $nadawca = $_POST['nadawca'] ?? '';
    $odbiorca = $_POST['odbiorca'] ?? '';
    $tresc = $_POST['tresc'] ?? '';

    if ($nadawca && $odbiorca && $tresc) {
        $sql = "INSERT INTO komunikaty (nadawca, odbiorca, tresc) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Prepare failed: " . $conn->error]);
            return;
        }

        $stmt->bind_param("sss", $nadawca, $odbiorca, $tresc);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Message not sent"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
    }
}

function getMessages($conn) {
    $kryptonim = $_GET['kryptonim'] ?? '';

    if ($kryptonim) {
        $sql = "SELECT * FROM komunikaty WHERE odbiorca = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Prepare failed: " . $conn->error]);
            return;
        }

        $stmt->bind_param("s", $kryptonim);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $messages = [];
            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }
            echo json_encode($messages);
        } else {
            echo json_encode([]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Missing parameters"]);
    }
}

function getInterwencje($conn) {
    $kryptonim = $_GET['kryptonim'] ?? '';

    if ($kryptonim) {
        $sql = "SELECT * FROM interwencje WHERE kryptonim = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Prepare failed: " . $conn->error]);
            return;
        }

        $stmt->bind_param("s", $kryptonim);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $interwencje = [];
            while ($row = $result->fetch_assoc()) {
                $interwencje[] = $row;
            }
            echo json_encode($interwencje);
        } else {
            echo json_encode([]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Missing parameters"]);
    }
}
?>
