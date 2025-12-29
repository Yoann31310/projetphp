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


    // Méthode pour traiter l'ajout
    public function ajouter() {
        echo "Ajout en cours de traitement...";
    }

    public function supprimer() {
        // On vérifie qu'on a bien reçu l'ID en POST
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id']; // On force le type entier pour la sécurité
            
            // On appelle le modèle pour faire le travail en base de données
            ModeleJoueur::supprimer($id);
            
            // Une fois fini, on recharge la liste des joueurs
            header('Location: ControleurJoueur.php?action=lister');
            exit();
        }
    }
}

$gestionnaire = new ControleurJoueur();
$action = "lister"; // Action par défaut

if (isset($_POST['action'])) $action = $_POST['action'];


// Le switch appelle la bonne méthode de la classe
switch ($action) {
    case "lister":
        // On récupère l'éventuel ID en cours d'édition (GET pour l'affichage)
        $idEdition = isset($_GET['id_edition']) ? (int)$_GET['id_edition'] : 0;
        $listeJoueurs = ModeleJoueur::recupererTout();
        require_once '../Vues/PageListeJoueurs.php';
        break;

    case "enregistrer_modif":
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $licence = (int) $_POST['licence'];
            $statut = $_POST['statut'];

            // Si champs vides
            if (empty($nom) || empty($prenom) || $licence <= 0) {
                $_SESSION['erreur'] = "Tous les champs sont obligatoires et la licence doit être valide.";
                header("Location: ControleurJoueur.php?action=lister&id_edition=$id");
                exit();
            }

            // Si licence existe déjà
            if (ModeleJoueur::licenceExisteDeja($licence, $id)) {
                $_SESSION['erreur'] = "Erreur : Le numéro de licence $licence est déjà attribué à un autre joueur.";
                header("Location: ControleurJoueur.php?action=lister&id_edition=$id");
                exit();
            }

            // Si OK, on modifie
            $donnees = ['nom' => $nom, 'prenom' => $prenom, 'licence' => $licence, 'statut' => $statut];
            ModeleJoueur::modifier($id, $donnees);
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