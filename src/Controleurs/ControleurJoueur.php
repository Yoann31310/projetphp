<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/ModeleJoueur.php';

class ControleurJoueur {

    // Vérification caractère par caractère
    // Accepte désormais les accents (avec IA), espaces et tirets.
    private function verifierFormatTexte($chaine) {
        // 1. Vérification de la taille
        $longueur = mb_strlen($chaine, 'UTF-8'); 
        if ($longueur < 3) {
            return "trop court (minimum 3 caractères)";
        }

        // Liste des caractères autorisés
        $autorises = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ éèàçîïôûùêëÂÀÉÈÊËÎÏÔÛÙÇ-";

        // Boucle de vérification
        for ($i = 0; $i < $longueur; $i++) {
            // mb_substr récupère UNE lettre complète, même avec un accent
            $lettre = mb_substr($chaine, $i, 1, 'UTF-8');

            // strpos cherche si la lettre est dans notre liste autorisée
            // Si strpos renvoie false, c'est que le caractère est interdit
            if (strpos($autorises, $lettre) === false) {
                return "caractère interdit trouvé : [" . $lettre . "] à la position " . ($i + 1);
            }
        }

        return "OK";
    }

    public function lister() {
        if (isset($_GET['id_edition'])) {
            $idEdition = (int)$_GET['id_edition'];
        } else {
            $idEdition = 0;
        }

        $listeJoueurs = ModeleJoueur::recupererTout();
        require_once '../Vues/PageListeJoueurs.php';
    }

    public function ajouter() {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $licence = $_POST['licence'];
        $statut = $_POST['statut'];

        // Verif Chiffres et champs vides
        if (empty($nom) || empty($prenom) || !is_numeric($licence) || $licence <= 0) {
            $_SESSION['erreur'] = "Données invalides. La licence doit être un nombre positif.";
            header('Location: ControleurJoueur.php');
            exit();
        } 

        // Verif Format Nom 
        $testNom = $this->verifierFormatTexte($nom);
        if ($testNom !== "OK") {
            $_SESSION['erreur'] = "Erreur sur le NOM : " . $testNom;
            header('Location: ControleurJoueur.php');
            exit();
        }

        // Verif Format Prénom
        $testPrenom = $this->verifierFormatTexte($prenom);
        if ($testPrenom !== "OK") {
            $_SESSION['erreur'] = "Erreur sur le PRÉNOM : " . $testPrenom;
            header('Location: ControleurJoueur.php');
            exit();
        }

        // Verif doublon licence
        $joueurDoublon = ModeleJoueur::trouverParNomPrenom($nom, $prenom, 0); 
        if ($joueurDoublon) {
            if ($joueurDoublon['Numero_licence'] != $licence) {
                $_SESSION['erreur'] = "Cette personne existe déjà (Licence : " . $joueurDoublon['Numero_licence'] . ").";
                header('Location: ControleurJoueur.php');
                exit();
            }
        }

        // ajout / restauration si statut == supprimé
        $joueurExistant = ModeleJoueur::trouverParLicence($licence);
        if ($joueurExistant) {
            if ($joueurExistant['statut'] == 'Supprimé') {
                $donnees = ['nom' => $nom, 'prenom' => $prenom, 'licence' => $licence, 'statut' => $statut];
                ModeleJoueur::modifier($joueurExistant['Id_Joueurs'], $donnees);
            } else {
                $_SESSION['erreur'] = "Cette licence est déjà active.";
            }
        } else {
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
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $licence = $_POST['licence'];

            // Debug Modif
            $resNom = $gestionnaire->verifierFormatTexte($nom);
            $resPre = $gestionnaire->verifierFormatTexte($prenom);
            
            if ($resNom !== "OK" || $resPre !== "OK") {
                $_SESSION['erreur'] = "Format invalide (Nom: $resNom / Prénom: $resPre)";
                header("Location: ControleurJoueur.php?action=lister&id_edition=$id");
                exit();
            }

            if (ModeleJoueur::licenceExisteDeja($licence, $id)) {
                $_SESSION['erreur'] = "Licence déjà utilisée.";
                header("Location: ControleurJoueur.php?action=lister&id_edition=$id");
            } else {
                $donnees = ['nom' => $nom, 'prenom' => $prenom, 'licence' => $licence, 'statut' => $_POST['statut']];
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