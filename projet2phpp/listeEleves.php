<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="affult.css">
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<?php
session_start();
?>
<?php
echo "<center>Bienvenu ".$_SESSION["log"]." ".$_SESSION["mot"]."</center>";
?>
<?php
@include("connexion.php");
$requete="SELECT * FROM eleves ORDER BY moyenne";
$resultat=mysqli_query($conn,$requete);
$per =mysqli_num_rows($resultat);
?>
<?php
@include("connexion.php");
$requetemo="SELECT AVG(moyenne) FROM eleves";
$resultatmo=mysqli_query($conn,$requetemo);
$enregmo=mysqli_fetch_array($resultatmo);
?>
?>
<?php
@include("connexion.php");
$requetesupp="SELECT * FROM eleves WHERE moyenne >= 10";
$resultatsupp=mysqli_query($conn,$requetesupp);
$supp=mysqli_num_rows($resultatsupp);
$a = ($supp/$per)*100;
?>
<?php
@include("connexion.php");
$requeteinf="SELECT * FROM eleves WHERE moyenne < 10";
$resultatinf=mysqli_query($conn,$requeteinf);
?>
<center>
    <table border="1">
        <tr>
            <td>Identifiant</td> <td>Nom</td> <td>Adresse</td> <td>Numéro</td>  <td>Moyenne</td>
        </tr>
        <?php while($enreg=mysqli_fetch_array($resultat))
        {?>
            <tr>
                <td><?php echo $enreg["num"];?></td>
                <td><?php echo $enreg["nom"];?></td>
                <td><?php echo $enreg["Adresse"];?></td>
                <td><?php echo $enreg["Numdetel"];?></td>
                <td><?php echo $enreg["moyenne"];?></td>

            </tr>
        <?php }?>
    </table>
    <h1>La moyenne est de : </h1><?php echo $enregmo['AVG(moyenne)'];?>
    <br><br>
    <h1>le pourcentage d'amis est de : </h1><?php echo $a;?><h1>%</h1>
    <br><br>
    <h1>Le nombre de personne admis est : </h1><?php echo mysqli_num_rows($resultatsupp);?>
    <br><br>
    <h1>Le nombre de personne non admis est : </h1><?php echo mysqli_num_rows($resultatinf);?>
    <br><br>
    <div class="prem">
        <div>
    <h1>les personnes admis</h1>
    <table border="1">
        <tr>
            <td>Identifiant</td> <td>Nom</td> <td>Adresse</td> <td>Numéro</td>  <td>Moyenne</td>
        </tr>
        <?php while($enregsupp=mysqli_fetch_array($resultatsupp))
        {?>
            <tr>
                <td><?php echo $enregsupp["num"];?></td>
                <td><?php echo $enregsupp["nom"];?></td>
                <td><?php echo $enregsupp["Adresse"];?></td>
                <td><?php echo $enregsupp["Numdetel"];?></td>
                <td><?php echo $enregsupp["moyenne"];?></td>

            </tr>
        <?php }?>
    </table>
        </div>
        <div>
    <h1>les personnes non admis</h1>
    <table border="1">
        <tr>
            <td>Identifiant</td> <td>Nom</td> <td>Adresse</td> <td>Numéro</td>  <td>Moyenne</td>
        </tr>
        <?php while($enreginf=mysqli_fetch_array($resultatinf))
        {?>
            <tr>
                <td><?php echo $enreginf["num"];?></td>
                <td><?php echo $enreginf["nom"];?></td>
                <td><?php echo $enreginf["Adresse"];?></td>
                <td><?php echo $enreginf["Numdetel"];?></td>
                <td><?php echo $enreginf["moyenne"];?></td>

            </tr>
        <?php }?>
    </table>
    </div>
    </div>
</center>
<?php
mysqli_close($conn);?>
</body>
</html>