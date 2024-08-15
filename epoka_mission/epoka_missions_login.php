<?php
// Paramètres de connexion à la base de données MySQL
$servername = "localhost";
$username = "root";
$password = "";
$database = "epoka_missions";

// Créer une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}

// Vérifier si le paramètre 'matricule' est présent dans l'URL
if(isset($_GET['matricule'])) {
    $matricule = $_GET['matricule'];

    // Requête SQL pour récupérer les données souhaitées avec filtre par matricule
    $sql_login = "SELECT matricule as username, mot_de_passe as password FROM salarie WHERE matricule = '$matricule'";

    // Exécuter la requête SQL avec le filtre matricule
    $result_login = $conn->query($sql_login);

    // Créer un tableau associatif pour stocker les résultats
    $rows = array();

    // Si des résultats sont renvoyés
    if ($result_login->num_rows > 0) {
        // Parcourir chaque ligne de résultat
        while ($row = $result_login->fetch_assoc()) {
            // Ajouter la ligne au tableau associatif
            $rows[] = $row;
        }
    }

    // Convertir le tableau associatif en format JSON et l'afficher
    echo json_encode($rows);
} else {
    // Aucun matricule n'a été fourni dans l'URL
    echo "Aucun matricule spécifié dans l'URL.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
