<?php
session_start();

// Sécurité : Si pas de session, on envoit sur page de Connexion
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/ModeleJoueur.php';

class ControleurJoueur {

    // Méthode pour afficher la liste (Récupère les données et appelle la Vue)
    public function lister() {
        $listeJoueurs = ModeleJoueur::recupererTout(); //
        require_once '../Vues/PageListeJoueurs.php';
    }

    // Méthode pour supprimer (Pas de Get, que Post)
    public function supprimer() {
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            // ModeleJoueur::supprimer($id); // Pour supprimer de la BD
            header('Location: ControleurJoueur.php'); // Actualisation de la liste
            exit();
        }
    }

    // Méthode pour traiter l'ajout
    public function ajouter() {
        echo "Ajout en cours de traitement...";
    }
}

$gestionnaire = new ControleurJoueur();
$action = "lister"; // Action par défaut

if (isset($_POST['action'])) $action = $_POST['action'];


// Le switch appelle la bonne méthode de la classe
switch ($action) {
    case "lister":
        // On récupère l'éventuel ID en cours d'édition (via GET pour l'affichage)
        $idEdition = isset($_GET['id_edition']) ? (int)$_GET['id_edition'] : 0;
        $listeJoueurs = ModeleJoueur::recupererTout();
        require_once '../Vues/PageListeJoueurs.php';
        break;

    case "enregistrer_modif":
        if (isset($_POST['id'])) {
            $donnees = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'licence' => $_POST['licence'],
                'statut' => $_POST['statut']
            ];
            ModeleJoueur::modifier((int)$_POST['id'], $donnees);
            header('Location: ControleurJoueur.php?action=lister');
            exit();
        }
        break;
    case "supprimer":
        $gestionnaire->supprimer();
        break;
    case "ajouter":
        $gestionnaire->ajouter();
        break;
    default:
        $gestionnaire->lister();
        break;
}