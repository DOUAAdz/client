<?php
session_start();
include "connection.php"; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté et est un livreur ou admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "livreur")) {
    header("Location: auth.php");
    exit();
}

// Vérifier si une commande et un nouveau statut sont envoyés
if (isset($_POST['commande_id']) && isset($_POST['nouveau_status'])) {
    $commande_ids = $_POST['commande_id']; // Tableau des ID
    $nouveaux_status = $_POST['nouveau_status']; // Tableau des statuts

    // Vérification des valeurs reçues
    if (count($commande_ids) !== count($nouveaux_status)) {
        echo "Erreur : Données invalides.";
        exit();
    }

    // Vérification de la connexion
    if (!$conn1) {
        die("Erreur de connexion : " . mysqli_connect_error());
    }

    // Préparation de la requête
    $sql = "UPDATE commandes SET status = ? WHERE id = ?";
    $stmt = $conn1->prepare($sql);

    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn1->error);
    }

    // Exécuter la requête pour chaque commande
    for ($i = 0; $i < count($commande_ids); $i++) {
        $commande_id = $commande_ids[$i];
        $nouveau_status = $nouveaux_status[$i];

        $stmt->bind_param("si", $nouveau_status, $commande_id);
        $stmt->execute();
    }

    $stmt->close();
    $conn1->close();

    // Redirection après mise à jour sans quitter la page
    $_SESSION['message'] = "Mise à jour réussie !";
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
} else {
    echo "Données manquantes.";
}
?>
