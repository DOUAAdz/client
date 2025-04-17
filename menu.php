<?php
session_start(); // Démarre la session
include "connection.php"; // Connexion à la base de données

// Récupération des plats depuis la base de données
$sql = "SELECT * FROM plats";
$result = $conn1->query($sql);

// Calculer le nombre d'articles dans le panier
$panier_count = isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu du Restaurant</title>
    <link rel="stylesheet" href="style.css"> <!-- Fichier CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
</head>
<body>
    <div class="container">
        <header>
            <div class="panier">
                <!-- Icône panier avec badge -->
                <a href="panier.php">
                    <i class="fas fa-shopping-cart"></i> <!-- Icône de panier -->
                    <?php if ($panier_count > 0) { ?>
                        <span class="badge"><?php echo $panier_count; ?></span> <!-- Badge avec le nombre d'articles -->
                    <?php } ?>
                </a>
            </div>
        </header>

        <h2>Menu des Plats</h2>

        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<p class='success-msg'>Plat ajouté au panier !</p>";
        }
        ?>

        <div class="plats">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="plat">
                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                    <h3><?php echo htmlspecialchars($row['nom']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Prix:</strong> <?php echo htmlspecialchars($row['prix']); ?> DA</p>
                    <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($row['categorie']); ?></p>

                    <!-- Formulaire pour commander un plat -->
                    <form action="commander.php" method="post">
                        <input type="hidden" name="plat_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="commande-btn">Commander</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

<?php
$conn1->close(); // Fermeture de la connexion
?>
