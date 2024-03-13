<html>
    <head>
        <meta charset="utf-8">
        <title>Gestion de la BDD</title>
    </head>
    <body>
        <?php
            @include("connexion.php");
            $requete="SELECT * FROM eleves";
            $resultat=mysqli_query($conn, $requete);

            while($enreg=mysqli_fetch_array($resultat)){
                echo $enreg["num"];
                echo(";");
                echo $enreg["nom"];
                echo(";");
                echo $enreg["adresse"];
                echo(";");
                echo $enreg["tel"];
                echo(";");
                echo("<br>");
            }
            echo "<a href='projet2.php'>retour</a>";
            mysqli_close($conn);

        ?>
        
        
    </body>
</html>