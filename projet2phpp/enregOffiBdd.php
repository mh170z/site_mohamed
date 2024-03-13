<?php
@include("connexion.php");
$prenom = $_POST["prenom"];
$nom = $_POST["nom"];
$date = $_POST["date"];
$email = $_POST["email"];
$phone = $_POST["telephone"];
$adresse = $_POST["adresse"];
$ville = $_POST["ville"];
$niveau = $_POST["niveau"];
$comm = $_POST["com"];

$file =$_FILES['file'];
$file_name = $_FILES['file']['name'];
$file_tmp_name = $_FILES['file']['tmp_name'];


$requete = "INSERT INTO eleves_v2 VALUES ('$prenom', '$file_name','$nom','$date','$email','$phone','$adresse','$ville','$niveau','$comm','')";

$path = "img/$file_name";
if (move_uploaded_file($file_tmp_name,$path)){
    if(mysqli_query($conn,$requete))
    {
        echo"etudiant ajoute avec succés";
        header("location:enregOffi.php");
    }else
    {
        echo"erreur d'ajout : "; echo mysqli_error($conn);
    }
}


mysqli_close($conn)

?>