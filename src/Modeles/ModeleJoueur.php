<?php
require_once 'database.php';

class ModeleJoueur {
    
    // Récupérer uniquement les joueurs qui ne sont pas supprimés
    public static function recupererTout() {
        try {
            $db = Database::getInstance();
            // On ajoute la condition WHERE statut != 'Supprimé'
            $requete = $db->query("SELECT * FROM Joueurs WHERE statut != 'Supprimé' ORDER BY nom ASC");
            return $requete->fetchAll();
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
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

    public static function modifier(int $id, array $donnees) {
        try {
            $db = Database::getInstance();
            $requete = $db->prepare("UPDATE Joueurs SET 
                nom = :nom, 
                prenom = :prenom, 
                Numero_licence = :licence, 
                statut = :statut 
                WHERE Id_Joueurs = :id");
            
            $requete->execute([
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'licence' => $donnees['licence'],
                'statut' => $donnees['statut'],
                'id' => $id
            ]);
            return true;
        } catch (PDOException $e) {
            die("Erreur lors de la modification : " . $e->getMessage());
        }
    }

    // Vérifie si un numéro de licence est déjà utilisé par quelqu'un d'autre [cite: 25]
    public static function licenceExisteDeja(int $licence, int $idJoueurActuel) {
        try {
            $db = Database::getInstance();
            $requete = $db->prepare("SELECT COUNT(*) FROM Joueurs WHERE Numero_licence = :licence AND Id_Joueurs != :id");
            $requete->execute(['licence' => $licence, 'id' => $idJoueurActuel]);
            return $requete->fetchColumn() > 0;
        } catch (PDOException $e) {
            die("Erreur vérification licence : " . $e->getMessage());
        }
    }

    // On transforme la suppression physique en "changement de statut"
    public static function supprimer(int $id) {
        try {
            $db = Database::getInstance();
            // On ne fait plus un DELETE mais un UPDATE
            $requete = $db->prepare("UPDATE Joueurs SET statut = 'Supprimé' WHERE Id_Joueurs = :id");
            $requete->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            die("Erreur lors de l'archivage : " . $e->getMessage());
        }
    }
}