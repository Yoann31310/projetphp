<?php
require_once 'Database.php'; 

class ModeleMatch {

    // Récupérer tous les matchs (du plus récent au plus ancien)
    public static function recupererTout() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Matchs ORDER BY Date_heure DESC";
        return $db->query($sql)->fetchAll();
    }

    // Trouver un match par ID
    public static function trouverParId($id) {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM Matchs WHERE Id_Matchs = :id");
        $req->execute(['id' => $id]);
        return $req->fetch();
    }

    // Ajouter un match
    public static function ajouter($d) {
        $db = Database::getInstance();
        $sql = "INSERT INTO Matchs (Date_heure, nom_equipe_adverse, lieu, adresse) 
                VALUES (:date_h, :adversaire, :lieu, :adresse)";
        $req = $db->prepare($sql);
        return $req->execute([
            'date_h'     => $d['date_heure'],
            'adversaire' => $d['adversaire'],
            'lieu'       => $d['lieu'],
            'adresse'    => $d['adresse']
        ]);
    }

    // Modifier un match (infos ou résultat)
    public static function modifier($id, $d) {
        $db = Database::getInstance();
        $sql = "UPDATE Matchs SET 
                Date_heure = :date_h, 
                nom_equipe_adverse = :adversaire, 
                lieu = :lieu, 
                adresse = :adresse, 
                resultat = :resultat 
                WHERE Id_Matchs = :id";
        $req = $db->prepare($sql);
        return $req->execute([
            'date_h'     => $d['date_heure'],
            'adversaire' => $d['adversaire'],
            'lieu'       => $d['lieu'],
            'adresse'    => $d['adresse'],
            'resultat'   => $d['resultat'],
            'id'         => $id
        ]);
    }
}