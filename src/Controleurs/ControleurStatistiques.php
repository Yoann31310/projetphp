<?php
session_start();

// Rediriger si non connecté
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/DAO/MatchDAO.php';
require_once '../Modeles/DAO/JoueurDAO.php';
require_once '../Modeles/DAO/ParticipationDAO.php';

class ControleurStatistiques {
    
    public function afficher() {
        // Récupérer toutes les statistiques
        $stats_matchs = MatchDAO::obtenir_stats_globales();
        $serie_en_cours = MatchDAO::obtenir_serie_en_cours();
        $meilleure_serie = MatchDAO::obtenir_meilleure_serie();
        $prochain_match = MatchDAO::obtenir_prochain_match();
        $dernier_resultat = MatchDAO::obtenir_dernier_resultat();
        
        $nb_joueurs = JoueurDAO::compter_joueurs_actifs();
        $age_moyen = JoueurDAO::calculer_age_moyen();
        
        $top_joueurs = ParticipationDAO::obtenir_top_participations(5);
        
        // Afficher la vue
        require_once '../Vues/PageStatistiques.php';
    }
}

$controleur = new ControleurStatistiques();
$controleur->afficher();