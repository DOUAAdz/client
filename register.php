<?php
session_start();
include "connection.php";

// Message à afficher
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $adresse = trim($_POST['adresse']);
    $role = "client";

    // Vérifier si l'email existe déjà
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn1->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "❌ Cet email est déjà utilisé.";
    } else {
        // Insertion dans la base
        $insert_sql = "INSERT INTO users (nom, email, password, adresse, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn1->prepare($insert_sql);
        $stmt->bind_param("sssss", $nom, $email, $password, $adresse, $role);

        if ($stmt->execute()) {
            $message = "✅ Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            $message = "❌ Une erreur est survenue. Réessayez.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Client</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Créer un compte client</h2>
    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <label>Adresse :</label><br>
        <input type="text" name="adresse" required><br><br>

        <button type="submit">S'inscrire</button>
    </form>

    <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
</div>
</body>
</html>
