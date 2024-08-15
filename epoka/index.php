<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon Site - Accueil</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <nav>
      <ul>
        <?php
        // Vérifier si le nom d'utilisateur est présent dans la session
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            echo "<li><button id='logout-btn'>Déconnexion</button></li>";
            echo "<li><a href='./validation.php'>Validation des missions</a></li>";
            echo "<li><a href='./paiement.php'>Paiement des frais</a></li>";
            echo "<li><a href='./parametrage.php'>Paramétrage</a></li>";
        } else {
            echo "<li id='login-link'><a href='./connexion.php'>Connexion</a></li>";
        }
        ?>
      </ul>
    </nav>
  </header>
  <main>
    <!-- Utilisation d'un conteneur pour le message de bienvenue -->
    <div id="welcome-message">
      <?php
      // Vérifier si le nom d'utilisateur est présent dans la session
      if (isset($_SESSION['username'])) {
          $username = $_SESSION['username'];
          echo "<h1>Bienvenue $username sur Epoka Missions</h1>";
      } else {
          echo "<h1>Bienvenue sur Epoka Missions</h1>";
      }
      ?>
    </div>
  </main>

  <!-- JavaScript pour gérer la déconnexion -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const logoutBtn = document.getElementById('logout-btn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
          window.location.href = 'logout.php';
        });
      }
    });
  </script>
</body>
</html>
