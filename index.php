<?php
// Démarrer une session (si nécessaire)
session_start();

include 'config.php'; // Inclure le fichier de configuration de la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['idUtilisateur'])) {
    // Rediriger vers la page de connexion
    header("Location: Acceuil.php");
    exit(); // Arrêter l'exécution du script
}
// Vérifier si le formulaire de connexion ou d'inscription a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['envoi'])) {
        // Traitement du formulaire d'inscription
        $name = $conn->real_escape_string($_POST['nomUtil']);
        $email = $conn->real_escape_string($_POST['Admail']);
        $password = $conn->real_escape_string($_POST['motdpasse']);

        // Hacher le mot de passe avant de le stocker dans la base de données
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Préparer la requête SQL pour insérer les données
        $sql = "INSERT INTO utilisateur (nomUtil, Admail, motdpasse) VALUES ('$name', '$email', '$hashed_password')";

        // Exécuter la requête SQL
        if ($conn->query($sql) === TRUE) {
            echo "Nouvel enregistrement créé avec succès";
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['soumettre'])) {
        // Traitement du formulaire de connexion
        $email = $conn->real_escape_string($_POST['Admail']);
        $password = $conn->real_escape_string($_POST['motdpasse']);

        // Préparer la requête SQL pour sélectionner l'utilisateur avec l'email fourni
        $sql = "SELECT * FROM utilisateur WHERE Admail = '$email'";
        $result = $conn->query($sql);

        // Vérifier si un utilisateur avec cet email existe
        if ($result->num_rows > 0) {
            // Récupérer les données de l'utilisateur
            $user = $result->fetch_assoc();

            // Vérifier si le mot de passe fourni correspond au mot de passe haché dans la base de données
            if (password_verify($password, $user['motdpasse'])) {
                // Mot de passe correct, définir les variables de session
                $_SESSION['user_id'] = $user['idUtilisateur'];
                $_SESSION['user_name'] = $user['nomUtil'];

                // Rediriger l'utilisateur vers la page d'accueil ou un tableau de bord
                header("Location: Acceuil.php");
                exit();
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Aucun compte trouvé avec cet email.";
        }
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site d'archivage de fichier numérique</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <img src="tofs/logoArchiv.png" alt="logo de l'application">
        <button id="btnOuverture">Se connecter</button>
    </div>
    <section>
        <div class="sec-contener">
            <div class="form-rwaper">
                <div class="card">
                    <div class="card-header">
                        <div id="forlogin" class="form-header active">Se connecter</div>
                        <div id="formregister" class="form-header">S'inscrire</div>
                    </div>
                    <div class="card-body" id="formcontener">
                        <form method="POST" id="loginform">
                            <input type="email" name="Admail" autocomplete="off" class="form-control" placeholder="votre email">
                            <input type="password" name="motdpasse" autocomplete="off" class="form-control" placeholder="votre mot de passe">
                            <button name="soumettre" class="formbutton">Connexion</button>
                        </form>
                        <form method="POST" id="registerform" class="toggleform" action="">
                            <input type="text" name="nomUtil" class="form-control" placeholder="votre nom utilisateur">
                            <input type="email" name="Admail" class="form-control" placeholder="votre email">
                            <input type="password" name="motdpasse" class="form-control" placeholder="votre mot de passe">
                            <button name="envoi" class="formbutton">Inscription</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="main">
        <p><strong>Bienvenue</strong><br><br>Archivez tous vos fichiers importants en toute sécurité sur notre site.Il
            vous suffit de créer un compte et le tour est joué;vous avez accès à vos fichiers à tout moment sur votre
            espace utilisateur</p>
    </div>
    <footer>
        <p>Tous droit reservé,copy right 2023/2024</p>
    </footer>
    <script>
        const btnOuverture = _(`btnOuverture`);
        const forlogin = _(`forlogin`);
        const loginform = _(`loginform`);
        const formregister = _(`formregister`);
        const registerform = _(`registerform`);
        const formcontener = _(`formcontener`);
        btnOuverture.addEventListener(`click`, showform);

        forlogin.addEventListener(`click`, () => {
            forlogin.classList.add(`active`);
            formregister.classList.remove(`active`);
            if (loginform.classList.contains(`toggleform`)) {
                formcontener.style.transform = `translate(0)`;
                formcontener.style.transition = `transform .5s`;
                registerform.classList.add(`toggleform`);
                loginform.classList.remove(`toggleform`);
            }
        });

        formregister.addEventListener(`click`, () => {
            forlogin.classList.remove(`active`);
            formregister.classList.add(`active`);
            if (registerform.classList.contains(`toggleform`)) {
                formcontener.style.transform = `translate(-100%)`;
                formcontener.style.transition = `transform .5s`;
                registerform.classList.remove(`toggleform`);
                loginform.classList.add(`toggleform`);
            }
        });

        function _(e) {
            return document.getElementById(e);
        }

        function showform() {
            document.querySelector(`.form-rwaper .card`).classList.toggle(`show`);
        }
    </script>
</body>

</html>