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
            echo "<center>"; echo mysqli_num_rows($resultat); echo "</center>";
        ?>
        <center><table border="1">
            <tr><td>Code_eleve</td><td>Nom_eleve</td><td>Adresse</td><td>Num_telephone</td><td>Supression</tr>
            <?php
                while(($enreg=mysqli_fetch_array($resultat))){
            ?>
            <tr>
                <td><?php echo $enreg["num"];?></td>
                <td><?php echo $enreg["nom"];?></td>
                <td><?php echo $enreg["adresse"];?></td>
                <td><?php echo $enreg["tel"];?></td>
                
                <td><?php echo "<a href=supprimeEleve.php?num=".$enreg['num'].">Supprimer</a>"?></td>

            </tr>
            <?php } ?>
        </table></center>
        
        <?php
            echo "<center><a href='projet2.php'>retour</a></center>";
            mysqli_close($conn);
        ?>
        
    </body>
</html>