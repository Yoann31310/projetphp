<?php
require_once 'Database.php'; 

class ModeleJoueur {

    // Récupérer les joueurs actifs
    public static function recupererTout() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Joueurs WHERE statut != 'Supprimé' ORDER BY nom ASC";
        return $db->query($sql)->fetchAll();
    }

    // Vérifier si la licence est déjà prise (en excluant l'ID actuel pour les modifs)
    public static function licenceExisteDeja($licence, $idExclu) {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT COUNT(*) FROM Joueurs WHERE Numero_licence = :licence AND Id_Joueurs != :id");
        $req->execute(['licence' => $licence, 'id' => $idExclu]);
        
        $resultat = $req->fetchColumn();
        if ($resultat > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Vérifier si une licence existe déjà
    public static function trouverParNomPrenom($nom, $prenom, $idExclu) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Joueurs WHERE nom = :nom AND prenom = :prenom AND Id_Joueurs != :id AND statut != 'Supprimé'";
        $req = $db->prepare($sql);
        $req->execute(['nom' => $nom, 'prenom' => $prenom, 'id' => $idExclu]);
        return $req->fetch();
    }

    public static function trouverParLicence($licence) {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM Joueurs WHERE Numero_licence = :licence");
        $req->execute(['licence' => $licence]);
        return $req->fetch(); 
    }

    // Ajouter un joueur
    public static function ajouter($d) {
        $db = Database::getInstance();
        $sql = "INSERT INTO Joueurs (nom, prenom, Numero_licence, date_naissance, taille, poids, statut) 
                VALUES (:nom, :prenom, :licence, :date_n, :taille, :poids, :statut)";
        $req = $db->prepare($sql);
        return $req->execute([
            'nom'     => $d['nom'],
            'prenom'  => $d['prenom'],
            'licence' => $d['licence'],
            'date_n'  => $d['date_naissance'],
            'taille'  => $d['taille'],
            'poids'   => $d['poids'],
            'statut'  => $d['statut']
        ]);
    }

    // Modifie un joueur
    public static function modifier($id, $d) {
        $db = Database::getInstance();
        $sql = "UPDATE Joueurs SET nom = :nom, prenom = :prenom, Numero_licence = :licence, 
                date_naissance = :date_n, taille = :taille, poids = :poids, statut = :statut 
                WHERE Id_Joueurs = :id";
        $req = $db->prepare($sql);
        return $req->execute([
            'nom'     => $d['nom'],
            'prenom'  => $d['prenom'],
            'licence' => $d['licence'],
            'date_n'  => $d['date_naissance'],
            'taille'  => $d['taille'],
            'poids'   => $d['poids'],
            'statut'  => $d['statut'],
            'id'      => $id
        ]);
    }

    public static function supprimer($id) {
        $db = Database::getInstance();
        $req = $db->prepare("UPDATE Joueurs SET statut = 'Supprimé' WHERE Id_Joueurs = :id");
        return $req->execute(['id' => $id]);
    }
}