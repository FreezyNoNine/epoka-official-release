<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "epoka_missions";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule_salarie = $_POST['matricule_salarie'];
    $startDate = $_POST['startDate'];  // Assurez-vous que le nom du champ correspond à celui dans votre formulaire Android
    $endDate = $_POST['endDate'];
    $communeId = $_POST['communeId'];

    // Vérifiez si les valeurs ne sont pas vides avant de les insérer
    if (!empty($matricule_salarie) && !empty($startDate) && !empty($endDate) && !empty($communeId)) {
        // Utilisation de requête préparée pour éviter les injections SQL
        $stmt = $conn->prepare("INSERT INTO mission (matricule_salarie, date_debut, date_fin, id_commune) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $matricule_salarie, $startDate, $endDate, $communeId);

        if ($stmt->execute()) {
            echo json_encode(array("success" => true, "message" => "La mission a été ajoutée avec succès."));
        } else {
            echo json_encode(array("success" => false, "message" => "Erreur lors de l'ajout de la mission : " . $stmt->error));
        }

        $stmt->close();
    } else {
        echo json_encode(array("success" => false, "message" => "Certains champs sont vides."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Erreur : méthode de requête incorrecte."));
}

$conn->close();
?>
