<?php
require_once 'database.php';

class ModeleJoueur {
    
    // Récupère tous les joueurs
    public static function recupererTout() {
        try {
            $db = Database::getInstance();
            $requete = $db->query("SELECT * FROM Joueurs ORDER BY nom ASC");
            return $requete->fetchAll();

        } catch (PDOException $e) {
            die("Erreur lors de la récupération des joueurs : " . $e->getMessage());
        }
    }

    // Récupère les joueurs actifs uniquement (Utile pour feuille matchs)
    public static function recupererActifs() {
        try {
            $db = Database::getInstance();
            $requete = $db->prepare("SELECT * FROM Joueurs WHERE statut = 'actif' ORDER BY nom ASC");
            $requete->execute();
            return $requete->fetchAll();
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des joueurs actifs : " . $e->getMessage());
        }
    }
}