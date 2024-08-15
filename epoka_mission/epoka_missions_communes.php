<?php
// Paramètres de connexion à la base de données MySQL
$servername = "localhost";
$dbUsername = "root";
$dbPassword = ""; // Votre mot de passe MySQL
$database = "epoka_missions";

// Créer une connexion à la base de données
$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête SQL pour sélectionner les noms des communes
$sql = "SELECT nom FROM commune";

// Exécuter la requête SQL
$result = $conn->query($sql);

// Tableau pour stocker les noms des communes
$communes = array();

// Vérifier si des résultats ont été retournés
if ($result->num_rows > 0) {
    // Parcourir les résultats et ajouter les noms des communes au tableau
    while ($row = $result->fetch_assoc()) {
        $communes[] = $row['nom'];
    }
} else {
    echo "Aucune commune trouvée.";
}

// Convertir le tableau en format JSON et l'afficher
echo json_encode($communes);

// Fermer la connexion à la base de données
$conn->close();
?>
