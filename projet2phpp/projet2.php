<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <link rel="stylesheet" type="text/css" href="index.css">
        <meta charset="utf-8">
        <title>Projet 2 PHP</title>
    </head>
    <body>
        <?php @include("infoDiv.php")?>
        <center><h1>Base de données</h1></center>
        <?php echo "<center><h3>Bienvenue  ".$_SESSION["log"]." ".$_SESSION["mot"]."</h3></center>"?>

        <form action="projet.php" method="post">
        <center><div>
            Identifiant : <input type="number" name="iden" class="style1"><br>
            Nom : <input type="text" name="name" class="style1"><br>
            Adresse : <input type="text" name="adr" class="style1"><br>
            Num de tel : <input type="tel" name="tele" class="style1"><br><br><br>

            <input type="submit" value="Valider" class="style">
            <input type="reset" value="Annuler" class="style"><br>
        </div></center>
        <form action="register.php" method="post">


        <br> <br>
        <center><a href="aff1.php">Liste la liste 1</a></center>
        <center><a href="aff2.php">Liste la liste 2</a></center>
        <center><a href="aff3.php">Affichage spécial 1</a></center>
        <center><a href="aff4.php">Affichage spécial 2</a></center>
        <center><a href="supprimer.php">Supprimer</a></center>
        <center><a href="rechercher1.php">Rechercher 1</a></center>
        <center><a href="rechercher2.html">Rechercher 2</a></center>
        <center><a href="aff5.php">Affichage CSS</a></center>
        <center><a href="enreOffi.php">Enregistrement Officiel</a></center>
        <center><a href="listeEleves.php">Liste des élèves</a></center>
        <center><a href="logout.php">Deconnexion</a></center>
        

    </body>
</html>