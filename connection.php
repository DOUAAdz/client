<?php
// Connexion à la base restaurant
$conn1 = new mysqli("localhost", "root", "", "restaurant");

// Connexion à la base restaurant_db
$conn2 = new mysqli("localhost", "root", "", "restaurant_db");

// Vérifier les connexions
if ($conn1->connect_error || $conn2->connect_error) {
    die("Erreur de connexion : " . $conn1->connect_error . " " . $conn2->connect_error);
}

// Exemple de requête sur la base "restaurant"
$sql1 = "SELECT * FROM commandes";  // Pour la table "plats" dans "restaurant"
$result1 = $conn1->query($sql1);

// Exemple de requête sur la base "restaurant_db"
$sql2 = "SELECT * FROM plats";  // Pour la table "clients" dans "restaurant_db"
$result2 = $conn2->query($sql2);
?>
