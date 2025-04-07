<?php
include('session_start.php'); // Connexion à la session


// Récupère les données JSON envoyées via la requête HTTP (POST ou PUT)
$data = json_decode(file_get_contents('php://input'), true);

// Vérifie que les données essentielles sont bien présentes et valides
if (!isset($data['tablename'])) {
    echo json_encode(["error" => "Invalid input data"]);
    exit();
}

$tableName = $conn->real_escape_string($data['tablename']);


// Vérifie si l'utilisateur est déjà connecté
$isLoggedIn = isset($_SESSION['user_id']);
$user_role = $isLoggedIn ? $_SESSION['role'] : 'EX'; // Récupère le rôle de l'utilisateur ou 'EX' s'il n'est pas connecté
$user = $isLoggedIn ? $_SESSION['user_id'] : ''; // Récupère l'ID de l'utilisateur connecté

// Récupère le prénom et le nom de l'utilisateur s'il est connecté
$user_prenom = $isLoggedIn ? $_SESSION['prenom'] : '';
$user_nom = $isLoggedIn ? $_SESSION['nom'] : '';

// Définit le statut de session (connecté ou non)
$sessionStatus = $isLoggedIn ? 'true' : 'false';

// Fonction pour récupérer les valeurs des clés étrangères d'une table
function getForeignKeyValues($conn, $tableName)
{
    $foreignKeys = [];

    // Étape 1 : Récupérer la description des colonnes
    $queryColumns = $conn->query("DESCRIBE $tableName");
    $columns = [];

    while ($row = $queryColumns->fetch_assoc()) {
        $columns[$row['Field']] = $row['Type'];
    }

    // Étape 2 : Récupérer les clés étrangères
    $sql = "SELECT 
                k.COLUMN_NAME, 
                k.REFERENCED_TABLE_NAME, 
                k.REFERENCED_COLUMN_NAME 
            FROM information_schema.KEY_COLUMN_USAGE k 
            WHERE k.TABLE_NAME = ? 
            AND k.CONSTRAINT_SCHEMA = DATABASE() 
            AND k.REFERENCED_TABLE_NAME IS NOT NULL";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tableName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Étape 3 : Récupérer les valeurs possibles pour chaque clé étrangère
    while ($row = $result->fetch_assoc()) {
        $column = $row['COLUMN_NAME'];
        $referencedTable = $row['REFERENCED_TABLE_NAME'];
        $referencedColumn = $row['REFERENCED_COLUMN_NAME'];

        $valuesQuery = "SELECT DISTINCT $referencedColumn FROM $referencedTable";
        $valuesResult = $conn->query($valuesQuery);

        $values = [];
        while ($valueRow = $valuesResult->fetch_assoc()) {
            $values[] = $valueRow[$referencedColumn];
        }

        $foreignKeys[$column] = $values;
    }

    return $foreignKeys;
}

// Fonction pour récupérer les données d'une table avec les clés étrangères et autres informations
function getTableData($conn, $user, $user_role, $tableName)
{
    // Récupérer les métadonnées des colonnes
    $queryColumns = $conn->query("DESCRIBE $tableName");
    $columns = [];
    $columnTypes = [];
    $nullable = [];
    $primaryKeys = [];
    $autoIncrement = [];

    while ($row = $queryColumns->fetch_assoc()) {
        $columns[] = $row['Field'];
        $columnTypes[$row['Field']] = $row['Type'];
        $nullable[$row['Field']] = $row['Null'] === 'YES' ? true : false;
        if ($row['Key'] === 'PRI') {
            $primaryKeys[] = $row['Field'];
        }
        $autoIncrement[$row['Field']] = $row['Extra'] === 'auto_increment' ? true : false;
    }

    // Récupérer les données de la table
    $columnList = implode(", ", $columns);
    if ($tableName === "ACTIVITE") {
        $result = $conn->query("SELECT $columnList FROM $tableName WHERE CODEETATACT != 'AN'");
    } else if ($tableName === "INSCRIPTION") {
        if ($user_role != 'AD' && $user_role != 'EN') {
            $result = $conn->query("SELECT $columnList 
                 FROM $tableName
                 WHERE USER = '" . $user . "'
                   AND DATEANNULE is NULL");
        } else {
            $result = $conn->query("SELECT $columnList FROM $tableName WHERE DATEANNULE is NULL");
        }
    } else {
        $result = $conn->query("SELECT $columnList FROM $tableName");
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    // Récupérer les valeurs des clés étrangères
    $fkValues = getForeignKeyValues($conn, $tableName);

    // Retourner un tableau structuré avec les métadonnées et les données
    return [
        "name" => $tableName,
        "columns" => $columns,
        "fkValues" => $fkValues,
        "rows" => $rows,
        "columnTypes" => $columnTypes,
        "nullable" => $nullable,
        "primaryKeys" => $primaryKeys,
        "autoIncrement" => $autoIncrement
    ];
}


try {
    // télécharge les données de la table $tableName
    echo json_encode(['success' => getTableData($conn, $user, $user_role, $tableName)]);
} catch (Exception $e) {
    // Gestion de l'erreur : code 500 + message d'erreur
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

// Fermer la connexion
$conn->close();
