<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <?php
            @include("connexion.php");
            $numEleve = $_GET['num'];
            $rql = "DELETE FROM eleves WHERE num=$numEleve";
            mysqli_query($conn, $rql);

            header('location:aff3.php');
            exit;

            mysqli_close($conn);

        ?>
        
        
    </body>
</html>