<?php
// Démarre la session
session_start();

// Supprime toutes les variables de session
session_unset();

// Détruit la session
session_destroy();

// Redirige l'utilisateur vers la page d'accueil ou login
header("Location: index.php");
exit();
