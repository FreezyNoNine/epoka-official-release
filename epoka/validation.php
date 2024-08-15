<?php
// Inclure le fichier de connexion à la base de données
include 'db.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur n'est pas connecté, le rediriger vers connexion.php
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

// Vérifier si un formulaire de validation a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['mission_id'])) {
    $action = $_POST['action'];
    $mission_id = $_POST['mission_id'];

    // Mettre à jour la mission selon l'action
    if ($action === 'valider') {
        $sql = "UPDATE mission SET valider = 1 WHERE id_mission = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $mission_id);
        $stmt->execute();
        $stmt->close();
    }

    // Rediriger pour éviter la rééxécution du formulaire
    header("Location: validation.php");
    exit();
}

// Récupérer toutes les missions avec le nom et le prénom du salarié et le nom de la commune associée
$sql = "SELECT m.*, s.nom, s.prenom, c.nom AS nom_commune 
        FROM mission m 
        INNER JOIN salarie s ON m.matricule_salarie = s.matricule
        INNER JOIN commune c ON m.id_commune = c.id";

$result = $conn->query($sql);

if (!$result) {
    die("Erreur dans la requête : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Validation des missions de vos subordonnés</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      border: 1px solid black;
      padding: 8px;
      text-align: left;
    }
    .commune-col {
      width: 20%;
    }
    .validation-col {
      width: 15%;
    }
  </style>
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
    <h1>Validation des missions de vos subordonnés</h1>
    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Date de début</th>
          <th>Date de fin</th>
          <th>Commune</th>
          <th>Validation</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['nom']; ?></td>
            <td><?php echo $row['prenom']; ?></td>
            <td><?php echo $row['date_debut']; ?></td>
            <td><?php echo $row['date_fin']; ?></td>
            <td><?php echo $row['nom_commune']; ?></td>
            <td>
              <?php if ($row['valider'] == 1) { ?>
                Validé
              <?php } else { ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <input type="hidden" name="action" value="valider">
                  <input type="hidden" name="mission_id" value="<?php echo $row['id_mission']; ?>">
                  <button type="submit">Valider</button>
                </form>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </main>
</body>
</html>
