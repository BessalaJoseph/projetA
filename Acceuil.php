<?php
include 'config.php'; // Inclure le fichier de configuration de la base de données

$_SESSION['idUtilisateur'] = $idUtilisateur;
// Vérifier si le formulaire de téléchargement a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fichier'])) {
    $idUtilisateur = $_SESSION['idUtilisateur'];
    $file_name = $_FILES['fichier']['name'];
    $file_type = $_FILES['fichier']['type'];
    $file_size = $_FILES['fichier']['size'];
    $file_tmp_name = $_FILES['fichier']['tmp_name'];
    $file_dest = 'tofs/' . $file_name;

    // Déplacer le fichier téléchargé vers le répertoire de destination
    if (move_uploaded_file($file_tmp_name, $file_dest)) {
        // Préparer et exécuter la requête d'insertion dans la base de données
        $req = $conn->prepare("INSERT INTO fichier(idUtilisateur, NomFichier, tailleFichier, typeFichier, chemaccesF) VALUES(?, ?, ?, ?, ?)");
        $req->bind_param("isiss", $idUtilisateur, $file_name, $file_size, $file_type, $file_dest);
        if ($user['idUtilisateur'] !== null) {
            $_SESSION['idUtilisateur'] = $user['idUtilisateur'];
        }
        if ($req->execute()) {
            echo 'Fichier envoyé avec succès';
        } else {
            echo 'Erreur d\'insertion: ' . $req->error;
        }
    } else {
        echo 'Une erreur est survenue lors du chargement';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site d'archivage de fichier numérique</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="acceuil.css">
</head>

<body>
    <?php echo $_SESSION['idUtilisateur']; ?>
    <div class="heade">
        <img src="tofs/logoArchiv.png" alt="logo de l'application">
        <button class="btn">Déconnexion</button>
        <img src="tofs/pngegg.png" alt="IMAGE" class="Anim">
    </div>
    <button id="btnpub" class="btnpub">Publier un nouveu fichier</button>
    <div id="modal" class="modal">
        <div class="modal-content">
            <!-- Bouton de fermeture du formulaire -->
            <span class="close-btn" id="closeModalBtn">&times;</span>
            <!-- Formulaire -->
            <form id="modalForm" method="POST" enctype="multipart/form-data">
                <h2>Entrez les informations de votre fichier</h2><br>
                <input type="text" name="nomFichier" id="nomFichier" placeholder="Nom du fichier">
                <input type="text" name="nomDoss" id="nomDoss" placeholder="nom du dossier qui contiendra">
                <input type="file" name="fichier" id="file" required><br>
                <button type="submit" name="submit">Archiver</button>
            </form>
        </div>
    </div>
    <div class="recherche">
        <form action="" class="cherche">
            <input type="text" placeholder="Que cherchez vous?" name="">
            <button type="submit"><img src="tofs/search.png" alt="ICONE DE RECHERCHE"></button>
        </form>
    </div>
    <footer>
        <p>Tous droit reservé,copy right 2023/2024</p>
    </footer>
    <SCript>
        // Ajoute un écouteur d'événement au bouton pour ouvrir le modal
        document.getElementById('btnpub').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'block'; // Affiche le modal
        });

        // Ajoute un écouteur d'événement au bouton de fermeture pour fermer le modal
        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none'; // Cache le modal
        });

        // Ajoute un écouteur d'événement à la fenêtre pour fermer le modal si l'utilisateur clique en dehors
        window.addEventListener('click', function(event) {
            if (event.target == document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none'; // Cache le modal
            }
        });
    </SCript>
</body>

</html>