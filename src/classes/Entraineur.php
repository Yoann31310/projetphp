<?php
class Entraineur {
    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password_hashe;

    public function __construct(array $data) {
        $this->id =             $data['id'] ?? 0;
        $this->nom =            $data['nom'];
        $this->prenom =         $data['prenom'];
        $this->email =          $data['email'];
        $this->password_hashe =  $data['password_hashe']; 
    }
}
