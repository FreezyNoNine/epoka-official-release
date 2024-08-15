<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon Site - Connexion</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <nav>
      <ul>
        <?php
        if (isset($_SESSION['username'])) {
            echo "<li><button id='logout-btn'>Déconnexion</button></li>";
            echo "<li><a href='./validation.php'>Validation des missions</a></li>";
            echo "<li><a href='./paiement.php'>Paiement des frais</a></li>";
            echo "<li><a href='./parametrage.php'>Paramétrage</a></li>";
        } else {
            echo "<li id='login-link'><a href='./index.php'>Accueil</a></li>";
        }
        ?>
      </ul>
    </nav>
  </header>
  <main>
    <h1>Identifiez-vous</h1>
    <form action="login.php" method="post">
        <span>Utilisateur : </span><input type="text" name="username">
        <span>Mot de passe : </span><input type="password" name="password">
        <button type="submit" name="login">Valider</button>
    </form>
  </main>
</body>
</html>
