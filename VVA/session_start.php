<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Paramètres de connexion à la base de données
$servername = "localhost";  
$username = "root";        // Nom d'utilisateur de la base de données
$password = "";            
$dbname = "GACTI";        
// Crée une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie si la connexion a échoué
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>