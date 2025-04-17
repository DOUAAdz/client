<?php
session_start();
include "connection.php";

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Récupérer l'ID du client connecté

// Récupérer les commandes du client
$sql = "SELECT c.user_id, c.status, c.date_commande, c.adresse, 
               p.nom AS plat_nom, p.prix 
        FROM commandes c
        JOIN plats p ON c.plat_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.date_commande DESC";

$stmt = $conn1->prepare($sql);
if (!$stmt) {
    die("Erreur lors de la préparation de la requête : " . $conn1->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$commandes = [];

while ($row = $result->fetch_assoc()) {
    $commandes[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Mes Commandes</h2>
        <table border="1">
            <tr>
                <th>ID Commande</th>
                <th>Plat</th>
                <th>Prix</th>
                <th>Date</th>
                <th>Adresse</th>
                <th>Statut</th>
            </tr>
            <?php if (empty($commandes)) { ?>
                <tr>
                    <td colspan="6">Aucune commande trouvée</td>
                </tr>
            <?php } else { ?>
                <?php foreach ($commandes as $commande) { ?>
                    <tr>
                        <td><?php echo $commande['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($commande['plat_nom']); ?></td>
                        <td><?php echo number_format($commande['prix'], 2, '.', ','); ?> DA</td>
                        <td>
                            <?php
                                $date_commande = new DateTime($commande['date_commande']);
                                echo $date_commande->format('d/m/Y H:i');
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($commande['adresse']); ?></td>
                        <td><?php echo htmlspecialchars($commande['status']); ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
</body>
</html>

<?php
$conn1->close();
?>
