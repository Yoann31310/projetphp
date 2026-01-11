<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../Classes/Participation.php';

class ParticipationDAO {

	// Récupérer tous les participants d'un match avec les informations des joueurs
	public static function recuperer_participants($id_match) {
		try {
			$db = Database::getInstance();
			// Jointure entre Participer et Joueurs pour avoir nom/prénom
			$sql = "SELECT p.*, j.nom, j.prenom 
					FROM Participer p 
					JOIN Joueurs j ON p.Id_Joueurs = j.Id_Joueurs 
					WHERE p.Id_Matchs = :id";
			$req = $db->prepare($sql);
			$req->execute(['id' => $id_match]);
			$resultat = $req->fetchAll();
			
			$participants_enrichis = [];
			foreach ($resultat as $ligne) {
				// Créer un objet Participation
				$participation = new Participation();
				$participation->set_id_joueurs($ligne['Id_Joueurs']);
				$participation->set_id_matchs($ligne['Id_Matchs']);
				$participation->set_feuille_match($ligne['feuille_match']);
				$participation->set_evaluation($ligne['evaluation']);
				$participation->set_nom_poste($ligne['nom_poste']);
				$participation->set_est_capitaine($ligne['est_Capitaine']);
				$participation->set_commentaire($ligne['commentaire']);
				
				// Enrichir avec les données du joueur pour l'affichage
				$participants_enrichis[] = [
					'participation'  => $participation,
					'nom'            => $ligne['nom'],
					'prenom'         => $ligne['prenom'],
					'Id_Joueurs'     => $ligne['Id_Joueurs'],
					'feuille_match'  => $ligne['feuille_match'],
					'nom_poste'      => $ligne['nom_poste'],
					'evaluation'     => $ligne['evaluation'],
					'commentaire'    => $ligne['commentaire']
				];
			}
			
			return $participants_enrichis;
		} catch (PDOException $e) {
			die("Erreur lors de la récupération des participants : " . $e->getMessage());
		}
	}

	// Ajouter un participant à la feuille de match
	public static function ajouter_participant($id_match, $id_joueur, $role, $poste) {
		try {
			$db = Database::getInstance();
			$sql = "INSERT INTO Participer (Id_Matchs, Id_Joueurs, feuille_match, nom_poste, est_Capitaine) 
					VALUES (:idM, :idJ, :role, :poste, 0)";
			$req = $db->prepare($sql);
			return $req->execute([
				'idM'   => $id_match,
				'idJ'   => $id_joueur,
				'role'  => $role,
				'poste' => $poste
			]);
		} catch (PDOException $e) {
			die("Erreur lors de l'ajout du participant : " . $e->getMessage());
		}
	}

	// Vider complètement la feuille de match
	public static function vider_feuille($id_match) {
		try {
			$db = Database::getInstance();
			$req = $db->prepare("DELETE FROM Participer WHERE Id_Matchs = :id");
			return $req->execute(['id' => $id_match]);
		} catch (PDOException $e) {
			die("Erreur lors du vidage de la feuille de match : " . $e->getMessage());
		}
	}

	// Enregistrer l'évaluation d'un joueur après le match
	public static function evaluer_joueur($id_match, $id_joueur, $note, $commentaire) {
		try {
			$db = Database::getInstance();
			$sql = "UPDATE Participer SET evaluation = :note, commentaire = :comm 
					WHERE Id_Matchs = :idM AND Id_Joueurs = :idJ";
			$req = $db->prepare($sql);
			return $req->execute([
				'note' => $note,
				'comm' => $commentaire,
				'idM'  => $id_match,
				'idJ'  => $id_joueur
			]);
		} catch (PDOException $e) {
			die("Erreur lors de l'évaluation du joueur : " . $e->getMessage());
		}
	}

	// Joueur avec le plus de participations
	public static function obtenir_top_participations($limite = 5) {
		try {
			$db = Database::getInstance();
			
			$sql = "SELECT j.nom, j.prenom, COUNT(p.Id_Joueurs) as nb_participations
							FROM Joueurs j
							LEFT JOIN Participer p ON j.Id_Joueurs = p.Id_Joueurs
							WHERE j.statut != 'Supprimé'
							GROUP BY j.Id_Joueurs
							ORDER BY nb_participations DESC
							LIMIT :limite";
			
			$req = $db->prepare($sql);
			$req->bindValue(':limite', $limite, PDO::PARAM_INT);
			$req->execute();
			
			return $req->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			die("Erreur top participations : " . $e->getMessage());
		}
	}
}