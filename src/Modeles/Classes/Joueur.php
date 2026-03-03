<?php

class Joueur {
	public $id_joueurs;
	public $numero_licence;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $taille;
	public $poids;
	public $statut;

	public function __construct($data = null) {
		if ($data !== null) {
			if (isset($data['Id_Joueurs'])) {
				$this->id_joueurs = $data['Id_Joueurs'];
			} else {
				if (isset($data['id_joueurs'])) {
					$this->id_joueurs = $data['id_joueurs'];
				}
			}

			if (isset($data['Numero_licence'])) {
				$this->numero_licence = $data['Numero_licence'];
			} else {
				if (isset($data['numero_licence'])) {
					$this->numero_licence = $data['numero_licence'];
				}
			}

			if (isset($data['nom'])) 									$this->nom = $data['nom'];
			if (isset($data['prenom'])) 								$this->prenom = $data['prenom'];
			if (isset($data['date_naissance'])) 						$this->date_naissance = $data['date_naissance'];
			if (isset($data['taille'])) 								$this->taille = $data['taille'];
			if (isset($data['poids'])) 									$this->poids = $data['poids'];
			if (isset($data['statut'])) 								$this->statut = $data['statut'];
		}
	}

	// ========== GETTERS ==========
	
	public function get_id_joueurs() {  		    return $this->id_joueurs; }
	public function get_numero_licence() {		    return $this->numero_licence; }
	public function get_nom() {         		    return $this->nom;}
	public function get_prenom() {      		    return $this->prenom;}
	public function get_date_naissance() {		    return $this->date_naissance;}
	public function get_taille() {      		    return $this->taille;}
	public function get_poids() {       		    return $this->poids;}
	public function get_statut() {      		    return $this->statut;}

	public function est_actif() {       		    return $this->statut !== 'Supprimé';}
	public function get_nom_complet() { 		    return strtoupper($this->nom) . " " . $this->prenom;}

	public function set_id_joueurs($id) {	    	$this->id_joueurs = $id;}
	public function set_numero_licence($num) {		$this->numero_licence = $num;}
	public function set_nom($nom) {         		$this->nom = $nom;}
	public function set_prenom($prenom) {   		$this->prenom = $prenom;}
	public function set_date_naissance($date) {		$this->date_naissance = $date;}
	public function set_taille($taille) {   		$this->taille = $taille;}
	public function set_poids($poids) {     		$this->poids = $poids;}
	public function set_statut($statut) {           $this->statut = $statut;}

	// -------- APPELS à l'api ---------

	public static function recuperer_actifs() {
		$reponse = appel_api('GET', urlApiGestionJoueur);
		$joueurs = [];
		if (isset($reponse['data'])) {
			foreach ($reponse['data'] as $donnees) {
				$joueurs[] = new Joueur($donnees);
			}
		}
		return $joueurs;
	}

	public static function trouver_par_id($id) {
		$reponse = appel_api('GET', urlApiGestionJoueur . "?id=" . $id);
		if (isset($reponse['data'])) {
			return new Joueur($reponse['data']);
		}
		return null;
	}


	public static function ajouter(Joueur $joueur) {
		$donnees = [
			'numero_licence' => $joueur->get_numero_licence(),
			'nom' => $joueur->get_nom(),
			'prenom' => $joueur->get_prenom(),
			'date_naissance' => $joueur->get_date_naissance(),
			'taille' => $joueur->get_taille(),
			'poids' => $joueur->get_poids(),
			'statut' => $joueur->get_statut()
		];
		$reponse = appel_api('POST', urlApiGestionJoueur, $donnees);
		if (isset($reponse['status_code'])) {
            if ($reponse['status_code'] == 201 || $reponse['status_code'] == 200) {
                return true;
            }
        }
		return false;
	}

	public static function modifier($id, Joueur $joueur) {
		$donnees = [
			'numero_licence' => $joueur->get_numero_licence(),
			'nom' => $joueur->get_nom(),
			'prenom' => $joueur->get_prenom(),
			'date_naissance' => $joueur->get_date_naissance(),
			'taille' => $joueur->get_taille(),
			'poids' => $joueur->get_poids(),
			'statut' => $joueur->get_statut()
		];
		$reponse = appel_api('PUT', urlApiGestionJoueur . "?id=" . $id, $donnees);
		if (isset($reponse['status_code'])) {
            if ($reponse['status_code'] == 200) {
                return true;
            }
        }
		return false;
	}

	public static function supprimer($id) {
		$reponse = appel_api('DELETE', urlApiGestionJoueur . "?id=" . $id);
		if (isset($reponse['status_code'])) {
            if ($reponse['status_code'] == 200) {
                return true;
            }
        }
		return false;
	}
}