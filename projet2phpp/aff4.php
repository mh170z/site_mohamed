<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body style="background-color: pink;">
        <?php
            @include("connexion.php");
            $requete = "SELECT * FROM eleves";
            $resultat = mysqli_query($conn, $requete);
        ?>

        <select style="text-align: center; font-weight: bold; font-size:20px;">
        <option value="">--Choisissez un élève--</option>
        <?php
            $i = 1;
            while($enre = mysqli_fetch_array($resultat)){
        ?>
        <option><?php echo utf8_encode($enre['nom']) ?></option>
        <?php
            $i++;
            }
        ?>
        </select>
        <?php mysqli_close($conn) ?>
    </body>
</html>