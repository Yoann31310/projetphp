<?php

class Joueur {

    public int $id = 0;
    public string $nom = "";
    public string $prenom = "";
    public string $num_licence = "";
    public string $date_naissance = "";
    public float $taille = 0;
    public float $poids = 0;
    public string $statut = "actif";   // actif / inactif

    public function __construct(array $data = []) {
        if (isset($data['id'])) {
            $this->id = (int)$data['id'];
        }

        $this->nom = $data['nom'] ?? "";
        $this->prenom = $data['prenom'] ?? "";
        $this->num_licence = $data['num_licence'] ?? "";
        $this->date_naissance = $data['date_naissance'] ?? "";
        $this->taille = isset($data['taille']) ? (float)$data['taille'] : 0;
        $this->poids = isset($data['poids']) ? (float)$data['poids'] : 0;
        $this->statut = $data['statut'] ?? "actif";
    }
}
