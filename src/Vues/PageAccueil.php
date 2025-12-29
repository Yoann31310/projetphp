<?php 
session_start(); 

// Si la session n'existe pas, on redirige vers la connexion
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: PageConnexion.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion Handball</title>
    <link rel="stylesheet" href="css/styleAccueil.css">
</head>
<body>

<!-- Menu sur la gauche avec bouton déconnexion -->
<nav class="barre-laterale">
        <div class="logo-section">
            <img src="css/imageCoach.png" alt="Logo" width="60">
            <h3>Handball Coach</h3>
        </div>
        
        <ul class="menu-navigation">
            <li><a href="PageAccueil.php" class="actif"             >Tableau de bord</a></li>
            <li><a href="../Controleurs/ControleurJoueur.php"       >Gestion des Joueurs</a></li>
            <li><a href="PageListeMatchs.php"                       >Calendrier des Matchs</a></li>
            <li><a href="PageFeuilleMatch.php"                      >Feuilles de Match</a></li>
            <li><a href="PageStatistiques.php"                      >Statistiques de l'équipe</a></li>
        </ul>
        
        <!-- Bouton déconnexion -->
        <div class="zone-deconnexion">
            <a href="../Controleurs/ControleurDeconnexion.php" class="bouton-deconnexion">Déconnexion</a>
        </div>
    </nav>

    
<!-- le reste -->
    <main class="contenu-principal">
        <header class="entete-page">
            <h1>Bienvenue, <?php echo $_SESSION['prenom_entraineur'] . " " . $_SESSION['nom_entraineur']; ?> !</h1>
            <p>Voici l'état actuel de votre équipe de Handball.</p>
        </header>

        <section class="grille-statistiques">
            <div class="carte-info">
                <h3>Joueurs Actifs</h3>
                <p class="chiffre">15</p>
            </div>
            <div class="carte-info">
                <h3>Prochain Match</h3>
                <p>Contre : <strong>HBC Nantes</strong></p>
                <p>Date : 10/01/2026</p>
            </div>
            <div class="carte-info">
                <h3>Dernier Résultat</h3>
                <p class="resultat-gagne">Victoire</p>
            </div>
        </section>
    </main>

</body>
</html>