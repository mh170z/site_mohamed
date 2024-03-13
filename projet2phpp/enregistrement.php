<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <?php
        
        @include("connexion.php");
        $a = $_POST["login"];
        $b = $_POST["motdepasse"];

        $requete = "INSERT INTO users VALUES ('$a', '$b')";


        if (mysqli_query($conn, $requete)) {
            echo "Nouveau enregistrement créé avec succès";
        } else {
                echo "Erreur : " . $requete . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);

        ?>
    </body>
</html>