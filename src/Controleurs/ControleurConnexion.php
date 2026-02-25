<?php
function appel_api($methode, $url, $donnees = null)
{
	// Initialisation de cURL
	$ch = curl_init($url);

	// Configuration des options de base
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methode);

	// Si on envoie des données, on les transforme en JSON
	if ($donnees) {
		$json_data = json_encode($donnees);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json_data)
		]);
	}

	// Exécution de la requête
	$reponse = curl_exec($ch);
	$info = curl_getinfo($ch);

	// Fermeture de cURL
	curl_close($ch);

	// On décode le JSON reçu pour obtenir un tableau PHP
	return json_decode($reponse, true);
}


session_start();

require_once '../Modeles/Classes/Entraineur.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$identifiant_saisi = $_POST['identifiant'];
	$mot_de_passe_saisi = $_POST['mdp'];

	// 1. On prépare les données pour l'API
	$donnees_login = [
		'identifiant' => $identifiant_saisi,
		'password' => $mot_de_passe_saisi
	];

	// 2. On appelle l'API d'authentification
	$url_api = "http://localhost/projetR401/auth_api/authapi.php";
	$resultat = appel_api('POST', $url_api, $donnees_login);

	// 3. On analyse la réponse de l'API
	if ($resultat && $resultat['status_code'] === 200) {

		// Le jeton JWT est dans $resultat['data']
		$jwt = $resultat['data'];

		// Pour récupérer le nom/prénom sans base de données, on décode le milieu du jeton
		$parties = explode('.', $jwt);
		$payload = json_decode(base64_decode($parties[1]), true); // Le Payload est la 2ème partie

		// On remplit la session avec les infos extraites du jeton
		$_SESSION['id_entraineur'] = $payload['id_entraineur'];
		$_SESSION['nom_entraineur'] = $payload['nom'];
		$_SESSION['prenom_entraineur'] = $payload['prenom'];
		$_SESSION['jwt'] = $jwt; // On garde le jeton pour les futures requêtes

		header('Location: ../Controleurs/ControleurAccueil.php');
		exit();

	} else {
		// Erreur : on utilise le message renvoyé par l'API
		$_SESSION['erreur'] = $resultat['status_message'] ?? "Erreur de connexion à l'API";
		header('Location: ../Vues/PageConnexion.php');
		exit();
	}
}
