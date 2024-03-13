<html>
    <head>
        <title>Gestion de base de données MYSQL en php</title>
      
</head>
<body>
<h1> Enregistrement officiel</h1>
    <?php
     @include("connexion.php");
    $requete="select * from eleves_v2";
    $resultat=mysqli_query($conn, $requete);
    echo mysqli_num_rows($resultat);
    ?>
    
    <center><table border="1">
        <tr><td>prenom</td><td>Nom</td><td>date naissance</td><td>telephone</td><td>adresse</td><td>ville</td><td>niveau</td><td>commentaire</td><td>photo</td>

    <?php while($enreg=mysqli_fetch_array($resultat))
    { 
    ?>
    <tr>
    <td><?php echo $enreg["prenom"];?></td>
    <td><?php echo $enreg["nom"];?></td>
    <td><?php echo $enreg["date_naiss"];?></td>
    <td><?php echo $enreg["email"];?></td>
    <td><?php echo $enreg["tel"];?></td>
    <td><?php echo $enreg["adresse"];?></td>
    <td><?php echo $enreg["ville"];?></td>
    <td><?php echo $enreg["niveau"];?></td>
    <?php echo "<td><img src='img/". $enreg['photo']."'width = '200px'</td>";?>
    </tr>
    <?php  } ?>
    </table></center>
    <?php
    echo '<center><a href="projet2.php">retour</a></center>';
    mysqli_close($conn);
    ?>
    
  </body>
 </html>