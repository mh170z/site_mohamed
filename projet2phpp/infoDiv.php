<?php
session_start();
?>

<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="infoDiv.css">
    </head>
    <body>
        <menu>
            <?php echo "<center>Vous êtes connecté en tant que ".$_SESSION["log"]."</center>"?>
        </menu>
    </body>
</html>