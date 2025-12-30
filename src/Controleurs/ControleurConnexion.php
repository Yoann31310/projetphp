<?php
session_start();

// Importation du modèle de l'entraîneur
require_once '../Modeles/ModeleEntraineur.php';

// On vérifie que le formulaire a bien été envoyé via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération des données saisies dans la Vue
    $identifiantSaisi = $_POST['identifiant'];
    $motDePasseSaisi = $_POST['mdp'];

    // On regarde si l'entraîneur existe dans la base de données
    $donneesEntraineur = ModeleEntraineur::verifierIdentifiant($identifiantSaisi);

    // On vérifie si l'entraîneur existe et on compare les mots de passes
    if ($donneesEntraineur && $motDePasseSaisi === $donneesEntraineur['mdp']) {
        
        // On stocke les informations importantes dans la session
        $_SESSION['id_entraineur'] = $donneesEntraineur['Id_Entraineur'];
        $_SESSION['nom_entraineur'] = $donneesEntraineur['nom'];
        $_SESSION['prenom_entraineur'] = $donneesEntraineur['prenom'];

        // Redirection vers la page d'accueil
        header('Location: ../Vues/PageAccueil.php');
        exit(); 
        
    } else {
        // Sinon : on renvoit à la vue une erreur
        $_SESSION['erreur'] = "Identifiant ou mot de passe incorrect.";
        header('Location: ../Vues/PageConnexion.php');
        exit();
    }
} else {
    // Si on arrive sur ce fichier, on redirige vers la page de connexion
    header('Location: ../Vues/PageConnexion.php');
    exit();
}