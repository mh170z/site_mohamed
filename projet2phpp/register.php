<?php
session_start();
?>

<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <?php
        
        @include("connexion.php");
        $a = $_POST["login"];
        $b = $_POST["motdepasse"];

        $_SESSION["log"] = $_POST["login"];
        $_SESSION["mot"] = $_POST["motdepasse"];

        $requete = "SELECT * from users where login = '$a' and motdepasse = '$b'";
        $resultat = mysqli_query($conn, $requete);
        $ligne = mysqli_num_rows($resultat);

        if($ligne==1){
            header("location:projet2.php");
        }else{
            header("location:userfail.html");
        }

        ?>
    </body>
</html>