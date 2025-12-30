<?php
require_once 'database.php';

class ModeleEntraineur {
    public static function verifierIdentifiant(string $identifiant) {
        try {
            $db = Database::getInstance();
            
            // Utilisation d'une requête pour trouver l'identifiant
            $requete = $db->prepare("SELECT * FROM Entraineur WHERE identifiant = :id");
            $requete->execute(['id' => $identifiant]);
            
            // Vérification du nombre de résultats
            $nombre = $requete->rowCount();

            // On vérifie qu'il y a bien un identifiant et pas plusieurs
            if ($nombre === 1) {        return $requete->fetch();
            } elseif ($nombre > 1) {    die("Erreur critique : Plusieurs entraîneurs partagent le même identifiant.");
            } else {                    return false;
            }

        } catch (PDOException $e) {
            die("Erreur SQL lors de la vérification : " . $e->getMessage());
        }
    }
}