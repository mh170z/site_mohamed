<?php
include('session_start.php'); // Connexion à la session

// Vérifie si l'utilisateur est déjà connecté
$isLoggedIn = isset($_SESSION['user_id']);
$user_role = $isLoggedIn ? $_SESSION['role'] : 'EX'; // Récupère le rôle de l'utilisateur ou 'EX' s'il n'est pas connecté
$user = $isLoggedIn ? $_SESSION['user_id'] : ''; // Récupère l'ID de l'utilisateur connecté

// Récupère le prénom et le nom de l'utilisateur s'il est connecté
$user_prenom = $isLoggedIn ? $_SESSION['prenom'] : '';
$user_nom = $isLoggedIn ? $_SESSION['nom'] : '';
$datedebsejour = $isLoggedIn ? $_SESSION['datedebsejour'] : '';
$datefinsejour = $isLoggedIn ? $_SESSION['datefinsejour'] : '';

// Définit le statut de session (connecté ou non)
$sessionStatus = $isLoggedIn ? 'true' : 'false';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bcryptjs@2.4.3/dist/bcrypt.min.js"></script>
    <script>
        let userId = <?php echo json_encode($user); ?>;
        let userRole = <?php echo json_encode($user_role); ?>;
        let datedebsejour = <?php echo json_encode($datedebsejour); ?>;
        let datefinsejour = <?php echo json_encode($datefinsejour); ?>;

        function getUserId() {
            return userId;
        }
    </script>
    <script>
        var containers = {}
        var currentContent;

        // si tableName est défini alors highlight option menu (filtrer)
        function setCurrent(tableName) {
            const container = containers[tableName]
            if (currentContent) {
                currentContent.classList.remove("current");
                currentContent.classList.add("not-current");
            }
            currentContent = container;
            currentContent.classList.remove("not-current");
            currentContent.classList.add("current");
        }

        function genTableContent(tableName, titre, filtre) {
            const container = document.getElementById(tableName);
            createTableContainer(getUserId(), tableName, container, titre, filtre);
            containers[tableName] = container;
        }

        function show(tableName, titre, filtre) {
            genTableContent(tableName, titre, filtre);
            setCurrent(tableName)
        }
    </script>

    <script>
        function checkSession() {
            const sessionStatus = <?php echo json_encode($sessionStatus); ?>;
            logged(sessionStatus === 'true', {
                "nom": `<?= htmlspecialchars($user_nom) ?>`,
                "prenom": `<?= htmlspecialchars($user_prenom) ?>`,
                "role": `<?= htmlspecialchars($user_role) ?>`,
                "datedebsejour": `<?= htmlspecialchars($datedebsejour ?? '') ?>`,
                "datefinsejour": `<?= htmlspecialchars($datefinsejour ?? '') ?>`
            })
        }

        document.addEventListener("DOMContentLoaded", function() {
            checkSession();
        });

        function logged(isLogged, result, err) {
            const errMsg = document.getElementById("login-message");
            const loggedUser = document.getElementById("logged-user");
            const loginForm = document.getElementById("login-form");
            if (err) {
                loginForm.style.display = 'none'
                errMsg.style.display = 'block'
                errMsg.innerText = err;
                loggedUser.style.display = 'none';
                setTimeout(() => {
                    loginForm.style.display = 'flex'
                    errMsg.style.display = 'none'
                }, 2000);
            } else if (isLogged) {
                userRole = result.role;
                let role = userRole == 'AD' ? 'Administrateur' : (userRole == 'EN' ? 'Animateur' : (userRole == 'VA' ? 'Vacancier' : ''))
                loggedUser.innerHTML = `
    <div class="user-info-container">
        <div class="user-info">
            <span class="username" id="user">${result.nom} ${result.prenom}</span> 
            <div class="role"><span>${role}</span>
            <span class="date">(${result.datedebsejour ? result.datedebsejour : ''} - ${result.datefinsejour ? result.datefinsejour : ''})</span></div>
        </div>
        <a href="logout.php" class="logout">Déconnexion</a>
    </div>`;
                loginForm.style.display = 'none'
                loggedUser.style.display = 'block';
                errMsg.style.display = 'none'
                handleUserRole();
            } else {
                loginForm.style.display = 'flex'
                errMsg.style.display = 'none'
                loggedUser.style.display = 'none';
            }
        }

        function handleLogin(event) {
            event.preventDefault(); // Empêche le rechargement de la page

            const login = document.getElementById("login").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!login || !password) {
                loginErr("❌ Remplissez tous les champs");
                return;
            }

            fetch("login.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        login,
                        password
                    }).toString()
                })
                .then(response => response.json())
                .then(result => {
                    console.log(`result: ${JSON.stringify(result)}`);
                    if (result.success) {
                        /*
                  Forcer un rechargement complet de la page après login;
                        Pourquoi ça arrive ?
Quand tu te connectes via une requête fetch() vers login.php :
  Le serveur initialise la session ($_SESSION['user_id'] = ...) et renvoie une réponse JSON.
  Mais comme ce n’est pas une navigation complète, la page d’accueil (index.php) est chargée 
  juste après en JS, sans que PHP n’ait encore récupéré la session via cookie.
  Du coup, au premier chargement, $_SESSION semble vide. Et donc userId aussi.
                        */
                        window.location.href = 'index.php';
                        logged(true, result)
                    } else {
                        logged(false, '', `❌ ${result.message}`);
                    }
                })
                .catch(error => {
                    logged(false, '', `Erreur: ${error}`);
                });
        }

        // lors de la mise à jour d'un compte et si le mot de passe est changé
        // alors il faut confirmer le mot de passer 

        function closePopup() {
            document.getElementById("password-confirmation-popup").style.display = "none";
            document.getElementById("error-msg").style.display = "none";
            document.getElementById("confirmation-password").value = "";
        }

        let originalPassword = '';
        let doUpdate;

        function confirmNewPassword(password, update) {
            originalPassword = password;
            doUpdate = update;
            document.getElementById("password-confirmation-popup").style.display = "flex";
        }

        function confirmPassword() {

            let confirmation = document.getElementById("confirmation-password").value.trim();

            const bcrypt = dcodeIO.bcrypt;
            const salt = bcrypt.genSaltSync(10);
            const hash1 = bcrypt.hashSync(originalPassword, salt); // hash mdp
            const hash2 = bcrypt.hashSync(confirmation, salt);

            closePopup();

            if (hash1 === hash2) {
                doUpdate(hash1);
            } else {
                doUpdate(hash1, "le password n'est pas confirmé");
            }
        }
    </script>
    <script>
        let codeAnimation;
        let codeActivite;
    </script>
    <script src="manage.js"></script>
