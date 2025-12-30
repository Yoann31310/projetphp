<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Match</title>
    <link rel="stylesheet" href="../Vues/css/styleAccueil.css">
    <link rel="stylesheet" href="../Vues/css/styleTableaux.css">
</head>
<body>
    <?php $page_active = 'matchs'; require_once 'menu.php'; ?>

    <main class="contenu-principal">
        <h1>Match contre : <?php echo $match['nom_equipe_adverse']; ?></h1>

        <?php if (isset($_SESSION['erreur'])) { ?>
            <div style="color:red; background:#fee; padding:10px; border:1px solid red; margin-bottom:20px;">
                <?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?>
            </div>
        <?php } ?>

        <?php if ($modeMatch == "PRE_MATCH") { ?>
            
            <section style="background:#fff; padding:20px; border:1px solid #ddd; margin-bottom:20px;">
                <h3>1. Infos Logistiques</h3>
                <form action="ControleurMatch.php" method="POST">
                    <input type="hidden" name="action" value="enregistrer_modif">
                    <input type="hidden" name="id" value="<?php echo $match['Id_Matchs']; ?>">
                    
                    <label>Date :</label> <input type="date" name="date" value="<?php echo explode(' ', $match['Date_heure'])[0]; ?>">
                    <label>Heure :</label> <input type="time" name="heure" value="<?php echo explode(' ', $match['Date_heure'])[1]; ?>">
                    <label>Adversaire :</label> <input type="text" name="adversaire" value="<?php echo $match['nom_equipe_adverse']; ?>">
                    <label>Lieu :</label> 
                    <select name="lieu">
                        <option value="Domicile" <?php if($match['lieu']=='Domicile') echo 'selected'; ?>>Domicile</option>
                        <option value="Extérieur" <?php if($match['lieu']=='Extérieur') echo 'selected'; ?>>Extérieur</option>
                    </select>
                    <label>Adresse :</label> <input type="text" name="adresse" value="<?php echo $match['adresse']; ?>">

                    <button type="submit">Enregistrer Infos</button>
                </form>
            </section>

            <section style="background:#fff; padding:20px; border:1px solid #ddd;">
                <h3>2. Feuille de Match</h3>
                <div style="background:#e8f4fd; padding:10px; margin-bottom:10px;">Quotas : 5 à 7 titulaires, Max 7 remplaçants.</div>

                <form action="ControleurMatch.php" method="POST">
                    <input type="hidden" name="action" value="enregistrer_feuille">
                    <input type="hidden" name="id_match" value="<?php echo $match['Id_Matchs']; ?>">
                    
                    <table class="tableau-donnees">
                        <tr><th>Joueur</th><th>Rôle</th><th>Poste</th></tr>
                        <?php foreach ($joueursActifs as $j) { ?>
                            <tr>
                                <input type="hidden" name="tous_les_joueurs[]" value="<?php echo $j['Id_Joueurs']; ?>">
                                <td><?php echo strtoupper($j['nom']) . " " . $j['prenom']; ?></td>
                                <td>
                                    <select name="role_<?php echo $j['Id_Joueurs']; ?>">
                                        <option value="non_partant">-- Ne participe pas --</option>
                                        <option value="titulaire">Titulaire</option>
                                        <option value="remplaçant">Remplaçant</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="poste_<?php echo $j['Id_Joueurs']; ?>">
                                        <option value="Gardien">Gardien</option>
                                        <option value="Pivot">Pivot</option>
                                        <option value="Demi-centre">Demi-centre</option>
                                        <option value="Arrière gauche">Arrière gauche</option>
                                        <option value="Arrière droit">Arrière droit</option>
                                        <option value="Ailier gauche">Ailier gauche</option>
                                        <option value="Ailier droit">Ailier droit</option>
                                    </select>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <button type="submit" style="margin-top:10px; background:#27ae60; color:white; padding:10px;">Valider l'équipe</button>
                </form>
            </section>

        <?php } else { ?>
            <section style="background:#fff; padding:20px; border:1px solid #ddd;">
                <h3>Résultat Final</h3>
                <form action="ControleurMatch.php" method="POST">
                    <input type="hidden" name="action" value="enregistrer_modif">
                    <input type="hidden" name="id" value="<?php echo $match['Id_Matchs']; ?>">
                    <input type="hidden" name="date" value="<?php echo explode(' ', $match['Date_heure'])[0]; ?>">
                    <input type="hidden" name="heure" value="<?php echo explode(' ', $match['Date_heure'])[1]; ?>">
                    <input type="hidden" name="adversaire" value="<?php echo $match['nom_equipe_adverse']; ?>">
                    <input type="hidden" name="lieu" value="<?php echo $match['lieu']; ?>">
                    <input type="hidden" name="adresse" value="<?php echo $match['adresse']; ?>">

                    <select name="resultat">
                        <option value="gagnée" <?php if($match['resultat']=="gagnée") echo "selected"; ?>>Gagnée</option>
                        <option value="perdus" <?php if($match['resultat']=="perdus") echo "selected"; ?>>Perdus</option>
                        <option value="égalité" <?php if($match['resultat']=="égalité") echo "selected"; ?>>Égalité</option>
                    </select>
                    <button type="submit">Valider Score</button>
                </form>
                
                <hr>
                <h3>Évaluation des joueurs</h3>
                <p>La section d'évaluation sera implémentée ici.</p>
            </section>
        <?php } ?>
    </main>
</body>
</html>