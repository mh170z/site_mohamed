<?php
// Déclare que la réponse sera au format JSON
header('Content-Type: application/json');

// Démarre la session ou la reprend (inclut les variables de session)
include('session_start.php');


// Récupère les données JSON envoyées via la requête HTTP (POST ou PUT)
$data = json_decode(file_get_contents('php://input'), true);

// Vérifie que les données essentielles sont bien présentes et valides
if (!isset($data['table'], $data['row'], $data['primaryKeys']) || !is_array($data['row']) || !is_array($data['primaryKeys'])) {
    echo json_encode(["error" => "Invalid input data"]);
    exit();
}

$tableName = $conn->real_escape_string($data['table']);

// Contient les valeurs à mettre à jour
$updatedRow = $data['row'];

error_log(print_r($updatedRow, true));

if ($tableName == 'ACTIVITE' && $updatedRow['CODEETATACT'] == 'AN') {
    $updatedRow['DATEANNULEACT'] == date('Y-m-d');
}

// Contient les clés primaires pour identifier la ligne à modifier
$primaryKeys = $data['primaryKeys'];

// Récupère la structure de la table (nom des colonnes et leurs types)
$queryColumns = $conn->query("DESCRIBE `$tableName`");
$columnTypes = [];
while ($row = $queryColumns->fetch_assoc()) {
    $columnTypes[$row['Field']] = $row['Type'];
}

// Initialisation des parties SET et WHERE de la requête SQL
$setClauses = [];
$whereClauses = [];

// Boucle sur chaque colonne à mettre à jour
foreach ($updatedRow as $column => $value) {
    // Ignore les colonnes non présentes dans la table
    if (!array_key_exists($column, $columnTypes))
        continue;

    // Sécurise le nom de la colonne
    $escapedColumn = "`" . $conn->real_escape_string($column) . "`";
    $type = strtolower($columnTypes[$column]);

    // Adapte la valeur selon le type de la colonne
    if (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false || strpos($type, 'float') !== false) {
        $safeValue = is_numeric($value) ? $value : 'NULL';
    } else if (strpos($type, 'date') !== false || strpos($type, 'time') !== false) {
        if ($value === '' || $value === null) {
            $safeValue = 'NULL';
        } else {
            $safeValue = "'" . $conn->real_escape_string($value) . "'";
        }
    } else {
        // Pour tous les autres types (ex : texte), on entoure de guillemets
        $safeValue = "'" . $conn->real_escape_string($value) . "'";
    }

    // Si c'est une clé primaire, on l'ajoute à la clause WHERE
    if (in_array($column, $primaryKeys)) {
        $whereClauses[] = "$escapedColumn = $safeValue";
    } else {
        // Sinon, on l'ajoute à la clause SET
        $setClauses[] = "$escapedColumn = $safeValue";
    }
}

// Vérifie qu'on a bien des clauses SET et WHERE valides
if (empty($setClauses) || empty($whereClauses)) {
    echo json_encode(["error" => "Invalid SQL query parameters (empty SET or WHERE clauses)"]);
    exit();
}

// Construit la requête SQL finale UPDATE
$sql = "UPDATE `$tableName` SET " . implode(', ', $setClauses) . " WHERE " . implode(' AND ', $whereClauses);

try {
    // Exécute la requête et renvoie un succès si OK
    if ($conn->query($sql) === TRUE) {
        if ($tableName == 'ACTIVITE' && $updatedRow['CODEETATACT'] == 'AN') { // activité annulée
            // Annule les inscriptions et éventuellement envoie un email
            $query = "UPDATE INSCRIPTION SET DATEANNULE = ? WHERE CODEANIM = ? AND DATEACT = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
            }
            $stmt->bind_param("sss", $updatedRow['DATEANNULEACT'], $updatedRow['CODEANIM'], $updatedRow['DATEACT']);
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'exécution de la requête: " . $stmt->error);
            }

            // 🔥 On récupère les NOINSCRIP des inscriptions annulées
            $selectQuery = "SELECT NOINSCRIP FROM INSCRIPTION
                WHERE CODEANIM = ? AND DATEACT = ? AND DATEANNULE IS NOT NULL";
            $stmtSelect = $conn->prepare($selectQuery);
            if (!$stmtSelect) {
                throw new Exception("Erreur de préparation de la requête SELECT: " . $conn->error);
            }
            $stmtSelect->bind_param("ss", $updatedRow['CODEANIM'], $updatedRow['DATEACT']);
            $stmtSelect->execute();
            $result = $stmtSelect->get_result();

            $annulations = [];
            while ($row = $result->fetch_assoc()) {
                $annulations[] = $row['NOINSCRIP'];
            }

            echo json_encode([
                "success" => "Enregistrement mis à jour avec succès. Inscriptions annulées (" . implode(', ', $annulations) . ")."
            ]);

            // À faire : envoyer un mail aux vacanciers pour les informer que l'activité est annulée

        } else {
            echo json_encode(["success" => "Enregistrement mis à jour avec succès."]);
        }
    } else {
        // Si erreur SQL, on lance une exception
        throw new Exception("SQL Error: " . $conn->error);
    }
} catch (Exception $e) {
    // Gestion de l'erreur : code 500 + message d'erreur
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

// Ferme la connexion à la base de données
$conn->close();
