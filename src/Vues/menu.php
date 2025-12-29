<!-- Menu sur la gauche avec bouton déconnexion -->
<nav class="barre-laterale">
        <div class="logo-section">
            <img src="../Vues/css/imageCoach.png" alt="Logo" width="60">
            <h3>Handball Coach</h3>
        </div>
        
        <ul class="menu-navigation">
            <li><a href="../Vues/PageAccueil.php" class="actif"     >Tableau de bord</a></li>
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