<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de l'Effectif</title>
    <link rel="stylesheet" href="../Vues/css/styleAccueil.css">
    <link rel="stylesheet" href="../Vues/css/styleTableaux.css">
</head>
<body>

    <nav class="barre-laterale">
        <div class="logo-section">
            <img src="../Vues/css/imageCoach.png" alt="Logo" width="60">
            <h3>Handball Coach</h3>
        </div>
        <ul class="menu-navigation">
            <li><a href="PageAccueil.php">Tableau de bord</a></li>
            <li><a href="../Controleurs/ControleurJoueur.php" class="actif">Gestion des Joueurs</a></li>
        </ul>
    </nav>

    <main class="contenu-principal">
        <h1>Effectif de l'équipe</h1>

        <?php if(isset($_SESSION['erreur'])): ?>
            <div class="message-erreur" style="color: red; background: #fee; padding: 10px; border: 1px solid red; margin-bottom: 20px;">
                <?php 
                    echo $_SESSION['erreur']; 
                    unset($_SESSION['erreur']); // On supprime l'erreur après affichage (Nettoyage)
                ?>
            </div>
        <?php endif; ?>

        <div class="actions-haut">
            <a href="PageAjouterJoueur.php" class="bouton-ajouter">+ Ajouter un joueur</a>
        </div>

        <table class="tableau-donnees">
            <thead>
                <tr>
                    <th>Licence</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($listeJoueurs) && !empty($listeJoueurs)): ?>
                    
                    <?php foreach ($listeJoueurs as $joueur): ?>
                        <tr>
                            <form action="../Controleurs/ControleurJoueur.php" method="POST">
                                
                                <input type="hidden" name="action" value="enregistrer_modif">
                                <input type="hidden" name="id" value="<?php echo $joueur['Id_Joueurs']; ?>">

                                <td>
                                    <?php if ($idEdition == $joueur['Id_Joueurs']): ?>
                                        <input type="text" name="licence" value="<?php echo $joueur['Numero_licence']; ?>" required>
                                    <?php else: echo $joueur['Numero_licence']; endif; ?>
                                </td>
                                
                                <td>
                                    <?php if ($idEdition == $joueur['Id_Joueurs']): ?>
                                        <input type="text" name="nom" value="<?php echo $joueur['nom']; ?>" required>
                                    <?php else: echo strtoupper($joueur['nom']); endif; ?>
                                </td>

                                <td>
                                    <?php if ($idEdition == $joueur['Id_Joueurs']): ?>
                                        <input type="text" name="prenom" value="<?php echo $joueur['prenom']; ?>" required>
                                    <?php else: echo $joueur['prenom']; endif; ?>
                                </td>

                                <td>
                                    <?php if ($idEdition == $joueur['Id_Joueurs']): ?>
                                        <select name="statut">
                                            <option value="Actif" <?php if($joueur['statut'] == 'Actif') echo 'selected'; ?>>Actif</option>
                                            <option value="Blessé" <?php if($joueur['statut'] == 'Blessé') echo 'selected'; ?>>Blessé</option>
                                            <option value="Suspendu" <?php if($joueur['statut'] == 'Suspendu') echo 'selected'; ?>>Suspendu</option>
                                            <option value="Absent" <?php if($joueur['statut'] == 'Absent') echo 'selected'; ?>>Absent</option>
                                        </select>
                                    <?php else: echo $joueur['statut']; endif; ?>
                                </td>

                                <td>
                                    <?php if ($idEdition == $joueur['Id_Joueurs']): ?>
                                        <button type="submit" class="bouton-valider">Valider</button>
                                        <a href="ControleurJoueur.php?action=lister">Annuler</a>
                                    <?php else: ?>
                                        <a href="ControleurJoueur.php?action=lister&id_edition=<?php echo $joueur['Id_Joueurs']; ?>" class="bouton-modifier">Modifier</a>

                                        <form action="../Controleurs/ControleurJoueur.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id" value="<?php echo $joueur['Id_Joueurs']; ?>">
                                            <button type="submit" class="bouton-supprimer" style="background:red; color:white; border:none; padding:5px; cursor:pointer;">
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>