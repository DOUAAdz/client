<?php
session_start();
include "connection.php";

// Vérifier que le panier existe et contient des plats
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    echo "<p>Votre panier est vide.</p>";
    echo '<a href="menu.php" class="commande-btn">Retour au menu</a>';
    exit();
}

// Traitement du formulaire de confirmation d'adresse, email et mot de passe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $adresse = $_POST['adresse'];

    // Vérifie si l'utilisateur existe déjà
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // L'utilisateur existe déjà
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $user_id = $user['id'];
        } else {
            echo "<p>Mot de passe incorrect.</p>";
            exit();
        }
    } else {
        // L'utilisateur n'existe pas, on le crée
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO users (email, password) VALUES (?, ?)";
        $insert_stmt = $conn1->prepare($insert_sql);
        $insert_stmt->bind_param("ss", $email, $hashed_password);

        if ($insert_stmt->execute()) {
            $user_id = $insert_stmt->insert_id;
        } else {
            echo "<p>Erreur lors de l'inscription.</p>";
            exit();
        }
    }

    // Insérer la commande pour chaque plat
    $plats_ids = implode(",", array_map('intval', $_SESSION['panier']));
    $sql = "SELECT * FROM plats WHERE id IN ($plats_ids)";
    $result = $conn1->query($sql);

    if ($result->num_rows > 0) {
        $status = 'En attente';
        while ($row = $result->fetch_assoc()) {
            $plat_id = $row['id'];
            $stmt = $conn1->prepare("INSERT INTO commandes (plat_id, user_id, adresse, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $plat_id, $user_id, $adresse, $status);
            if (!$stmt->execute()) {
                echo "Erreur commande: " . $stmt->error;
            }
        }

        $stmt->close();
        unset($_SESSION['panier']); // Vider le panier
        echo "<p>Commande enregistrée avec succès.</p>";
        echo '<a href="menu.php" class="commande-btn">Retour au menu</a>';
    } else {
        echo "<p>Aucun plat trouvé dans le panier.</p>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container">
    <h2>Votre Panier</h2>
    <div class="plats">
        <?php 
        if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
            $plats_ids = implode(",", array_map('intval', $_SESSION['panier']));
            $sql = "SELECT * FROM plats WHERE id IN ($plats_ids)";
            $result = $conn1->query($sql);

            while ($row = $result->fetch_assoc()) { ?>
                <div class="plat">
                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                    <h3><?php echo htmlspecialchars($row['nom']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Prix:</strong> <?php echo htmlspecialchars($row['prix']); ?> DA</p>
                    <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($row['categorie']); ?></p>
                </div>
            <?php }
        } ?>
    </div>

    <!-- Formulaire de confirmation -->
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>

            <label for="adresse">Adresse de livraison :</label>
            <textarea name="adresse" id="adresse" required></textarea>
        </div>
        <button type="submit" class="commande-btn">Confirmer la commande</button>
       
    <a href="login.php" class="commande-btn" style="background-color: #444; margin-top: 10px;">Se connecter</a>


    </form>

    <a href="menu.php" class="commande-btn">Retour au menu</a>
</div>
</body>
</html>

<?php
$conn1->close();
?>
