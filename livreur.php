<?php
session_start();
include "connection.php"; // Connexion à la base de données

// Vérification de l'accès (seulement admin et livreur)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "livreur")) {
    header("Location: auth.php"); // Redirige vers la page d'authentification
    exit();
}

// Récupérer les commandes avec les détails des plats et adresse
$sql = "SELECT commandes.id, commandes.status, commandes.date_commande, commandes.adresse, 
               plats.nom AS plat_nom, plats.prix 
        FROM commandes 
        JOIN plats ON commandes.plat_id = plats.id 
        ORDER BY commandes.date_commande DESC";
$result = $conn1->query($sql);

// Calculer le total des commandes livrées
$sql_total = "SELECT SUM(plats.prix) AS total FROM commandes 
              JOIN plats ON commandes.plat_id = plats.id 
              WHERE commandes.status = 'Livrée'";
$result_total = $conn1->query($sql_total);
$total_livree = 0;

if ($result_total->num_rows > 0) {
    $row_total = $result_total->fetch_assoc();
    $total_livree = $row_total['total'];
}

// Regrouper les commandes par adresse
$commandes_par_adresse = [];
while ($row = $result->fetch_assoc()) {
    $commandes_par_adresse[$row['adresse']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Import des icônes -->
    <style>
        .total-box {
            background-color: #f4f4f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .total-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .total-amount {
            color: #27ae60; /* Vert pour le total */
            font-size: 1.8rem;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .update-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-btn:hover {
            background-color: #45a049;
        }

        .adresse-box {
            background-color: #eaf2f8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 1.1rem;
            color: #333;
            text-align: center;
            font-weight: bold;
            border: 2px solid #2980b9;
        }

        .commande-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Affichage du total des commandes livrées en haut -->
    <div class="total-box">
        <h3 class="total-text">Total des commandes livrées : <span class="total-amount"><?php echo number_format($total_livree, 2); ?> DA</span></h3>
    </div>

    <div class="container">
        <h2><i class="fa-solid fa-list"></i> Gestion des Commandes</h2>
        
        <form action="modifier_commande.php" method="post">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Plat</th>
                    <th>Prix</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
                <?php 
                // Afficher les commandes par adresse
                foreach ($commandes_par_adresse as $adresse => $commandes) { 
                    // Afficher une section séparée pour chaque adresse
                    echo '<tr><td colspan="5" class="adresse-box">Adresse : ' . htmlspecialchars($adresse) . '</td></tr>';
                    
                    // Afficher chaque commande sous cette adresse
                    foreach ($commandes as $commande) {
                        echo '<tr>';
                        echo '<td>' . $commande['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($commande['plat_nom']) . '</td>';
                        echo '<td>' . $commande['prix'] . ' DA</td>';
                        echo '<td>' . $commande['date_commande'] . '</td>';
                        echo '<td>
                                <input type="hidden" name="commande_id[]" value="' . $commande['id'] . '">
                                <select name="nouveau_status[]">
                                    <option value="En attente" ' . ($commande['status'] == "En attente" ? "selected" : "") . '>En attente</option>
                                    <option value="En cours" ' . ($commande['status'] == "En cours" ? "selected" : "") . '>En cours</option>
                                    <option value="Livrée" ' . ($commande['status'] == "Livrée" ? "selected" : "") . '>Livrée</option>
                                    <option value="Annulée" ' . ($commande['status'] == "Annulée" ? "selected" : "") . '>Annulée</option>
                                </select>
                              </td>';
                        echo '</tr>';
                    }
                }
                ?>
            </table>
            
            <div class="btn-container">
                <button type="submit" class="update-btn">
                    <i class="fa-solid fa-check"></i> Mettre à Jour
                </button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn1->close();
$conn2->close();
?>
