<?php
header('Content-Type: application/json');
include('session_start.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No user logged in, redirecting to login.php"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['table'], $data['row'], $data['primaryKeys']) || !is_array($data['row']) || !is_array($data['primaryKeys'])) {
    echo json_encode(["error" => "Invalid input data"]);
    exit();
}

$tableName = $conn->real_escape_string($data['table']);
$updatedRow = $data['row'];
$primaryKeys = $data['primaryKeys'];

$queryColumns = $conn->query("DESCRIBE `$tableName`");
$columnTypes = [];

while ($row = $queryColumns->fetch_assoc()) {
    $columnTypes[$row['Field']] = $row['Type'];
}

$columns = [];
$values = [];

foreach ($updatedRow as $column => $value) {
    if (!array_key_exists($column, $columnTypes)) continue;

    $escapedColumn = "`" . $conn->real_escape_string($column) . "`";
    $type = strtolower($columnTypes[$column]);

    if (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false || strpos($type, 'float') !== false) {
        $safeValue = is_numeric($value) ? $value : 'NULL';
    } else if (strpos($type, 'date') !== false || strpos($type, 'time') !== false) {
        $safeValue = ($value === '' || $value === null) ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
    } else {
        $safeValue = "'" . $conn->real_escape_string($value) . "'";
    }

    $columns[] = $escapedColumn;
    $values[] = $safeValue;
}

if (empty($columns) || empty($values)) {
    echo json_encode(["error" => "Invalid SQL query parameters (empty columns or values)"]);
    exit();
}

$sql = "INSERT INTO `$tableName` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";

try {
    if ($conn->query($sql) === TRUE) {
        $response = ["success" => "Enregistrement inséré avec succès."];

        // Si la table est INSCRIPTION, retourne aussi la clé primaire NOINSCRIP
        if (strtoupper($tableName) === 'INSCRIPTION') {
            $last_id = $conn->insert_id;
            $response["NOINSCRIP"] = $last_id;
        }

        echo json_encode($response);
    } else {
        throw new Exception("SQL Error: " . $conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage(), "sql" => $sql]);
}

$conn->close();
