<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/ModeleJoueur.php';

class ControleurJoueur {

    // Vérification du format texte (avec accent) + taille minimum = 3
    private function verifierFormatTexte($chaine) {
        $longueur = mb_strlen($chaine, 'UTF-8'); 
        if ($longueur < 3) {
            return "trop court (min 3 caractères)";
        }

        $autorises = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ éèàçîïôûùêëÂÀÉÈÊËÎÏÔÛÙÇ-";
        for ($i = 0; $i < $longueur; $i++) {
            $lettre = mb_substr($chaine, $i, 1, 'UTF-8');
            if (strpos($autorises, $lettre) === false) {
                return "caractère interdit : [" . $lettre . "] à la position " . ($i + 1);
            }
        }
        return "OK";
    }

    // Vérifier toutes les données d'un joueur
    // Utilisé pour l'ajout et la modification
    public function validerDonneesJoueur($id, $nom, $prenom, $licence, $taille, $poids) {
        // Vérification des champs vides et en chiffres
        if (empty($nom) || empty($prenom) || !is_numeric($licence) || $licence <= 0) {
            return "La licence doit être un nombre et les noms ne peuvent être vides.";
        }

        // Vérification des minimum (poids et taille)
        if (!is_numeric($taille) || $taille < 80)   return "La taille doit être d'au moins 80 cm.";
        if (!is_numeric($poids) || $poids < 20)     return "Le poids doit être d'au moins 20 kg.";

        // Vérification du format du texte 
        $resNom = $this->verifierFormatTexte($nom);
        if ($resNom !== "OK")                       return "Format NOM incorrect : " . $resNom; 
        
        $resPre = $this->verifierFormatTexte($prenom);
        if ($resPre !== "OK")                       return "Format PRÉNOM incorrect : " . $resPre; 

        // Vérification doublon (Nom et prénom égaux mais avec une autre licence)
        $doublon = ModeleJoueur::trouverParNomPrenom($nom, $prenom, $id);
        if ($doublon) {
            if ($doublon['Numero_licence'] != $licence) {
                return "Cette personne existe déjà (Licence : " . $doublon['Numero_licence'] . ").";
            }
        }

        // Vérification si la licence appartient à quelqu'un d'autre
        if (ModeleJoueur::licenceExisteDeja($licence, $id)) {
            return "Le numéro de licence $licence est déjà attribué à un autre joueur.";
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
        $date_n = $_POST['date_naissance'];
        $taille = $_POST['taille'];
        $poids = $_POST['poids'];
        $statut = $_POST['statut'];

        $erreur = $this->validerDonneesJoueur(0, $nom, $prenom, $licence, $taille, $poids);

        if ($erreur !== "OK") {
            $_SESSION['erreur'] = $erreur;
            header('Location: ControleurJoueur.php');
            exit();
        }

        // Si OK, On ajoute / restaure le joueur (restaurer = si statut = supprimé)
        $joueurExistant = ModeleJoueur::trouverParLicence($licence);
        $donnees = ['nom'=>$nom, 'prenom'=>$prenom, 'licence'=>$licence, 'date_naissance'=>$date_n, 'taille'=>$taille, 'poids'=>$poids, 'statut'=>$statut];

        if ($joueurExistant) {  // On restaure
            ModeleJoueur::modifier($joueurExistant['Id_Joueurs'], $donnees);
        } else {
            ModeleJoueur::ajouter($donnees);
        }

        header('Location: ControleurJoueur.php');
        exit();
    }

    public function supprimer() {
        if (isset($_POST['id'])) { 
            ModeleJoueur::supprimer((int)$_POST['id']);
        }
        header('Location: ControleurJoueur.php');
        exit();
    }
}



$gestionnaire = new ControleurJoueur();
$action = "lister";
if (isset($_POST['action'])) { $action = $_POST['action']; } 
elseif (isset($_GET['action'])) { $action = $_GET['action']; }

switch ($action) {
    case "valider_ajout": $gestionnaire->ajouter(); break;
    case "supprimer": $gestionnaire->supprimer(); break;
    case "enregistrer_modif":
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            // Vérifications
            $erreur = $gestionnaire->validerDonneesJoueur($id, $_POST['nom'], $_POST['prenom'], $_POST['licence'], $_POST['taille'], $_POST['poids']);
            
            if ($erreur !== "OK") {
                $_SESSION['erreur'] = $erreur;
                header("Location: ControleurJoueur.php?id_edition=$id");
            } else {
                $d = ['nom'=>$_POST['nom'], 'prenom'=>$_POST['prenom'], 'licence'=>$_POST['licence'], 'date_naissance'=>$_POST['date_naissance'], 'taille'=>$_POST['taille'], 'poids'=>$_POST['poids'], 'statut'=>$_POST['statut']];
                ModeleJoueur::modifier($id, $d);
                header('Location: ControleurJoueur.php');
            }
            exit();
        }
        break;
    default: $gestionnaire->lister(); break;
}