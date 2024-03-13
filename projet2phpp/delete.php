<html>
    <head>
        <meta charset="utf-8">
        <title>Gestion de la BDD</title>
    </head>
    <body>
        <?php
            @include("connexion.php");
            $numEleve = $_POST["num"];
            $requete = "DELETE FROM eleves WHERE num=$numEleve";
            echo $requete;
            $resultat = mysqli_query($conn, $requete);
            if(!$resultat){
                echo "<center><h1>Echec de la suppression $requete</h1></center>";
                echo mysqli_error($connexion);
            }else{
                if(mysqli_affected_rows($conn)){
                    echo "<center><h1>Suppression effectuée</h1></center>";
                    echo "<center><a href='projet2.php'>Retour</a></center>";
                }
            }
            
            mysqli_close($conn);

        ?>
        
    </body>
</html>