<?php
session_start();

require_once '../Modeles/Classes/Entraineur.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$identifiant_saisi = $_POST['identifiant'];
	$mot_de_passe_saisi = $_POST['mdp'];

	// Chercher l'entraîneur avec les identifiants
	$entraineur = Entraineur::verifier_identifiant($identifiant_saisi);

	// Vérifier que l'entraîneur existe et que le mot de passe est correct
	if ($entraineur && $entraineur->verifier_mot_de_passe($mot_de_passe_saisi)) {
		// Enregistrer les informations de l'entraîneur dans la session
		$_SESSION['id_entraineur'] =        $entraineur->get_id_entraineur();
		$_SESSION['nom_entraineur'] =       $entraineur->get_nom();
		$_SESSION['prenom_entraineur'] =    $entraineur->get_prenom();

		// Rediriger vers la page d'accueil
		header('Location: ../Controleurs/ControleurAccueil.php');
		exit();
		
	} else {
		// Enregistrer le message d'erreur dans la session
		$_SESSION['erreur'] = "Identifiant ou mot de passe incorrect.";
		header('Location: ../Vues/PageConnexion.php');
		exit();
	}
} else {
	// Si ce n'est pas une requête POST, on redirige vers la page de connexion
	header('Location: ../Vues/PageConnexion.php');
	exit();
}