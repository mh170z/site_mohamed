<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="aff5.css">
        <title>Gestion de la BDD</title>
    </head>
    <body>
        <?php
            @include("connexion.php");
            $requete="SELECT * FROM eleves";
            $resultat=mysqli_query($conn, $requete);
            echo "<center>"; echo mysqli_num_rows($resultat); echo "</center>";
        ?>
        <?php
            while(($enreg=mysqli_fetch_array($resultat))){
            ?>
            <div class="ele">
                <div class="head">
                    <h1><?php echo $enreg["num"]?></h1>
                </div>
                <div class="content">   
                    <br>
                    <center><p>Num élève : <?php echo $enreg["num"]?></p>
                    <p>Nom : <?php echo $enreg["nom"]?></p>
                    <p>Adresse : <?php echo $enreg["adresse"]?></p>
                    <p>Num tel : <?php echo $enreg["tel"]?></p></center>
                </div>
                <div class="but">
                    <button>Plus de détail</button>
                </div>
            </div>
            <?php } ?>
        
        <?php
            echo "<center><a href='projet2.php'>retour</a></center>";
            mysqli_close($conn);
        ?>
        
    </body>
</html>