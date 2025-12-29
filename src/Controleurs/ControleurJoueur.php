<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/ModeleJoueur.php';

class ControleurJoueur {

    // Affiche la liste et gère le mode édition
    public function lister() {
        // Remplacement du ternaire par un if/else
        if (isset($_GET['id_edition'])) {
            $idEdition = (int)$_GET['id_edition'];
        } else {
            $idEdition = 0;
        }

        $listeJoueurs = ModeleJoueur::recupererTout();
        require_once '../Vues/PageListeJoueurs.php';
    }

    // Gère l'ajout ou la restauration
    public function ajouter() {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $licence = $_POST['licence'];
        $statut = $_POST['statut'];

        // Types et champs vides
        if (empty($nom) || empty($prenom) || !is_numeric($licence) || $licence <= 0) {              // Pour vérifier les chiffres
            $_SESSION['erreur'] = "Données invalides. La licence doit être un nombre.";
            header('Location: ControleurJoueur.php');
            exit();
        } 
        if (!(ctype_alpha($nom)) || strlen($nom) < 3 || !(ctype_alpha($prenom)) || strlen($prenom) < 3) {       // Pour vérifier que c'est des lettres
            $_SESSION['erreur'] = "Données invalides. Le nom et prénom doivent avoir au moins 3 lettres et aucun chiffre.";
            header('Location: ControleurJoueur.php');
            exit();
        }

        // Vérif si le joueur existe déjà (même supprimé)
        $joueurExistant = ModeleJoueur::trouverParLicence($licence);

        if ($joueurExistant) {
            // Si joueur supprimé, on restaure avec le reste des infos
            if ($joueurExistant['statut'] == 'Supprimé') {
                $donnees = ['nom' => $nom, 'prenom' => $prenom, 'licence' => $licence, 'statut' => $statut];
                ModeleJoueur::modifier($joueurExistant['Id_Joueurs'], $donnees);
            } else {
                $_SESSION['erreur'] = "Cette licence est déjà active.";
            }
        } else {
            // Nouveau joueur
            $donnees = ['nom' => $nom, 'prenom' => $prenom, 'licence' => $licence, 'statut' => $statut];
            ModeleJoueur::ajouter($donnees);
        }

        header('Location: ControleurJoueur.php');
        exit();
    }

    public function supprimer() {
        if (isset($_POST['id'])) {
            ModeleJoueur::supprimer((int)$_POST['id']);
        }
        header('Location: ControleurJoueur.php?action=lister');
        exit();
    }
}


$gestionnaire = new ControleurJoueur();
$action = "lister";

if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}

switch ($action) {
    case "lister":
        $gestionnaire->lister();
        break;

    case "valider_ajout":
        $gestionnaire->ajouter();
        break;

    case "enregistrer_modif":
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $licence = $_POST['licence'];
            
            // Vérification si la licence est prise par qqun d'autre
            if (ModeleJoueur::licenceExisteDeja($licence, $id)) {
                $_SESSION['erreur'] = "Licence déjà utilisée.";
                header("Location: ControleurJoueur.php?action=lister&id_edition=$id");
            } else {
                $donnees = ['nom' => $_POST['nom'], 'prenom' => $_POST['prenom'], 'licence' => $licence, 'statut' => $_POST['statut']];
                ModeleJoueur::modifier($id, $donnees);
                header('Location: ControleurJoueur.php?action=lister');
            }
            exit();
        }
        break;

    case "supprimer":
        $gestionnaire->supprimer();
        break;

    default:
        $gestionnaire->lister();
        break;
}