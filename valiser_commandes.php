<?php
include "connection.php"; // Inclusion de la connexion

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $commande_id = intval($_POST['commande_id']);

    // Mettre à jour le statut de la commande
    $sql = "UPDATE commandes SET status = 'Validée' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $commande_id);

    if ($stmt->execute()) {
        echo "<script>alert('Commande validée !'); window.location.href='commandes.php';</script>";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
