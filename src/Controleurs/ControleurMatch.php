<?php
session_start();

if (!isset($_SESSION['id_entraineur'])) {
    header('Location: ../Vues/PageConnexion.php');
    exit();
}

require_once '../Modeles/ModeleMatch.php';
require_once '../Modeles/ModeleJoueur.php';
require_once '../Modeles/ModeleFeuilleMatch.php';

class ControleurMatch {

    // Validation des quotas (5-7 titulaires, 7 remplaçants max)
    private function validerQuotasFeuille($participants) {
        $nbTitulaires = 0;
        $nbRemplacants = 0;

        foreach ($participants as $p) {
            if ($p['role'] == "titulaire") {
                $nbTitulaires++;
            } else if ($p['role'] == "remplaçant") {
                $nbRemplacants++;
            }
        }

        if ($nbTitulaires < 5) return "Nombre de titulaires insuffisant : 5 minimum (actuellement : $nbTitulaires).";
        if ($nbTitulaires > 7) return "Trop de titulaires : 7 maximum (actuellement : $nbTitulaires).";
        if ($nbRemplacants > 7) return "Trop de remplaçants : 7 maximum (actuellement : $nbRemplacants).";

        return "OK";
    }

    public function lister() {
        $listeMatchs = ModeleMatch::recupererTout();
        require_once '../Vues/PageListeMatchs.php';
    }

    public function details() {
        if (isset($_GET['id'])) {
            $idMatch = (int)$_GET['id'];
            $match = ModeleMatch::trouverParId($idMatch);
            $joueursActifs = ModeleJoueur::recupererTout();
            $participants = ModeleFeuilleMatch::recupererParticipants($idMatch);

            // Logique Pré/Post Match
            $dateMatch = strtotime($match['Date_heure']);
            if ($dateMatch < time()) {
                $modeMatch = "POST_MATCH";
            } else {
                $modeMatch = "PRE_MATCH";
            }
            require_once '../Vues/PageDetailsMatch.php';
        }
    }

    public function ajouter() {
        $date_heure = $_POST['date'] . " " . $_POST['heure'] . ":00";
        $donnees = [
            'date_heure' => $date_heure,
            'adversaire' => $_POST['adversaire'],
            'lieu' => $_POST['lieu'],
            'adresse' => $_POST['adresse']
        ];
        ModeleMatch::ajouter($donnees);
        header('Location: ControleurMatch.php');
        exit();
    }

    public function modifier() {
        $id = (int)$_POST['id'];
        $date_heure = $_POST['date'] . " " . $_POST['heure'];
        
        $res = isset($_POST['resultat']) ? $_POST['resultat'] : NULL;

        $donnees = [
            'date_heure' => $date_heure,
            'adversaire' => $_POST['adversaire'],
            'lieu'       => $_POST['lieu'],
            'adresse'    => $_POST['adresse'],
            'resultat'   => $res
        ];
        ModeleMatch::modifier($id, $donnees);
        header("Location: ControleurMatch.php?action=details&id=$id");
        exit();
    }

    public function valider_feuille() {
        $idMatch = (int)$_POST['id_match'];
        $tousLesIds = $_POST['tous_les_joueurs']; 
        
        $listeFinale = array();

        // On filtre ceux qui ne sont pas "non_partant"
        foreach ($tousLesIds as $idJ) {
            $roleChoisi = $_POST['role_' . $idJ];

            if ($roleChoisi != "non_partant") {
                $listeFinale[] = [
                    'id_joueur' => $idJ,
                    'role' => $roleChoisi,
                    'poste' => $_POST['poste_' . $idJ]
                ];
            }
        }

        $erreur = $this->validerQuotasFeuille($listeFinale);
        if ($erreur != "OK") {
            $_SESSION['erreur'] = $erreur;
            header("Location: ControleurMatch.php?action=details&id=$idMatch");
            exit();
        }

        ModeleFeuilleMatch::viderFeuille($idMatch);
        foreach ($listeFinale as $joueur) {
            ModeleFeuilleMatch::ajouterParticipant($idMatch, $joueur['id_joueur'], $joueur['role'], $joueur['poste']);
        }
        
        header("Location: ControleurMatch.php?action=details&id=$idMatch");
        exit();
    }
}

$gestionnaire = new ControleurMatch();
$action = "lister";
if (isset($_GET['action'])) $action = $_GET['action'];
else if (isset($_POST['action'])) $action = $_POST['action'];

switch ($action) {
    case "details": $gestionnaire->details(); break;
    case "valider_ajout": $gestionnaire->ajouter(); break;
    case "enregistrer_modif": $gestionnaire->modifier(); break;
    case "enregistrer_feuille": $gestionnaire->valider_feuille(); break;
    default: $gestionnaire->lister(); break;
}