<?php
@include("connexion.php");
$a = $_POST["iden"];
$b = $_POST["name"];
$d = $_POST["adr"];
$e = $_POST["tele"];
session_start();

$reql = "INSERT INTO eleves VALUES ('$a','$b','$d','$e')";
$rl = mysqli_query($conn, $reql);

echo "<center><p>Enregistrement effectuer</p></center>";
echo '<center><a href="projet2.php">Retour</a></center>';

mysqli_close($conn)

?>