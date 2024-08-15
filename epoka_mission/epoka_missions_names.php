<?php

// Paramètres de connexion à la base de données MySQL
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "epoka_missions";

// Créer une connexion à la base de données
$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si le matricule a été passé en paramètre GET
if (isset($_GET['matricule'])) {
    // Échapper les caractères spéciaux pour éviter les injections SQL
    $matricule = $conn->real_escape_string($_GET['matricule']);

    // Requête SQL pour sélectionner le nom et le prénom correspondant au matricule
    $sql = "SELECT nom, prenom FROM salarie WHERE matricule = '$matricule'";
    $result = $conn->query($sql);

    // Vérifier si la requête a renvoyé des résultats
    if ($result->num_rows > 0) {
        // Convertir les résultats en format JSON
        $row = $result->fetch_assoc();
        $response = array("nom" => $row["nom"], "prenom" => $row["prenom"]);
        echo json_encode($response);
    } else {
        // Aucun résultat trouvé pour le matricule donné
        echo "Aucun nom et prénom trouvés pour ce matricule";
    }
} else {
    // Matricule non fourni en paramètre GET
    echo "Matricule non fourni";
}

// Fermer la connexion à la base de données
$conn->close();

