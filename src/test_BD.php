<?php
// ---------- PARAMÈTRES BDD ----------
$host = "localhost";
$dbname = "projetphpsport";   // mets le nom exact de ta base
$user = "root";
$pass = "";               // vide sur XAMPP par défaut

try {
    // Connexion PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<h2>✔ Connexion réussie !</h2>";

    // ---------- TEST 1 : Afficher les tables ----------
    echo "<h3>Tables disponibles :</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll();
    foreach ($tables as $t) {
        echo "- " . $t[array_keys($t)[0]] . "<br>";
    }

    // ---------- TEST 2 : Exemple SELECT sur la table joueur ----------
    echo "<h3>Test SELECT sur la table joueur :</h3>";

    try {
        $rows = $pdo->query("SELECT * FROM joueur LIMIT 10")->fetchAll();

        if (count($rows) === 0) {
            echo "(La table existe, mais elle est vide.)<br>";
        } else {
            foreach ($rows as $r) {
                echo $r['nom'] . " " . $r['prenom'] . "<br>";
            }
        }
    } catch (PDOException $e) {
        echo "⚠ Impossible d'accéder à la table joueur : " . $e->getMessage();
    }

} catch (PDOException $e) {
    echo "<h2>❌ Échec connexion BDD</h2>";
    echo $e->getMessage();
}
