<?php
require_once './Modeles/database.php';

try {
    // Appel du Singleton
    $db = Database::getInstance();
    
    if ($db) {
        echo "<h1>Succès !</h1>";
        echo "Connexion établie avec la base de données AlwaysData.<br>";        
    }
} catch (Exception $e) {
    echo "<h1>Échec</h1>";
    echo "Erreur : " . $e->getMessage();
}