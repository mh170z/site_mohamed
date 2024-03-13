<?php
session_start();
?>

<!DOCTYPE html>
    <head>
        <html lang="en">
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulaire</title> 
        <link rel="stylesheet" href="enreOff.css">
    </head>
    <body>
        <div class="container"> 
            <form action="enregOffiBdd.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nom">Nom:</label> 
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom:</label> 
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-group"> 
                    <label for="date_naissance">Date de naissance:</label> 
                    <input type="date" id="date_naissance" name="date" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label> 
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone:</label> 
                    <input type="tel" id="telephone" name="telephone" required>
                </div>
                <div class="form-group"> 
                    <label for="photo">Photo de profil:</label> 
                    <input type="file" id="photo" name="file">
                </div>
                <div class="form-group"> 
                    <label for="adresse">Adresse:</label> 
                    <textarea id="adresse" name="adresse" rows="2" required></textarea>
                </div>
                <div class="form-group">    
                    <label for="ville">Ville:</label> 
                    <select id="ville" name="ville" required>   
                        <option value="" selected disabled>Choisir une ville</option>
                        <option value="Paris">Paris</option>
                        <option value="Lyon">Lyon</option> 
                        <option value="Marseille">Marseille</option> 
                        <option value="Toulouse">Toulouse</option>
                    </select>
                </div> 
                <div class="form-group">
                <label for="niveau">Niveau (exemple BAC):</label>
                    <select id="niveau" name="niveau" required>
                        <option value="" selected disabled>Choisir un niveau</option>
                        <option value="BAC">BAC</option>
                        <option value="BTS">BTS</option> 
                        <option value="Licence">Licence</option> 
                        <option value="Master">Master</option>
                    </select>
                </div> 
                <div class="form-group">
                    <label for="commentaire">Commentaire:</label>
                    <textarea id="commentaire" name="com" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Valider</button>
                    <button type="button" class="btn btn-danger">Annuler</button> 
                </div> 
            </form> 
        </div>
    </body>
</html>