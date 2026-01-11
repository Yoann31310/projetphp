<?php
session_start();

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