</head>

<body>

    <!-- Menu Latéral -->
    <div class="sidebar">
        <ul>
            <li id="menu-acceuil"><a class="menu-item selected" href="#" onclick="setCurrent('acceuil')">Acceuil</a>
            </li>
            <li id="menu-ANIMATION"><a class="menu-item" href="#"
                    onclick="show('ANIMATION', 'Animations')">Animations</a>
            </li>
            <li id="menu-ACTIVITE"><a class="menu-item" href="#" onclick="show('ACTIVITE', 'Activités')">Activités</a>
            </li>
            <li id="menu-INSCRIPTION"><a class="menu-item hidden" href="#"
                    onclick="show('INSCRIPTION', 'Inscriptions')">Inscriptions</a></li>
            <li id="menu-TYPEANIM"><a class="menu-item hidden" href="#"
                    onclick="show('TYPEANIM', 'Type Animations')">Type Animations</a></li>
            <li id="menu-COMPTE"><a class="menu-item" href="#" onclick="show('COMPTE', 'Comptes')">Compte</a>
            </li>
        </ul>
    </div>

    <!-- Contenu Principal -->
    <div class="main-container">

        <!-- (Login / Register ou Nom utilisateur) -->
        <div class="header">
            <!-- Affichage du formulaire de connexion si l'utilisateur n'est pas connecté -->
            <section class="login">
                <form class="login-form" id="login-form" onsubmit="handleLogin(event); return false;">
                    <label for="login">User:</label>
                    <input type="text" id="login" name="login" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <button class="login" type="submit">Login</button>

                </form>
                <div class="logged-user" id="logged-user"></div>
                <p id="login-message" class="error-message"></p>
            </section>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <div class="acceuil current" id="acceuil">
                <div>
                    <h1>Bienvenue sur notre plateforme</h1>
                    <p>Explorez nos animations et activités.</p>
                </div>
            </div>
            <div clas="animation not-current" id="ANIMATION"></div>
            <div class="activite not-current" id="ACTIVITE"></div>
            <div class="compte not-current" id="COMPTE"></div>
            <div class="inscription not-current" id="INSCRIPTION"></div>
            <div class="typeanim not-current" id="TYPEANIM"></div>
            <!-- Le popup masqué par défaut -->
            <div id="password-confirmation-popup" class="confirm-password-popup">
                <div class="confirm-password-popup"
                    style="color:coral; background: white; padding: 20px; border-radius: 10px;">
                    <label>Confirmez le mot de passe :</label><br>
                    <input type="password" id="confirmation-password" /><br><br>
                    <button onclick="confirmPassword()">Valider</button>
                    <button onclick="closePopup()">Annuler</button>
                    <p id="error-msg" style="color: red; display: none;">Les mots de passe ne correspondent pas.</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        var container = document.getElementById("acceuil");
        containers["acceuil"] = container;
        setCurrent('acceuil');

        function _handleUserRole() {

            if (userRole === 'AD') { // peut tout voir
                document.getElementById("menu-INSCRIPTION").classList.remove("hidden");
                document.getElementById("menu-COMPTE").classList.remove("hidden");
            } else if (userRole === 'EN') { // peut voir toutes les inscriptions
                document.getElementById("menu-INSCRIPTION").classList.remove("hidden");
                document.getElementById("menu-COMPTE").classList.add("hidden");
            } else if (userRole === 'VA') { // ne peut voir que ces inscriptions
                document.getElementById("menu-INSCRIPTION").classList.remove("hidden");
                document.getElementById("menu-COMPTE").classList.add("hidden");
            } else {
                // les utilisateurs qui ne sont pas connecter peuvent voir les animations et les activités
                document.getElementById("menu-INSCRIPTION").classList.add("hidden");
                document.getElementById("menu-COMPTE").classList.add("hidden");
            }
        }

        function handleUserRole() {
            const menuInscription = document.getElementById("menu-INSCRIPTION");
            const menuCompte = document.getElementById("menu-COMPTE");

            // Tous les rôles connectés peuvent voir "INSCRIPTION"
            const roles = ['AD', 'EN', 'VA'];

            if (roles.includes(userRole)) {
                menuInscription.classList.remove("hidden");
            } else {
                menuInscription.classList.add("hidden");
            }

            // Seul l'admin peut voir "COMPTE"
            if (userRole === 'AD') {
                menuCompte.classList.remove("hidden");
            } else {
                menuCompte.classList.add("hidden");
            }
        }


        document.addEventListener("DOMContentLoaded", function() {
            handleUserRole();
        });

        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                menuItems.forEach(i => i.classList.remove('selected'));

                this.classList.add('selected');
            });
        });
    </script>
</body>

</html>