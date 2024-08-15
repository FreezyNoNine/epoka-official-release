<?php
include 'db.php'; // Inclure le fichier de connexion à la base de données

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les champs sont vides
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "<script>alert('Veuillez saisir un nom d\\'utilisateur et un mot de passe.');</script>";
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Échapper les caractères spéciaux pour éviter les injections SQL
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT matricule, prenom FROM salarie WHERE prenom='$username' AND mot_de_passe='$password'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Erreur dans la requête : " . $conn->error);
    }

    if ($result->num_rows == 1) {
        // Stocker l'ID de l'utilisateur dans la session
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['matricule'];
        $_SESSION['username'] = $row['prenom'];

        // Redirection vers la page index
        header("Location: index.php");
        exit();
    } else {
        // Affichage d'une alerte JavaScript
        echo "<script>alert('Nom d\\'utilisateur ou mot de passe incorrect.');</script>";
    }
} else {
    // En cas de méthode HTTP incorrecte
    echo "<script>alert('Méthode de requête incorrecte.');</script>";
}

$conn->close(); // Fermer la connexion à la base de données
?>
