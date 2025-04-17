<?php
session_start(); // Démarre la session
include "connection.php"; // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sécuriser l'ID du plat
    $plat_id = intval($_POST['plat_id']);

    // Vérifier si le panier existe déjà dans la session, sinon le créer
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array(); // Créer un panier vide si inexistant
    }

    // Ajouter le plat au panier
    $_SESSION['panier'][] = $plat_id; // Ajouter l'ID du plat dans le panier

    // Redirection vers la page du menu avec un message de succès
    header("Location: menu.php?success=1");
    exit();
}
?>
