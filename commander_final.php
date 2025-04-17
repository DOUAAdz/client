<?php
session_start();
include "connection.php"; // Connexion à la base de données

// Vérification si le panier est vide
if (empty($_SESSION['panier'])) {
    echo "<p class='message'>Votre panier est vide. Vous ne pouvez pas passer de commande.</p>";
    exit();
}

// Vérification si l'adresse a été soumise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adresse'])) {
    $adresse = trim($_POST['adresse']); // Récupérer l'adresse de livraison et supprimer les espaces superflus

    // Vérification que l'adresse n'est pas vide
    if (empty($adresse)) {
        echo "<p class='message'>Veuillez fournir une adresse de livraison valide.</p>";
        exit();
    }

    // Récupérer les plats du panier
    $plats_ids = implode(",", $_SESSION['panier']); // Convertir les IDs du panier en chaîne de caractères
    $sql = "SELECT * FROM plats WHERE id IN ($plats_ids)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Insertion des plats dans la table des commandes
        while ($row = $result->fetch_assoc()) {
            $plat_id = $row['id'];
            $status = "En attente"; // Statut de la commande

            // Préparation de la requête d'insertion avec l'adresse de livraison
            $stmt = $conn->prepare("INSERT INTO commandes (plat_id, statut, adresse) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $plat_id, $status, $adresse);

            if (!$stmt->execute()) {
                echo "<p class='message'>Erreur lors de l'enregistrement de la commande: " . $stmt->error . "</p>";
                $stmt->close();
                exit();
            }
        }

        $stmt->close();

        // Vider le panier après la commande
        unset($_SESSION['panier']);

        // Affichage de la confirmation de commande
        echo "<p class='message success'>Votre commande a été enregistrée avec succès. Livraison à l'adresse : $adresse</p>";

        // Redirection après quelques secondes
        echo '<meta http-equiv="refresh" content="3; url=menu.php">';
    } else {
        echo "<p class='message'>Il y a eu un problème avec les plats dans votre panier. Veuillez réessayer.</p>";
    }
} else {
    // Formulaire pour l'adresse de livraison
    echo '
    <div class="form-container">
        <h2>Adresse de livraison</h2>
        <form method="post" action="">
            <label for="adresse">Adresse de livraison:</label>
            <input type="text" id="adresse" name="adresse" required>
            <button type="submit" class="commande-btn">Confirmer la commande</button>
        </form>
    </div>';
}
?>

<a href="menu.php" class="commande-btn">Retour au menu</a>

<!-- Style intégré directement dans le fichier HTML -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 0 auto;
    }

    h2 {
        color: #444;
        text-align: center;
        margin-top: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    form label {
        font-size: 18px;
        margin-bottom: 8px;
    }

    form input[type="text"] {
        padding: 10px;
        font-size: 16px;
        width: 80%;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    form button {
        background-color: #5cb85c;
        color: #fff;
        font-size: 18px;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 50%;
    }

    form button:hover {
        background-color: #4cae4c;
    }

    .message {
        text-align: center;
        font-size: 18px;
        margin-top: 20px;
    }

    .success {
        color: green;
    }

    .error {
        color: red;
    }

    .commande-btn {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        text-align: center;
        margin-top: 20px;
    }

    .commande-btn:hover {
        background-color: #0056b3;
    }

    .form-container {
        width: 50%;
        margin: 20px auto;
    }
</style>
