<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body style="background-color: lightgreen;">

    <?php
        @include("connexion.php");
        $id = $_POST["num"];
        $requete="SELECT * FROM eleves WHERE num=$id";
        $resultat=mysqli_query($conn, $requete);
        
        echo "<center>"; echo mysqli_num_rows($resultat); echo "</center>";
    ?>
    
        <center><table border="1">
            <tr><td>Code_eleve</td><td>Nom_eleve</td><td>Adresse</td><td>Num_telephone</td></tr>
            <?php
                while(($enreg=mysqli_fetch_array($resultat))){
            ?>
            <tr>
                <td><?php echo $enreg["num"];?></td>
                <td><?php echo $enreg["nom"];?></td>
                <td><?php echo $enreg["adresse"];?></td>
                <td><?php echo $enreg["tel"];?></td>
            </tr>
            <?php } ?>
        </table></center>
        
        <?php
            echo "<center><a href='projet2.php'>retour</a></center>";
            mysqli_close($conn);
        ?>

    </body>
</html>