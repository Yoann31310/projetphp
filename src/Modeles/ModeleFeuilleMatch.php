<?php
require_once 'Database.php';

class ModeleFeuilleMatch {

    // Récupérer les participants d'un match avec leurs infos (nom, prénom)
    public static function recupererParticipants($idMatch) {
        $db = Database::getInstance();
        $sql = "SELECT p.*, j.nom, j.prenom 
                FROM Participer p 
                JOIN Joueurs j ON p.Id_Joueurs = j.Id_Joueurs 
                WHERE p.Id_Matchs = :id";
        $req = $db->prepare($sql);
        $req->execute(['id' => $idMatch]);
        return $req->fetchAll();
    }

    // Ajouter un joueur sur la feuille
    public static function ajouterParticipant($idMatch, $idJoueur, $role, $poste) {
        $db = Database::getInstance();
        $sql = "INSERT INTO Participer (Id_Matchs, Id_Joueurs, feuille_match, nom_poste, est_Capitaine) 
                VALUES (:idM, :idJ, :role, :poste, 0)";
        $req = $db->prepare($sql);
        return $req->execute([
            'idM' => $idMatch,
            'idJ' => $idJoueur,
            'role' => $role,
            'poste' => $poste
        ]);
    }

    // Vider la feuille avant de la réenregistrer
    public static function viderFeuille($idMatch) {
        $db = Database::getInstance();
        $req = $db->prepare("DELETE FROM Participer WHERE Id_Matchs = :id");
        return $req->execute(['id' => $idMatch]);
    }
    
}