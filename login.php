<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <div class="container">
        <h2>Se connecter</h2>
        
        <form method="post" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Mot de passe:</label>
            <input type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>

        <!-- Lien pour rediriger vers la page d'inscription -->
        <p>Pas encore de compte ? <a href="register.php">Inscris-toi ici</a></p>
    </div>
</body>
</html>
