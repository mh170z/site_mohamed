<?php
// Connexion à la session pour récupérer les variables de session (utilisateur connecté, etc.)
include('session_start.php');

// Vérifie si l'utilisateur n'est pas connecté (pas d'ID utilisateur en session)
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, un message est loggé dans la console et l'utilisateur est redirigé vers la page de connexion
    echo "<script>console.log('No user logged in, redirecting to login.php');</script>";
    header("Location: login.php"); // Redirection vers login.php
    exit(); // Arrêt du script pour éviter de continuer l'exécution
}

// Récupère le rôle de l'utilisateur stocké dans la session
$user_role = $_SESSION['role'];

// Récupère les données envoyées via POST sous forme JSON
$data = json_decode(file_get_contents('php://input'), true);

// Récupère le nom de la table et les données de la ligne à supprimer
$tableName = $data['table'];
$row = $data['row'];

// Initialisation de la liste des clauses WHERE pour la requête DELETE
$whereClauses = [];

// Boucle à travers chaque colonne et valeur dans la ligne à supprimer
foreach ($row as $column => $value) {
    // Échappe les valeurs pour prévenir les attaques par injection SQL
    $escapedValue = $conn->real_escape_string($value);

    // Ajoute une clause WHERE pour chaque condition sur une colonne
    $whereClauses[] = "$column = '$escapedValue'";
}

// Combine toutes les clauses WHERE avec l'opérateur "AND" pour former une condition complète
$where = implode(' AND ', $whereClauses);

// Prépare la requête DELETE pour supprimer les données de la table en fonction de la condition WHERE
$sql = "DELETE FROM $tableName WHERE $where";

try {
    // Exécute la requête DELETE et renvoie un succès si tout se passe bien
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => "Record deleted successfully"]);
    } else {
        // Si une erreur SQL survient, une exception est lancée
        throw new Exception("SQL Error: " . $conn->error);
    }

} catch (Exception $e) {
    // Si une exception se produit, renvoie un code HTTP 500 et un message d'erreur JSON
    http_response_code(500); // Code HTTP 500 pour signaler une erreur côté serveur
    echo json_encode(["error" => $e->getMessage()]); // Renvoie le message d'erreur
}

// Ferme la connexion à la base de données une fois la requête exécutée
$conn->close();
?>
