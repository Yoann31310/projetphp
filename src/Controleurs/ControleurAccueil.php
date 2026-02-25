<?php
session_start();
require_once 'jwt_utils.php';

$secret = 'random';

// On vérifie si le jeton existe ET s'il est encore valide
if (!isset($_SESSION['jwt']) || !is_jwt_valid($_SESSION['jwt'], $secret)) {
    // Le jeton a expiré ou n'existe pas !
    session_destroy();
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

// Rediriger si non connecté
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/DAO/MatchDAO.php';
require_once '../Modeles/DAO/JoueurDAO.php';

// Récupérer les données nécessaires
$prochain_match = MatchDAO::obtenir_prochain_match();
$dernier_resultat = MatchDAO::obtenir_dernier_resultat();
$nb_joueurs = JoueurDAO::compter_joueurs_actifs();

// Afficher la vue
require_once '../Vues/PageAccueil.php';