<?php
// Inclusion de la classe Database pour la connexion
require_once 'Database.php'; 

class ModeleJoueur {

    // Récupérer tous les joueurs qui ne sont pas marqués comme 'Supprimé'
    public static function recupererTout() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Joueurs WHERE statut != 'Supprimé' ORDER BY nom ASC";
        return $db->query($sql)->fetchAll();
    }

    // Vérifier si une licence existe déjà en base de données
    // On ajoute un $idExclu pour ne pas se compter soi-même lors d'une modification
    public static function licenceExisteDeja($licence, $idExclu) {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT COUNT(*) FROM Joueurs WHERE Numero_licence = :licence AND Id_Joueurs != :id");
        $req->execute([
            'licence' => $licence, 
            'id' => $idExclu
        ]);
        
        $resultat = $req->fetchColumn();
        
        if ($resultat > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Chercher un joueur par sa licence (même s'il est supprimé)
    public static function trouverParLicence($licence) {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM Joueurs WHERE Numero_licence = :licence");
        $req->execute(['licence' => $licence]);
        return $req->fetch(); 
    }

    // Insérer un nouveau joueur
    public static function ajouter($d) {
        $db = Database::getInstance();
        $req = $db->prepare("INSERT INTO Joueurs (nom, prenom, Numero_licence, statut) VALUES (:nom, :prenom, :licence, :statut)");
        return $req->execute([
            'nom'     => $d['nom'],
            'prenom'  => $d['prenom'],
            'licence' => $d['licence'],
            'statut'  => $d['statut']
        ]);
    }

    // Mettre à jour les données d'un joueur
    public static function modifier($id, $d) {
        $db = Database::getInstance();
        $req = $db->prepare("UPDATE Joueurs SET nom = :nom, prenom = :prenom, Numero_licence = :licence, statut = :statut WHERE Id_Joueurs = :id");
        return $req->execute([
            'nom'     => $d['nom'],
            'prenom'  => $d['prenom'],
            'licence' => $d['licence'],
            'statut'  => $d['statut'],
            'id'      => $id
        ]);
    }

    // Soft delete : on change juste le statut
    public static function supprimer($id) {
        $db = Database::getInstance();
        $req = $db->prepare("UPDATE Joueurs SET statut = 'Supprimé' WHERE Id_Joueurs = :id");
        return $req->execute(['id' => $id]);
    }
}