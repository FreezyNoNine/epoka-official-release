<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Variables pour stocker les valeurs du remboursement au km et de l'indemnité d'hébergement
$remboursement_km = "";
$indemnite_hebergement = "";
$message = "";

// Vérifier si les valeurs ont été soumises et les stocker dans les variables PHP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'valider_parametres') {
  if (!empty($_POST['remboursement_km']) && !empty($_POST['indemnite_hebergement'])) {
    $remboursement_km = $_POST['remboursement_km'];
    $indemnite_hebergement = $_POST['indemnite_hebergement'];

    // Connexion à la base de données
    include 'db.php';
    if ($conn) {
      echo "Connexion à la base de données réussie.<br>";

      // Préparer la requête SQL pour mettre à jour les valeurs dans la table "parametre"
      $sql = "UPDATE parametre SET kilometrage = ?, indemnite = ?";

      // Préparer et exécuter la requête avec gestion des erreurs
      $stmt = $conn->prepare($sql);
      if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error);
      }

      // Liaison des paramètres et exécution de la requête
      $result = $stmt->bind_param("dd", $remboursement_km, $indemnite_hebergement); // Les valeurs sont de type double
      if (!$result) {
        die("Erreur lors de la liaison des paramètres : " . $stmt->error);
      }

      $result = $stmt->execute();
      if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . $stmt->error);
      }

      // Fermeture du statement
      $stmt->close();

      // Rediriger vers la page de paramétrage avec un message de succès
      header("Location: parametrage.php?success=1");
      exit();
    } else {
      die("Échec de la connexion à la base de données.");
    }
  } else {
    $message = "Veuillez remplir tous les champs.";
  }
}

// Vérifier si l'utilisateur n'est pas connecté
if (!isset($_SESSION['username'])) {
  header("Location: connexion.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paramétrage de l'application</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <header>
    <nav>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="validation.php">Validation des missions</a></li>
        <li><a href="paiement.php">Paiement des frais</a></li>
        <li><a href="parametrage.php">Paramétrage</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <h1>Paramétrage de l'application</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <h2>Montant du remboursement au km</h2>
      <div>
        <span>Remboursement au km:</span>
        <input type="number" name="remboursement_km" step="0.01" value="<?php echo $remboursement_km; ?>" required>
      </div>
      <div>
        <span>Indemnité d'hébergement:</span>
        <input type="number" name="indemnite_hebergement" step="0.01" value="<?php echo $indemnite_hebergement; ?>" required>
      </div>

      <input type="hidden" name="action" value="valider_parametres">
      <button type="submit">Valider</button>
    </form>

    <!-- Affichage des valeurs -->
    <?php if (!empty($message)) { ?>
      <h2>Résultat de la validation</h2>
      <p><?php echo $message; ?></p>
    <?php } ?>

    <h2>Distance entre villes</h2>
    <form action="process_parametrage.php" method="post">
      <div>
        <span>De:</span>
        <select name="ville_depart" required>
          <!-- Option de la liste déroulante -->
        </select>
        <span>à:</span>
        <select name="ville_arrivee" required>
          <!-- Option de la liste déroulante -->
        </select>
        <span>Distance en km:</span>
        <input type="number" name="distance_km" step="0.01" required>
      </div>
      <button type="submit">Valider</button>
    </form>

    <h2>Distances entre villes déjà saisies</h2>
    <table>
      <thead>
        <tr>
          <th>De</th>
          <th>À</th>
          <th>Km</th>
        </tr>
      </thead>
      <tbody>
        <!-- Lignes du tableau -->
      </tbody>
    </table>
  </main>
</body>

</html>
