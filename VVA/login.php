<?php
// Inclusion du fichier de démarrage de session pour gérer les variables de session
include('session_start.php');

// Définition de l'en-tête de la réponse en JSON pour indiquer que la réponse sera au format JSON
header("Content-Type: application/json");

try {
    // Vérifie si l'utilisateur est déjà connecté
    if (isset($_SESSION['user_id'])) {
        // Si un utilisateur est déjà connecté, on détruit la session existante
        session_unset(); // Supprime toutes les variables de session
        session_destroy(); // Détruit la session
        session_start(); // Démarre une nouvelle session
    }

    // Vérifie si la méthode de la requête est bien POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Si la méthode n'est pas POST, renvoie un code de statut HTTP 405 (Méthode non autorisée)
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
        exit(); // Arrêt du script si la méthode n'est pas POST
    }

    // Récupère et nettoie les données envoyées dans le formulaire
    $login = trim($_POST['login'] ?? ''); // Nettoyage du login (suppression des espaces inutiles)
    $passwd = $_POST['password'] ?? ''; // Récupération du mot de passe

    // Vérifie si le login ou le mot de passe sont vides
    if (empty($login) || empty($passwd)) {
        // Si un des champs est vide, renvoie un message d'erreur
        echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs"]);
        exit(); // Arrêt du script si des champs sont vides
    }

    // Prépare la requête SQL pour vérifier si l'utilisateur existe dans la base de données
    $sql = "SELECT USER, NOMCOMPTE, PRENOMCOMPTE, MDP, TYPEPROFIL, DATEDEBSEJOUR, DATEFINSEJOUR FROM COMPTE WHERE USER = ?";
    $stmt = $conn->prepare($sql); // Prépare la requête SQL
    $stmt->bind_param("s", $login); // Lier le paramètre 'login' à la requête SQL (sécurisé contre les injections SQL)
    $stmt->execute(); // Exécute la requête
    $result = $stmt->get_result(); // Récupère le résultat de la requête

    // Vérifie si un utilisateur avec ce login existe dans la base de données
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc(); // Récupère les données de l'utilisateur

        // Vérifie si le mot de passe fourni correspond au mot de passe stocké dans la base de données
        $d = date('Y-m-d');
        $debSejour = $row['DATEDEBSEJOUR'];
        $finSejour = $row['DATEFINSEJOUR'];
        $sejourOk = $d >= ($debSejour == '' ? '1900-01-01' : $debSejour) && $d <= ($finSejour == '' ? '9999-01-01' : $finSejour);
        if (password_verify($passwd, $row['MDP']) && $sejourOk) {
            // Si le mot de passe est correct, on initialise les variables de session
            $_SESSION['user_id'] = $row['USER'];
            $_SESSION['nom'] = $row['NOMCOMPTE'];
            $_SESSION['prenom'] = $row['PRENOMCOMPTE'];
            $_SESSION['role'] = $row['TYPEPROFIL'];
            $_SESSION['datedebsejour'] = $row['DATEDEBSEJOUR'];
            $_SESSION['datefinsejour'] = $row['DATEFINSEJOUR'];

            // Renvoie un message de succès avec les informations de l'utilisateur
            echo json_encode([
                "success" => true,
                "role" => $row['TYPEPROFIL'],
                "nom" => $row['NOMCOMPTE'],
                "prenom" => $row['PRENOMCOMPTE'],
                "datedebsejour" => $row['DATEDEBSEJOUR'],
                "datefinsejour" => $row['DATEFINSEJOUR']
            ]);
        } else {
            // Si le mot de passe est incorrect, renvoie un message d'erreur
            if ($sejourOk) {
                echo json_encode(["success" => false, "message" => "Mot de passe incorrect"]);
            } else {
                if ($d < $row['DATEDEBSEJOUR']) {
                    echo json_encode(["success" => false, "message" => "Votre séjour n'a pas encore commencé."]);
                } else if ($d > $row['DATEFINSEJOUR']) {
                    echo json_encode(["success" => false, "message" => "Votre séjour est terminé."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Séjour invalide."]);
                }
            }
        }
    } else {
        // Si l'utilisateur n'est pas trouvé dans la base de données, renvoie un message d'erreur
        echo json_encode(["success" => false, "message" => "Utilisateur non trouvé"]);
    }

    $stmt->close(); // Ferme la requête préparée
} catch (Exception $e) {
    // En cas d'erreur (par exemple erreur serveur), renvoie un code d'erreur HTTP 500 et le message d'erreur
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur serveur: " . $e->getMessage()]);
} finally {
    // Ferme la connexion à la base de données à la fin du script
    $conn->close();
}
