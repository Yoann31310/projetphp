<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../Classes/Matchs.php';

class MatchDAO {

	// Récupérer tous les matchs triés du plus récent au plus ancien
	public static function recuperer_tout() {
		try {
			$db = Database::getInstance();
			$sql = "SELECT * FROM Matchs ORDER BY Date_heure DESC";
			$resultat = $db->query($sql)->fetchAll();
			
			$matchs = [];
			foreach ($resultat as $ligne) {
				$match = new Matchs();
				$match->set_id_matchs($ligne['Id_Matchs']);
				$match->set_date_heure($ligne['Date_heure']);
				$match->set_nom_equipe_adverse($ligne['nom_equipe_adverse']);
				$match->set_lieu($ligne['lieu']);
				$match->set_adresse($ligne['adresse']);
				$match->set_resultat($ligne['resultat']);
				$matchs[] = $match;
			}
			return $matchs;
		} catch (PDOException $e) {
			die("Erreur lors de la récupération des matchs : " . $e->getMessage());
		}
	}

	// Trouver un match par son identifiant
	public static function trouver_par_id($id) {
		try {
			$db = Database::getInstance();
			$req = $db->prepare("SELECT * FROM Matchs WHERE Id_Matchs = :id");
			$req->execute(['id' => $id]);
			$ligne = $req->fetch();
			
			if ($ligne) {
				$match = new Matchs();
				$match->set_id_matchs($ligne['Id_Matchs']);
				$match->set_date_heure($ligne['Date_heure']);
				$match->set_nom_equipe_adverse($ligne['nom_equipe_adverse']);
				$match->set_lieu($ligne['lieu']);
				$match->set_adresse($ligne['adresse']);
				$match->set_resultat($ligne['resultat']);
				return $match;
			}
			return null;
		} catch (PDOException $e) {
			die("Erreur lors de la recherche du match par ID : " . $e->getMessage());
		}
	}

	// Ajouter un nouveau match
	public static function ajouter(Matchs $match) {
		try {
			$db = Database::getInstance();
			$sql = "INSERT INTO Matchs (Date_heure, nom_equipe_adverse, lieu, adresse) 
					VALUES (:date_h, :adversaire, :lieu, :adresse)";
			$req = $db->prepare($sql);
			return $req->execute([
				'date_h'     => $match->get_date_heure(),
				'adversaire' => $match->get_nom_equipe_adverse(),
				'lieu'       => $match->get_lieu(),
				'adresse'    => $match->get_adresse()
			]);
		} catch (PDOException $e) {
			die("Erreur lors de l'ajout du match : " . $e->getMessage());
		}
	}

	// Modifier les informations d'un match
	public static function modifier($id, Matchs $match) {
		try {
			$db = Database::getInstance();
			$sql = "UPDATE Matchs SET Date_heure = :date_h, nom_equipe_adverse = :adversaire, 
					lieu = :lieu, adresse = :adresse, resultat = :resultat 
					WHERE Id_Matchs = :id";
			$req = $db->prepare($sql);
			return $req->execute([
				'date_h'     => $match->get_date_heure(),
				'adversaire' => $match->get_nom_equipe_adverse(),
				'lieu'       => $match->get_lieu(),
				'adresse'    => $match->get_adresse(),
				'resultat'   => $match->get_resultat(),
				'id'         => $id
			]);
		} catch (PDOException $e) {
			die("Erreur lors de la modification du match : " . $e->getMessage());
		}
	}

	
    // Supprimer définitivement un match
    public static function supprimer($id) {
        try {
            $db = Database::getInstance();
            $sql = "DELETE FROM Matchs WHERE Id_Matchs = :id";
            $req = $db->prepare($sql);
            return $req->execute(['id' => $id]);
        } catch (PDOException $e) {
            die("Erreur lors de la suppression du match : " . $e->getMessage());
        }
    }
}