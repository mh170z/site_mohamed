<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <?php
        
        @include("connexion.php");
        $num = $_POST["num"];
        $nom = $_POST["nom"];
        $adresse = $_POST["adresse"];   
        $tel = $_POST["tel"];

        $requete = "UPDATE eleves SET num='$num', nom='$nom', adresse='$adresse', tel='$tel' WHERE num='$num'";
        $resultat = mysqli_query($conn, $requete);

        if(!$resultat){
            echo "<center><h1>Echec de la modification $requete</h1></center>";
            echo mysqli_error($conn);
        }else{
            if(mysqli_affected_rows($conn)){
                echo "<center><h1>Modification effectuée</h1></center>";
                echo "<center><a href='projet2.php'>Retour</a></center>";
            }
        }

        

        ?>
    </body>
</html>